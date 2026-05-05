<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        ClassRoom::insert([
            ['name' => 'VII A', 'grade' => '7'],
            ['name' => 'VII B', 'grade' => '7'],
            ['name' => 'VIII A', 'grade' => '8'],
            ['name' => 'IX A', 'grade' => '9'],
        ]);
    }
}