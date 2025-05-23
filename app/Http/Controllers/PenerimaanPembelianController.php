<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PenerimaanPembelian;
use App\Models\PenerimaanPembelianDetail;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenerimaanPembelianController extends Controller
{
    public function index()
    {
        $penerimaans = PenerimaanPembelian::with(['pembelian.supplier', 'user'])
            ->orderBy('tanggal_penerimaan', 'desc')
            ->paginate(10);

        return view('penerimaan_pembelian.index', compact('penerimaans'));
    }

    public function create()
    {
        $pembelian = Pembelian::where('status', 'dipesan')
            ->whereDoesntHave('penerimaan')
            ->with(['supplier', 'detailPembelian.obat'=> function($query) {
                $query->select('id', 'nama_obat', 'kode_obat', 'harga_beli');
            }])
            ->get()
            ->map(function($pembelian) {
                $pembelian->detailPembelian->each(function($item) {
                    $item->obat->nama_obat = $item->obat->nama_obat . ' (' . $item->obat->kode_obat . ')';
                });
                return $pembelian;
            });

        return view('penerimaan_pembelian.create', compact('pembelian'));
    }

public function store(Request $request)
{
    DB::beginTransaction();

    try {
        // Validate basic fields first
        $request->validate([
            'pembelian_id' => 'required|exists:pembelian,id',
            'tanggal_penerimaan' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        // Check if items exist and is array
        if (!$request->has('items') || !is_array($request->items)) {
            return back()->withInput()->with('error', 'Data obat tidak ditemukan. Silakan pilih pembelian terlebih dahulu.');
        }

        // Flag to check if we have any valid items
        $hasValidItems = false;
        
        // Get the pembelian details first to compare
        $pembelian = Pembelian::with('detailPembelian.obat')->findOrFail($request->pembelian_id);
        
        // Buat penerimaan
        $penerimaan = PenerimaanPembelian::create([
            'pembelian_id' => $request->pembelian_id,
            'tanggal_penerimaan' => $request->tanggal_penerimaan,
            'user_id' => Auth::id(),
            'catatan' => $request->catatan ?? '',
        ]);

        // Process each item separately
        $totalDiterima = 0;
        $totalDipesan = 0;
        
        foreach ($request->items as $key => $item) {
            // Skip if any required field is missing
            if (!isset($item['obat_id']) || !isset($item['jumlah_diterima']) || !isset($item['harga_satuan'])) {
                continue;
            }
            
            // Convert to appropriate types
            $obatId = (int)$item['obat_id'];
            $jumlahDiterima = (int)$item['jumlah_diterima'];
            $hargaSatuan = (float)$item['harga_satuan'];
            
            // Skip items with zero quantity
            if ($jumlahDiterima <= 0) {
                continue;
            }
            
            // Find the detail in the purchase order
            $detailPembelian = null;
            foreach ($pembelian->detailPembelian as $detail) {
                if ($detail->obat && $detail->obat->id == $obatId) {
                    $detailPembelian = $detail;
                    break;
                }
            }
            
            // Skip if no matching detail found
            if (!$detailPembelian) {
                continue;
            }
            
            // Make sure jumlah_diterima doesn't exceed the ordered amount
            $maxJumlah = $detailPembelian->jumlah ?? 0;
            $jumlahDiterima = min($jumlahDiterima, $maxJumlah);
            $totalDipesan += $maxJumlah;
            
            // Create the detail record
            PenerimaanPembelianDetail::create([
                'penerimaan_pembelian_id' => $penerimaan->id,
                'obat_id' => $obatId,
                'jumlah_diterima' => $jumlahDiterima,
                'harga_satuan' => $hargaSatuan,
            ]);
            
            // Update stok obat
            Obat::where('id', $obatId)
                ->increment('stok', $jumlahDiterima);
                
            $totalDiterima += $jumlahDiterima;
            $hasValidItems = true;
        }
        
        // If no valid items were processed, roll back and return error
        if (!$hasValidItems) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Tidak ada item yang valid untuk disimpan. Pastikan ada minimal satu item dengan jumlah diterima lebih dari 0.');
        }

        // Update status pembelian based on whether all items are received
        $status = ($totalDiterima >= $totalDipesan) ? 'diterima' : 'diterima_sebagian';
        $pembelian->update(['status' => $status]);

        DB::commit();

        return redirect()->route('penerimaan-pembelian.show', $penerimaan->id)
            ->with('success', 'Penerimaan pembelian berhasil dicatat');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Gagal menyimpan penerimaan: ' . $e->getMessage());
    }
}

    public function show($id)
    {
        $penerimaan = PenerimaanPembelian::with([
            'pembelian.supplier',
            'user',
            'items.obat'
        ])->findOrFail($id);

        return view('penerimaan_pembelian.show', compact('penerimaan'));
    }

    public function edit($id)
    {
        $penerimaan = PenerimaanPembelian::with(['pembelian.detailPembelian.obat', 'items'])
            ->findOrFail($id);

        return view('penerimaan_pembelian.edit', compact('penerimaan'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'tanggal_penerimaan' => 'required|date',
                'items' => 'required|array',
                'items.*.id' => 'required|exists:penerimaan_pembelian_detail,id',
                'items.*.jumlah_diterima' => 'required|integer|min:1',
                'catatan' => 'nullable|string',
            ]);

            $penerimaan = PenerimaanPembelian::findOrFail($id);

            // Update penerimaan
            $penerimaan->update([
                'tanggal_penerimaan' => $request->tanggal_penerimaan,
                'catatan' => $request->catatan,
            ]);

            // Update detail penerimaan
            foreach ($request->items as $item) {
                $penerimaanItem = PenerimaanPembelianDetail::find($item['id']);
                // Hitung selisih jumlah
                $selisih = $item['jumlah_diterima'] - $penerimaanItem->jumlah_diterima;

                // Update item
                $penerimaanItem->update([
                    'jumlah_diterima' => $item['jumlah_diterima'],
                ]);

                // Update stok obat jika ada perubahan
                if ($selisih != 0) {
                    Obat::where('id', $penerimaanItem->obat_id)
                        ->increment('stok', $selisih);
                }
            }

            DB::commit();

            return redirect()->route('penerimaan-pembelian.show', $penerimaan->id)
                ->with('success', 'Penerimaan pembelian berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui penerimaan: ' . $e->getMessage());
        }
    }
}