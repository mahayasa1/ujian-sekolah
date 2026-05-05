<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Guru
        User::insert([
            [
                'name' => 'Ni Luh Putu Putri Dewi, S.Pd.',
                'email' => 'guru.ipa@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ],
            [
                'name' => 'I Made Wirawan, S.Pd.',
                'email' => 'guru.mtk@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ],
        ]);

        // Siswa
        User::insert([
            [
                'name' => 'I Gede Ari Pratama',
                'email' => 'ari.pratama@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ],
            [
                'name' => 'Ni Putu Ayu Lestari',
                'email' => 'ayu.lestari@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ],
            [
                'name' => 'Komang Diva Cahyani',
                'email' => 'diva.cahyani@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ],
            [
                'name' => 'I Nyoman Bayu Saputra',
                'email' => 'bayu.saputra@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ],
            [
                'name' => 'Ni Kadek Sari Dewi',
                'email' => 'sari.dewi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'siswa',
            ],
        ]);
    }
}