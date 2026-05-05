<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\User;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $guru1 = User::where('email', 'guru.ipa@gmail.com')->first();
        $guru2 = User::where('email', 'guru.mtk@gmail.com')->first();

        Teacher::create([
            'user_id' => $guru1->id,
            'nip' => '198504152010012023',
        ]);

        Teacher::create([
            'user_id' => $guru2->id,
            'nip' => '197811202005011015',
        ]);
    }
}