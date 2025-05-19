<?php

// database/seeders/ObatSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;
use Carbon\Carbon;

class ObatSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'jenis_obat' => 'bebas',
                'nama_obat' => 'Paracetamol 500mg',
                'harga_jual' => 2500,
                'harga_beli' => 1500,
                'stok' => 100,
                'kadaluarsa' => Carbon::now()->addYear(),
            ],
            [
                'jenis_obat' => 'herbal',
                'nama_obat' => 'Temulawak Herbal',
                'harga_jual' => 5000,
                'harga_beli' => 3000,
                'stok' => 50,
                'kadaluarsa' => Carbon::now()->addMonths(9),
            ],
            [
                'jenis_obat' => 'psikotropik',
                'nama_obat' => 'Diazepam 2mg',
                'harga_jual' => 7500,
                'harga_beli' => 5000,
                'stok' => 30,
                'kadaluarsa' => Carbon::now()->addMonths(6),
            ],
            [
                'jenis_obat' => 'suplemen',
                'nama_obat' => 'Vitamin C 1000mg',
                'harga_jual' => 10000,
                'harga_beli' => 7000,
                'stok' => 200,
                'kadaluarsa' => Carbon::now()->addMonths(12),
            ],
            [
                'jenis_obat' => 'bebas_terbatas',
                'nama_obat' => 'CTM 4mg',
                'harga_jual' => 1500,
                'harga_beli' => 800,
                'stok' => 80,
                'kadaluarsa' => Carbon::now()->addMonths(10),
            ],
        ];

        $kodeAwalan = [
            'Bebas' => 'B',
            'Herbal' => 'H',
            'Psikotropik' => 'P',
            'Suplemen' => 'S',
            'Bebas Terbatas' => 'T',
        ];

        foreach ($data as $index => $obat) {
            $prefix = $kodeAwalan[$obat['jenis_obat']] ?? 'X';
            $kode = $prefix . str_pad(($index + 1), 3, '0', STR_PAD_LEFT);

            Obat::create([
                'kode_obat' => $kode,
                'nama_obat' => $obat['nama_obat'],
                'jenis_obat' => $obat['jenis_obat'],
                'stok' => $obat['stok'],
                'harga_jual' => $obat['harga_jual'],
                'harga_beli' => $obat['harga_beli'],
                'kadaluarsa' => $obat['kadaluarsa'],
            ]);
        }
    }
}


