<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Utama',
                'email' => 'admin@apotek.com',
                'password' => Hash::make('password'), // Ganti password ini di produksi
                'role' => 'admin',
            ],
            [
                'name' => 'Apoteker A',
                'email' => 'apoteker@apotek.com',
                'password' => Hash::make('password'),
                'role' => 'apoteker',
            ],
            [
                'name' => 'Asisten Apoteker B',
                'email' => 'asisten@apotek.com',
                'password' => Hash::make('password'),
                'role' => 'asisten_apoteker',
            ],
            [
                'name' => 'Pemilik Apotek',
                'email' => 'pemilik@apotek.com',
                'password' => Hash::make('password'),
                'role' => 'pemilik',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert(array_merge($user, [
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
