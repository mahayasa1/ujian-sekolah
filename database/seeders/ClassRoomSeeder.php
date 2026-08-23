<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        ClassRoom::insert([
            // Kelas VII
            ['name' => 'VII A', 'grade' => '7'],
            ['name' => 'VII B', 'grade' => '7'],
            ['name' => 'VII C', 'grade' => '7'],
            ['name' => 'VII D', 'grade' => '7'],
            ['name' => 'VII E', 'grade' => '7'],
            ['name' => 'VII F', 'grade' => '7'],

            // Kelas VIII
            ['name' => 'VIII A', 'grade' => '8'],
            ['name' => 'VIII B', 'grade' => '8'],
            ['name' => 'VIII C', 'grade' => '8'],
            ['name' => 'VIII D', 'grade' => '8'],
            ['name' => 'VIII E', 'grade' => '8'],
            ['name' => 'VIII F', 'grade' => '8'],
            ['name' => 'VIII G', 'grade' => '8'],

            // Kelas IX
            ['name' => 'IX A', 'grade' => '9'],
            ['name' => 'IX B', 'grade' => '9'],
            ['name' => 'IX C', 'grade' => '9'],
            ['name' => 'IX D', 'grade' => '9'],
            ['name' => 'IX E', 'grade' => '9'],
            ['name' => 'IX F', 'grade' => '9'],
            ['name' => 'IX G', 'grade' => '9'],
        ]);
    }
}