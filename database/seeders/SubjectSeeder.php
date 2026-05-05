<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\Teacher;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $mapel = [
            ['name' => 'IPA', 'code' => 'IPA-01'],
            ['name' => 'Matematika', 'code' => 'MTK-01'],
        ];
        
        $teachers = Teacher::all();
        
        foreach ($teachers as $index => $teacher) {
            if (!isset($mapel[$index])) break;
        
            Subject::create([
                'name' => $mapel[$index]['name'],
                'code' => $mapel[$index]['code'],
                'teacher_id' => $teacher->id,
            ]);
        }
    }
}