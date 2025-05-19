<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run()
    {
        Supplier::insert([
            [
                'kode_supplier' => '001',
                'nama_supplier' => 'PT Kimia Farma',
                'alamat' => 'Jl. Sudirman No. 10, Jakarta',
                'telepon' => '02112345678',
            ],
            [
                'kode_supplier' => '002',
                'nama_supplier' => 'PT Indofarma',
                'alamat' => 'Jl. Merdeka No. 20, Bandung',
                'telepon' => '02287654321',
            ],
        ]);
    }
}
