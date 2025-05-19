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
        $penerimaans = PenerimaanPembelian::whereDoesntHave('pembayaran')
            ->whereHas('pembelian', function($query) {
                $query->whereIn('status', ['diterima', 'diterima_sebagian']);
            })
            ->with(['pembelian.supplier', 'pembelian.detailPembelian.obat'])
            ->get();

        return view('pembayaran_pembelian.create', compact('penerimaans'));
    }

    public function store(Request $request)
{
    // Log the incoming request data
    \Log::info('Incoming request data:', $request->all());

    // Konversi format Rupiah ke integer
    $jumlah_bayar = str_replace(['Rp', '.', ' '], '', $request->jumlah_bayar);
    $request->merge(['jumlah_bayar' => (int)$jumlah_bayar]);

    // Validasi
    $request->validate([
        'penerimaan_pembelian_id' => 'required|exists:penerimaan_pembelian,id',
        'tanggal_bayar' => 'required|date',
        'jumlah_bayar' => 'required|integer|min:1',
        'metode_pembayaran' => 'required|in:tunai,kredit',
        'status' => 'required|in:lunas,belum lunas',
    ]);

    DB::beginTransaction();

    try {
        $penerimaan = PenerimaanPembelian::with('pembelian')->findOrFail($request->penerimaan_pembelian_id);
        
        $totalHarga = $penerimaan->pembelian->total;
        $totalSudahDibayar = $penerimaan->pembayaran()->sum('jumlah_bayar');
        $sisaPembayaran = $totalHarga - $totalSudahDibayar;

        // Log the payment details
        \Log::info('Payment details:', [
            'totalHarga' => $totalHarga,
            'totalSudahDibayar' => $totalSudahDibayar,
            'sisaPembayaran' => $sisaPembayaran,
            'jumlah_bayar' => $request->jumlah_bayar,
        ]);

        if ($request->jumlah_bayar > $sisaPembayaran) {
            return back()->withInput()->with('error', 'Jumlah pembayaran Rp '.number_format($request->jumlah_bayar, 0, ',','.').' melebihi sisa yang harus dibayar Rp '.number_format($sisaPembayaran, 0, ',','.'));
        }

        $pembayaran = PembayaranPembelian::create([
            'penerimaan_pembelian_id' => $request->penerimaan_pembelian_id,
            'tanggal_bayar' => $request->tanggal_bayar,
            'jumlah_bayar' => $request->jumlah_bayar,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => ($request->jumlah_bayar >= $sisaPembayaran) ? 'lunas' : $request->status,
            'user_id' => Auth::id(),
        ]);

        DB::commit();

        return redirect()->route('pembayaran-pembelian.show', $pembayaran->id)
            ->with('success', 'Pembayaran berhasil disimpan');
    } catch (\Exception $e) {
        DB::rollBack();
        // Log the exception message
        \Log::error('Error saving payment:', ['message' => $e->getMessage(), 'request' => $request->all()]);
        return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
    }
}

    public function show($id)
    {
        $pembayaran = PembayaranPembelian::with([
            'penerimaanPembelian.pembelian.supplier',
            'penerimaanPembelian.pembelian.detailPembelian.obat',
            'user'
        ])->findOrFail($id);

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