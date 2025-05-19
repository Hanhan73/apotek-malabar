<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class ObatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Obat::query();
    
            if ($request->has('search') && !empty($request->search)) {
                $query->where(function($q) use ($request) {
                    $q->where('nama_obat', 'like', '%' . $request->search . '%')
                      ->orWhere('kode_obat', 'like', '%' . $request->search . '%')
                      ->orWhere('jenis_obat', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->has('sort') && !empty($request->sort)) {
                $sortDirection = $request->has('direction') ? $request->direction : 'asc';
                $query->orderBy($request->sort, $sortDirection);
            } else {
                $query->orderBy('kode_obat', 'asc');
            }
    
            $data = $query->paginate(10)->withQueryString();
    
            return view('obat.index', compact('data'));
        } catch (Exception $e) {
            Log::error('Error displaying obat data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat data obat: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisObatOptions = [
            'bebas' => 'Bebas',
            'bebas_terbatas' => 'Bebas Terbatas',
            'herbal' => 'Herbal',
            'psikotropik' => 'Psikotropik',
            'suplemen' => 'Suplemen',
        ];
        
        return view('obat.create', compact('jenisObatOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'nama_obat' => 'required|string|max:255',
        'jenis_obat' => 'required|in:bebas,bebas_terbatas,herbal,psikotropik,suplemen',
        'stok' => 'required|integer|min:0',
        'harga_jual' => 'required|numeric|gt:0',
        'harga_beli' => 'required|numeric|gt:0|lt:harga_jual',
        'kadaluarsa' => 'required|date|after:today'
    ], [
        'harga_beli.lt' => 'Harga beli harus lebih rendah dari harga jual',
        'kadaluarsa.after' => 'Tanggal kadaluarsa harus setelah hari ini'
    ]);
    
    DB::beginTransaction();
    try {
        $jenisObat = $request->jenis_obat;
        $jenis = $this->getJenisCode($jenisObat);
        
        $last = Obat::where('kode_obat', 'like', $jenis . '%')->count() + 1;
        $id_obat = $jenis . str_pad($last, 3, '0', STR_PAD_LEFT);

        $obat = new Obat([
            'kode_obat' => $id_obat,
            'nama_obat' => $request->nama_obat,
            'jenis_obat' => $request->jenis_obat,
            'stok' => $request->stok,
            'harga_jual' => $request->harga_jual,
            'harga_beli' => $request->harga_beli,
            'kadaluarsa' => $request->kadaluarsa
        ]);
        $obat->save();

        DB::commit(); 

        return redirect()->route('obat.index')->with('success', 'Obat berhasil ditambahkan.');
    } catch (Exception $e) {
        DB::rollBack(); 
        Log::error('Error creating obat: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal menambahkan Obat: ' . $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $obat = Obat::findOrFail($id);
            return view('obat.show', compact('obat'));
        } catch (Exception $e) {
            Log::error('Error showing obat: ' . $e->getMessage());
            return redirect()->route('obats.index')
                ->with('error', 'Data obat tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $obat = Obat::findOrFail($id);
            $jenisObatOptions = [
                'bebas' => 'Bebas',
                'bebas_terbatas' => 'Bebas Terbatas',
                'herbal' => 'Herbal',
                'psikotropik' => 'Psikotropik',
                'suplemen' => 'Suplemen',
            ];
            
            return view('obat.edit', compact('obat', 'jenisObatOptions'));
        } catch (Exception $e) {
            Log::error('Error editing obat: ' . $e->getMessage());
            return redirect()->route('obats.index')
                ->with('error', 'Data obat tidak ditemukan');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Obat $obat)
    {
        
        
    $validated = $request->validate([
        'nama_obat' => 'required|string|max:255',
        'jenis_obat' => 'required|in:bebas,bebas_terbatas,herbal,psikotropik,suplemen',
        'stok' => 'required|integer|min:0',
        'harga_jual' => 'required|numeric|gt:0',
        'harga_beli' => 'required|numeric|gt:0|lt:harga_jual',
        'kadaluarsa' => 'required|date|after:today'
    ], [
        'harga_beli.lt' => 'Harga beli harus lebih rendah dari harga jual',
        'kadaluarsa.after' => 'Tanggal kadaluarsa harus setelah hari ini'
    ]);
        
        try {
            $obat->update($request->all());
            return redirect()->route('obat.index')->with('success', 'Obat berhasil diperbarui.');
        } catch (Exception $e) {
            Log::error('Error updating obat: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui Obat: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $obat = Obat::findOrFail($id);
            $obat->delete();
            
            return redirect()->route('obat.index')
                ->with('success', 'Obat berhasil dihapus.');
        } catch (Exception $e) {
            Log::error('Error deleting obat: ' . $e->getMessage());
            return redirect()->route('obat.index')
                ->with('error', 'Gagal menghapus Obat: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the jenis obat code based on the jenis obat value.
     */
private function getJenisCode($jenis)
{
    $codes = [
        'bebas' => 'B',
        'bebas_terbatas' => 'T',
        'herbal' => 'H',
        'psikotropik' => 'P',
        'suplemen' => 'S'
    ];
    
    return $codes[$jenis] ?? 'U'; // Default 'U' for unknown
}
}
