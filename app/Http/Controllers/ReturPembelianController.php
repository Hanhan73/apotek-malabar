<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanPembelian;
use App\Models\ReturPembelian;
use App\Models\ReturPembelianDetail;
use App\Models\PenerimaanPembelianDetail;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturPembelianController extends Controller
{
    public function index()
    {
        $returs = ReturPembelian::with(['penerimaanPembelian.pembelian.supplier', 'items.obat'])
            ->orderBy('tanggal_retur', 'desc')
            ->paginate(10);

        return view('retur_pembelian.index', compact('returs'));
    }

    public function create()
    {
        $penerimaans = PenerimaanPembelian::whereHas('pembelian', function($query) {
                $query->where('status', 'diterima');
            })
            ->with(['pembelian.supplier', 'items.obat'])
            ->get();

        return view('retur_pembelian.create', compact('penerimaans'));
    }

public function store(Request $request)
{
        // dd($request->all());
    DB::beginTransaction();

    try {
        $request->validate([
            'penerimaan_pembelian_id' => 'required|exists:penerimaan_pembelian,id',
            'tanggal_retur' => 'required|date',
            'alasan_retur' => 'required|string',
        ]);

        // Validasi items
        if (!$request->has('items') || !is_array($request->items)) {
            throw ValidationException::withMessages([
                'items' => 'Data obat tidak ditemukan. Silakan pilih minimal satu obat untuk retur.'
            ]);
        }

        $penerimaan = PenerimaanPembelian::with('items.obat')
                        ->findOrFail($request->penerimaan_pembelian_id);

        $hasValidItems = false;
        $totalRetur = 0;

        $retur = ReturPembelian::create([
            'penerimaan_pembelian_id' => $request->penerimaan_pembelian_id,
            'tanggal_retur' => $request->tanggal_retur,
            'alasan_retur' => $request->alasan_retur,
            'user_id' => Auth::id(),
            'total_retur' => 0,
        ]);

        foreach ($request->items as $obatId => $item) {
            if (!isset($item['jumlah']) || (int)$item['jumlah'] <= 0) {
                continue;
            }

            $detailPenerimaan = $penerimaan->detailPenerimaan
                                ->where('obat_id', $obatId)
                                ->first();

            if (!$detailPenerimaan) {
                continue;
            }

            $jumlahRetur = min((int)$item['jumlah'], $detailPenerimaan->jumlah_diterima);

            ReturPembelianDetail::create([
                'retur_pembelian_id' => $retur->id,
                'obat_id' => $obatId,
                'jumlah' => $jumlahRetur,
                'keterangan' => $item['keterangan'] ?? null,
            ]);

            Obat::where('id', $obatId)->decrement('stok', $jumlahRetur);
            $totalRetur += ($jumlahRetur * $detailPenerimaan->harga_satuan);
            $hasValidItems = true;
        }

        if (!$hasValidItems) {
            throw ValidationException::withMessages([
                'items' => 'Minimal satu item harus memiliki jumlah retur lebih dari 0'
            ]);
        }

        $retur->update(['total_retur' => $totalRetur]);

        $penerimaan->update(['status' => 'diretur']);
        DB::commit();

        return redirect()->route('retur-pembelian.show', $retur->id)
               ->with('success', 'Retur pembelian berhasil dicatat');

    } catch (ValidationException $e) {
        DB::rollBack();
        return redirect()->back()
               ->withInput()
               ->withErrors($e->errors());
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
               ->withInput()
               ->with('error', 'Gagal menyimpan retur: ' . $e->getMessage());
    }
}
    public function show($id)
    {
        $retur = ReturPembelian::with([
            'penerimaanPembelian.pembelian.supplier',
            'items.obat',
            'user'
        ])->findOrFail($id);

        return view('retur_pembelian.show', compact('retur'));
    }

public function edit($id)
{
    // Ambil data retur beserta relasi penerimaan, item retur, dan obatnya
    $retur = ReturPembelian::with([
        'penerimaanPembelian.pembelian.detailPembelian.obat',
        'penerimaanPembelian.items.obat',
        'items.obat'
    ])->findOrFail($id);

    // Ambil data penerimaan dari relasi retur
    $penerimaan = $retur->penerimaanPembelian;

    // Kirim dua variabel ke view
    return view('retur_pembelian.edit', compact('retur', 'penerimaan'));
}

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'tanggal_retur' => 'required|date',
                'items' => 'required|array',
                'items.*.id' => 'required|exists:retur_pembelian_detail,id',
                'items.*.jumlah' => 'required|integer|min:1',
                'alasan_retur' => 'required|string',
            ]);

            $retur = ReturPembelian::findOrFail($id);

            // Update retur
            $retur->update([
                'tanggal_retur' => $request->tanggal_retur,
                'alasan_retur' => $request->alasan_retur,
            ]);

            // Hitung total retur baru
            $totalRetur = 0;
            
            // Update detail retur
            foreach ($request->items as $item) {
                $returItem = ReturPembelianDetail::find($item['id']);
                $penerimaanItem = PenerimaanPembelianDetail::where('penerimaan_pembelian_id', $retur->penerimaan_pembelian_id)
                    ->where('obat_id', $returItem->obat_id)
                    ->firstOrFail();

                // Hitung selisih jumlah
                $selisih = $item['jumlah'] - $returItem->jumlah;

                // Update item
                $returItem->update([
                    'jumlah' => $item['jumlah'],
                    'keterangan' => $item['keterangan'] ?? null,
                ]);

                // Update stok obat jika ada perubahan
                if ($selisih != 0) {
                    Obat::where('id', $returItem->obat_id)
                        ->decrement('stok', $selisih);
                }

                $totalRetur += $penerimaanItem->harga_satuan * $item['jumlah'];
            }

            // Update total retur
            $retur->update(['total_retur' => $totalRetur]);

            DB::commit();

            return redirect()->route('retur-pembelian.show', $retur->id)
                ->with('success', 'Retur pembelian berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui retur: ' . $e->getMessage());
        }
    }
}