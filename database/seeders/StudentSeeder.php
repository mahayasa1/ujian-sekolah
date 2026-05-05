<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $kelas9a = ClassRoom::where('name', 'IX A')->first();

        $students = [
            ['email' => 'ari.pratama@gmail.com', 'nis' => '2024001'],
            ['email' => 'ayu.lestari@gmail.com', 'nis' => '2024002'],
            ['email' => 'diva.cahyani@gmail.com', 'nis' => '2024003'],
            ['email' => 'bayu.saputra@gmail.com', 'nis' => '2024004'],
            ['email' => 'sari.dewi@gmail.com', 'nis' => '2024005'],
        ];

        foreach ($students as $s) {
            $user = User::where('email', $s['email'])->first();

            Student::create([
                'user_id' => $user->id,
                'nis' => $s['nis'],
                'nisn' => '00' . $s['nis'],
                'class_room_id' => $kelas9a->id,
            ]);
        }
    }
}