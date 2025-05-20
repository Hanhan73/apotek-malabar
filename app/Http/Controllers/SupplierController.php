<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Supplier::query();
            
            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $query->where(function($q) use ($request) {
                    $q->where('nama_supplier', 'like', '%' . $request->search . '%')
                      ->orWhere('kode_supplier', 'like', '%' . $request->search . '%')
                      ->orWhere('telepon', 'like', '%' . $request->search . '%');
                });
            }
            
            // Sorting
            if ($request->has('sort') && !empty($request->sort)) {
                $sortDirection = $request->has('direction') ? $request->direction : 'asc';
                $query->orderBy($request->sort, $sortDirection);
            } else {
                $query->orderBy('kode_supplier', 'asc');
            }
            
            $suppliers = $query->paginate(10)->withQueryString();
            
            return view('supplier.index', compact('suppliers'));
        } catch (Exception $e) {
            Log::error('Error displaying supplier data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat data supplier: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'required|string|max:15|regex:/^[0-9\-\+]+$/'
        ], [
            'telepon.regex' => 'Format nomor telepon tidak valid'
        ]);
        
        // Generate a unique kode_supplier
        $lastSupplier = Supplier::orderBy('kode_supplier', 'desc')->first();
        $lastNumber = $lastSupplier ? intval($lastSupplier->kode_supplier) : 0;
        $newNumber = $lastNumber + 1;
        $kodeSupplier = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        // Ensure the code is unique
        while (Supplier::where('kode_supplier', $kodeSupplier)->exists()) {
            $newNumber++;
            $kodeSupplier = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        }
        
        $supplier = new Supplier([
            'kode_supplier' => $kodeSupplier,
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon
        ]);
        
        $supplier->save();
        
        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan.');
    } catch (Exception $e) {
        Log::error('Error creating supplier: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal menambahkan supplier: ' . $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            return view('supplier.show', compact('supplier'));
        } catch (Exception $e) {
            Log::error('Error showing supplier: ' . $e->getMessage());
            return redirect()->route('supplier.index')
                ->with('error', 'Data supplier tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            return view('supplier.edit', compact('supplier'));
        } catch (Exception $e) {
            Log::error('Error editing supplier: ' . $e->getMessage());
            return redirect()->route('supplier.index')
                ->with('error', 'Data supplier tidak ditemukan');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            
            $validated = $request->validate([
                'nama_supplier' => 'required|string|max:255',
                'alamat' => 'required|string|max:500',
                'telepon' => 'required|string|max:15|regex:/^[0-9\-\+]+$/'
            ], [
                'telepon.regex' => 'Format nomor telepon tidak valid'
            ]);
            
            $supplier->update([
                'nama_supplier' => $request->nama_supplier,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon
            ]);
            
            return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diperbarui.');
        } catch (Exception $e) {
            Log::error('Error updating supplier: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui supplier: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();
            
            return redirect()->route('supplier.index')
                ->with('success', 'Supplier berhasil dihapus.');
        } catch (Exception $e) {
            Log::error('Error deleting supplier: ' . $e->getMessage());
            return redirect()->route('supplier.index')
                ->with('error', 'Gagal menghapus supplier: ' . $e->getMessage());
        }
    }
    
    
}