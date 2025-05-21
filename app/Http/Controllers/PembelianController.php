<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Pembelian::with(['supplier', 'users']);
            
            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $query->where(function($q) use ($request) {
                    $q->where('kode_pembelian', 'like', '%' . $request->search . '%')
                      ->orWhereHas('supplier', function($supplier) use ($request) {
                          $supplier->where('nama_supplier', 'like', '%' . $request->search . '%');
                      });
                });
            }
            
            // Filter by status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }
            
            // Filter by date range
            if ($request->has('from_date') && !empty($request->from_date)) {
                $query->whereDate('tanggal_pembelian', '>=', $request->from_date);
            }
            
            if ($request->has('to_date') && !empty($request->to_date)) {
                $query->whereDate('tanggal_pembelian', '<=', $request->to_date);
            }
            
            // Sorting
            if ($request->has('sort') && !empty($request->sort)) {
                $sortDirection = $request->has('direction') ? $request->direction : 'desc';
                $query->orderBy($request->sort, $sortDirection);
            } else {
                $query->orderBy('tanggal_pembelian', 'desc');
            }
            
            $pembelian = $query->paginate(10)->withQueryString();
            
            return view('pembelian.index', compact('pembelian'));
        } catch (Exception $e) {
            Log::error('Error displaying pembelian data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat data pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $suppliers = Supplier::orderBy('nama_supplier')->get();
            $obats = Obat::orderBy('nama_obat')->get();
            
            // Generate kode pembelian (PO-YYYYMMDD-XXXX)
            $today = now()->format('Ymd');
            $lastPembelian = Pembelian::where('kode_pembelian', 'like', "PO-$today-%")->latest()->first();
            
            if ($lastPembelian) {
                $lastNumber = intval(substr($lastPembelian->kode_pembelian, -4));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $kodePembelian = "PO-$today-" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            
            return view('pembelian.create', compact('suppliers', 'obats', 'kodePembelian'));
        } catch (Exception $e) {
            Log::error('Error showing create pembelian form: ' . $e->getMessage());
            return redirect()->route('pembelian.index')->with('error', 'Gagal memuat form pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try {
        DB::beginTransaction();
        
        // Generate kode_pembelian
        $currentYear = date('y');
        $currentMonth = date('m');
        $lastPembelian = Pembelian::whereYear('tanggal_pembelian', date('Y'))
                                ->whereMonth('tanggal_pembelian', date('m'))
                                ->orderBy('id', 'desc')
                                ->first();
        
        $sequenceNumber = $lastPembelian ? 
                         (int) substr($lastPembelian->kode_pembelian, -3) + 1 : 1;
        $kodePembelian = $currentYear . $currentMonth . str_pad($sequenceNumber, 3, '0', STR_PAD_LEFT);

        // Bersihkan format harga sebelum validasi
        $cleanedItems = [];
        foreach ($request->items as $item) {
            $cleanedItems[] = [
                'obat_id' => $item['obat_id'],
                'jumlah' => $item['jumlah'],
                'harga' => is_string($item['harga']) ? 
                          (int) str_replace(['.', ','], '', $item['harga']) : 
                          (int) $item['harga']
            ];
        }

        // Replace request items dengan yang sudah dibersihkan
        $request->merge(['items' => $cleanedItems]);

        // Validate request
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.obat_id' => 'required|exists:obats,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|integer', 
            'jenis_pembayaran' => 'required|in:tunai,kredit',
            'tanggal_jatuh_tempo' => 'required_if:jenis_pembayaran,kredit|nullable|date|after:tanggal_pembelian',
            'keterangan' => 'nullable|string'
        ]);
        
        // Calculate total
        $totalHarga = collect($request->items)->sum(function($item) {
            return $item['jumlah'] * $item['harga'];
        });

        // Create pembelian record
        $pembelian = Pembelian::create([
            'kode_pembelian' => $kodePembelian,
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'supplier_id' => $request->supplier_id,
            'total' => $totalHarga,
            'sisa_pembayaran' => $totalHarga,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'tanggal_jatuh_tempo' => $request->jenis_pembayaran === 'kredit' ? $request->tanggal_jatuh_tempo : null,
            'status' => 'dipesan',
            'user_id' => Auth::id(),
            'keterangan' => $request->keterangan ?? null
        ]);
        
        // Create detail pembelian records
        foreach ($request->items as $item) {
            $pembelian->detailPembelian()->create([
                'obat_id' => $item['obat_id'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga'],
                'subtotal' => $item['jumlah'] * $item['harga']
            ]);
            
        }
        
        DB::commit();
        
        return redirect()->route('pembelian.index')
            ->with('success', 'Pembelian berhasil dibuat dengan kode: ' . $kodePembelian);
            
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error creating pembelian: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal membuat pembelian: ' . $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $pembelian = Pembelian::with(['supplier', 'detailPembelian.obat', 'users', 'penerimaan.user'])
                ->findOrFail($id);
            
            return view('pembelian.show', compact('pembelian'));
        } catch (Exception $e) {
            Log::error('Error showing pembelian: ' . $e->getMessage());
            return redirect()->route('pembelian.index')
                ->with('error', 'Data pembelian tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $pembelian = Pembelian::with(['supplier', 'detailPembelian.obat'])
                ->findOrFail($id);
                
            // Hanya pembelian dengan status pending yang bisa diedit
            if ($pembelian->status !== 'dipesan') {
                return redirect()->route('pembelian.show', $pembelian->id)
                    ->with('error', 'Pembelian dengan status ' . $pembelian->status . ' tidak dapat diedit.');
            }
            
            $suppliers = Supplier::orderBy('nama_supplier')->get();
            $obats = Obat::orderBy('nama_obat')->get();
            
            return view('pembelian.edit', compact('pembelian', 'suppliers', 'obats'));
        } catch (Exception $e) {
            Log::error('Error editing pembelian: ' . $e->getMessage());
            return redirect()->route('pembelian.index')
                ->with('error', 'Data pembelian tidak ditemukan');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    try {
        DB::beginTransaction();
        
        $pembelian = Pembelian::findOrFail($id);
        
        // Hanya pembelian dengan status dipesan yang bisa diupdate
        if ($pembelian->status !== 'dipesan') {
            return redirect()->route('pembelian.show', $pembelian->id)
                ->with('error', 'Pembelian dengan status ' . $pembelian->status . ' tidak dapat diupdate.');
        }
        
        // Bersihkan format harga
        $cleanedItems = [];
        foreach ($request->items as $item) {
            $cleanedItems[] = [
                'obat_id' => $item['obat_id'],
                'jumlah' => $item['jumlah'],
                'harga' => is_string($item['harga']) ? 
                          (int) str_replace(['.', ','], '', $item['harga']) : 
                          (int) $item['harga']
            ];
        }
        
        $request->merge(['items' => $cleanedItems]);

        // Validate request
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.obat_id' => 'required|exists:obats,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga' => 'required|integer',
            'jenis_pembayaran' => 'required|in:tunai,kredit',
            'keterangan' => 'nullable|string'
        ]);
        
        // Calculate total
        $totalHarga = collect($request->items)->sum(function($item) {
            return $item['jumlah'] * $item['harga'];
        });

        // Update pembelian record
        $pembelian->update([
            'tanggal_pembelian' => $request->tanggal_pembelian,
            'supplier_id' => $request->supplier_id,
            'total' => $totalHarga,
            'jenis_pembayaran' => $request->jenis_pembayaran,
            'keterangan' => $request->keterangan ?? null
        ]);
        
        // Hapus detail lama sambil mengurangi stok
        $oldDetails = $pembelian->detailPembelian;
        foreach ($oldDetails as $detail) {
            Obat::where('id', $detail->obat_id)->decrement('stok', $detail->jumlah);
        }
        $pembelian->detailPembelian()->delete();
        
        // Buat detail baru dan update stok
        foreach ($request->items as $item) {
            $pembelian->detailPembelian()->create([
                'obat_id' => $item['obat_id'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga'],
                'subtotal' => $item['jumlah'] * $item['harga']
            ]);
            
        }
        
        DB::commit();
        
        return redirect()->route('pembelian.show', $pembelian->id)
            ->with('success', 'Pembelian berhasil diperbarui.');
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error updating pembelian: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal memperbarui pembelian: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            
            $pembelian = Pembelian::findOrFail($id);
            
            // Pembelian yang sudah diterima tidak bisa dihapus
            if ($pembelian->isDiterima()) {
                return redirect()->route('pembelian.index')
                    ->with('error', 'Pembelian yang sudah diterima tidak dapat dihapus.');
            }
            
            // Delete detail pembelian first
            $pembelian->detailPembelian()->delete();
            
            // Delete pembelian
            $pembelian->delete();
            
            DB::commit();
            
            return redirect()->route('pembelian.index')
                ->with('success', 'Pembelian berhasil dihapus.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pembelian: ' . $e->getMessage());
            return redirect()->route('pembelian.index')
                ->with('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
        }
    }
}