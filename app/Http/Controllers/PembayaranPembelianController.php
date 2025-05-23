<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanPembelian;
use App\Models\PembayaranPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranPembelianController extends Controller
{
    public function index()
    {
        $pembayarans = PembayaranPembelian::with(['penerimaanPembelian.pembelian.supplier', 'user'])
            ->orderBy('tanggal_bayar', 'desc')
            ->paginate(10);

        return view('pembayaran_pembelian.index', compact('pembayarans'));
    }

public function create()
{
    $penerimaans = PenerimaanPembelian::with([
            'pembelian.supplier',
            'pembayaran' // Eager load pembayaran
        ])
        ->whereHas('pembelian', function($query) {
            $query->where('status_pembayaran', '!=', 'lunas')
                  ->whereIn('status', ['diterima', 'diterima_sebagian']);
        })
        ->get()
        ->filter(function($penerimaan) {
            // Gunakan null coalescing operator untuk handle null pembayaran
            $totalDibayar = optional($penerimaan->pembayaran)->sum('jumlah_bayar') ?? 0;
            return $penerimaan->pembelian->total > $totalDibayar;
        });

    return view('pembayaran_pembelian.create', compact('penerimaans'));
}

    public function store(Request $request)
{
    // Validasi request
    $request->validate([
        'penerimaan_pembelian_id' => 'required|exists:penerimaan_pembelian,id',
        'tanggal_bayar' => 'required|date',
        'jumlah_bayar' => 'required',
        'metode_pembayaran' => 'required|in:tunai,kredit',
        'catatan' => 'nullable|string',
    ]);

    // Konversi format Rupiah ke integer
    $jumlah_bayar = (int) str_replace(['Rp', '.', ' '], '', $request->jumlah_bayar);
    
    DB::beginTransaction();
    try {
        $penerimaan = PenerimaanPembelian::with('pembelian')->findOrFail($request->penerimaan_pembelian_id);
        $pembelian = $penerimaan->pembelian;
        
        // Hitung total yang sudah dibayar sebelumnya
        $totalDibayar = $penerimaan->pembayaran()->sum('jumlah_bayar');
        $sisaHutang = $pembelian->total - $totalDibayar;
        
        // Validasi jumlah pembayaran
        if ($jumlah_bayar <= 0) {
            return back()->withInput()->with('error', 'Jumlah pembayaran harus lebih dari 0');
        }
        
        if ($jumlah_bayar > $sisaHutang) {
            return back()->withInput()->with('error', 'Jumlah pembayaran melebihi sisa hutang');
        }
        
        // Hitung sisa hutang setelah pembayaran ini
        $sisaHutangBaru = $sisaHutang - $jumlah_bayar;
        
        // Tentukan status pembayaran
        $status = ($sisaHutangBaru <= 0) ? 'lunas' : 'belum lunas';
        
        // Simpan pembayaran
        $pembayaran = PembayaranPembelian::create([
            'penerimaan_pembelian_id' => $request->penerimaan_pembelian_id,
            'tanggal_bayar' => $request->tanggal_bayar,
            'jumlah_bayar' => $jumlah_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'sisa_hutang' => $sisaHutangBaru,
            'status' => $status,
            'catatan' => $request->catatan,
            'user_id' => Auth::id(),
        ]);
        
        // Update status pembelian jika sudah lunas
        if ($status === 'lunas') {
            $pembelian->update([
                'status_pembayaran' => 'lunas',
                'sisa_pembayaran' => 0
            ]);
        } else {
            $pembelian->update([
                'sisa_pembayaran' => $sisaHutangBaru
            ]);
        }
        
        DB::commit();
        
        return redirect()->route('pembayaran-pembelian.show', $pembayaran->id)
            ->with('success', 'Pembayaran berhasil disimpan');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput()->with('error', 'Gagal menyimpan pembayaran: ' . $e->getMessage());
    }
}

    public function show($id)
    {
$pembayaran = PembayaranPembelian::with(['penerimaanPembelian.pembayaran' => function($query) {
    $query->orderBy('tanggal_bayar');
}])->find($id);

        return view('pembayaran_pembelian.show', compact('pembayaran'));
    }

    public function edit($id)
    {
        $pembayaran = PembayaranPembelian::with(['penerimaanPembelian.pembelian'])
            ->findOrFail($id);

        return view('pembayaran_pembelian.edit', compact('pembayaran'));
    }

    public function update(Request $request, $id)
    {

        $request->merge([
            'jumlah_bayar' => (int) str_replace('.', '', $request->jumlah_bayar)
        ]);

        $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,kredit',
            'status' => 'required|in:lunas,belum lunas',
        ]);

        DB::beginTransaction();

        try {
            $pembayaran = PembayaranPembelian::findOrFail($id);
            $penerimaan = $pembayaran->penerimaanPembelian;
            
            // Hitung total pembayaran lain (selain yang sedang diupdate)
            $totalPembayaranLain = $penerimaan->pembayaran()
                ->where('id', '!=', $id)
                ->sum('jumlah_bayar');
            
            // Validasi jumlah bayar tidak melebihi total harga
            $totalHarga = $penerimaan->pembelian->total;
            if (($totalPembayaranLain + $request->jumlah_bayar) > $totalHarga) {
                return back()->withInput()->with('error', 'Total pembayaran melebihi total harga pembelian.');
            }

            $pembayaran->update([
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => $request->status,
            ]);

            // Update status pembelian jika sudah lunas
            $totalPembayaran = $penerimaan->pembayaran()->sum('jumlah_bayar');
            if ($totalPembayaran >= $totalHarga) {
                $penerimaan->pembayaran()->update(['status' => 'lunas']);
                $penerimaan->pembelian->update(['status_pembayaran' => 'lunas']);
            }

            DB::commit();

            return redirect()->route('pembayaran-pembelian.show', $pembayaran->id)
                ->with('success', 'Pembayaran pembelian berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui pembayaran: ' . $e->getMessage());
        }
    }
}