<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penjualans = Penjualan::with('user')->latest()->paginate(10);
        return view('penjualan.index', compact('penjualans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $obats = Obat::where('stok', '>', 0)->get();
        $nomorNota = 'INV-' . date('YmdHis');
        
        return view('penjualan.create', compact('obats', 'nomorNota'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_penjualan' => 'required|in:dengan_resep,tanpa_resep',
            'obat_id' => 'required|array',
            'obat_id.*' => 'exists:obats,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        $now = now(); // atau bisa pakai Carbon::now() jika Carbon dipakai
        $currentYear = $now->format('Y');    // contoh: '2025'
        $currentMonth = $now->format('m');   // contoh: '05'
        // Generate a unique kode_supplier
        $lastPenjualan = Penjualan::orderBy('nomor_nota', 'desc')->first();
        $lastNumber = $lastPenjualan ? intval($lastPenjualan->kode_supplier) : 0;
        $newNumber = $lastNumber + 1;
        $kodePenjualan = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        $sequenceNumber = $lastPenjualan ? 
                         (int) substr($lastPenjualan->no_nota, -3) + 1 : 1;
        $kodePenjualan = $currentYear . $currentMonth . str_pad($sequenceNumber, 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            // Buat transaksi penjualan
            $penjualan = Penjualan::create([
                'nomor_nota' => $kodePenjualan,
                'tanggal' => $request->tanggal,
                'jenis_penjualan' => $request->jenis_penjualan,
                'user_id' => Auth::id(),
                'total_harga' => 0, // Akan diupdate setelah menghitung detail
                'status_pembayaran' => 'belum_dibayar',
            ]);

            $totalHarga = 0;

            // Proses detail penjualan
            foreach ($request->obat_id as $key => $obatId) {
                $obat = Obat::findOrFail($obatId);
                $jumlah = $request->jumlah[$key];
                
                // Pastikan stok cukup
                if ($obat->stok < $jumlah) {
                    throw new \Exception("Stok obat {$obat->nama_obat} tidak mencukupi.");
                }
                
                $hargaSatuan = $obat->harga_jual;
                $subtotal = $hargaSatuan * $jumlah;
                $totalHarga += $subtotal;
                
                // Simpan detail penjualan
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'obat_id' => $obatId,
                    'jumlah' => $jumlah,
                    'status' => 'lunas',
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ]);
                
                // Kurangi stok obat
                $obat->stok -= $jumlah;
                $obat->save();
            }
            
            // Update total harga penjualan
            $penjualan->total_harga = $totalHarga;
            $penjualan->save();
            
            DB::commit();
            
            return redirect()->route('penjualan.show', $penjualan->id)
                ->with('success', 'Transaksi penjualan berhasil disimpan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['details.obat', 'user']);
        return view('penjualan.show', compact('penjualan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penjualan $penjualan)
    {
        // Hanya penjualan yang belum dibayar yang bisa diedit
        if ($penjualan->status_pembayaran === 'sudah_dibayar') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Penjualan yang sudah dibayar tidak dapat diedit.');
        }
        
        $penjualan->load('details.obat');
        $obats = Obat::where('stok', '>', 0)->get();
        
        return view('penjualan.edit', compact('penjualan', 'obats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        // Hanya penjualan yang belum dibayar yang bisa diupdate
        if ($penjualan->status_pembayaran === 'sudah_dibayar') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Penjualan yang sudah dibayar tidak dapat diubah.');
        }
        
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_penjualan' => 'required|in:dengan_resep,tanpa_resep',
            'obat_id' => 'required|array',
            'obat_id.*' => 'exists:obats,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Kembalikan stok dari detail lama
            foreach ($penjualan->details as $detail) {
                $obat = $detail->obat;
                $obat->stok += $detail->jumlah;
                $obat->save();
            }
            
            // Hapus detail lama
            $penjualan->details()->delete();
            
            // Update data penjualan utama
            $penjualan->update([
                'tanggal' => $request->tanggal,
                'jenis_penjualan' => $request->jenis_penjualan,
                'total_harga' => 0, // Akan diupdate setelah menghitung detail
            ]);
            
            $totalHarga = 0;
            
            // Proses detail penjualan baru
            foreach ($request->obat_id as $key => $obatId) {
                $obat = Obat::findOrFail($obatId);
                $jumlah = $request->jumlah[$key];
                
                // Pastikan stok cukup
                if ($obat->stok < $jumlah) {
                    throw new \Exception("Stok obat {$obat->nama_obat} tidak mencukupi.");
                }
                
                $hargaSatuan = $obat->harga_jual;
                $subtotal = $hargaSatuan * $jumlah;
                $totalHarga += $subtotal;
                
                // Simpan detail penjualan
                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'obat_id' => $obatId,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $subtotal,
                ]);
                
                // Kurangi stok obat
                $obat->stok -= $jumlah;
                $obat->save();
            }
            
            // Update total harga penjualan
            $penjualan->total_harga = $totalHarga;
            $penjualan->save();
            
            DB::commit();
            
            return redirect()->route('penjualan.show', $penjualan->id)
                ->with('success', 'Transaksi penjualan berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penjualan $penjualan)
    {
        // Hanya penjualan yang belum dibayar yang bisa dihapus
        if ($penjualan->status_pembayaran === 'sudah_dibayar') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Penjualan yang sudah dibayar tidak dapat dihapus.');
        }
        
        DB::beginTransaction();
        try {
            // Kembalikan stok dari detail
            foreach ($penjualan->details as $detail) {
                $obat = $detail->obat;
                $obat->stok += $detail->jumlah;
                $obat->save();
            }
            
            // Hapus penjualan (detail akan ikut terhapus dengan cascade)
            $penjualan->delete();
            
            DB::commit();
            
            return redirect()->route('penjualan.index')
                ->with('success', 'Transaksi penjualan berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}