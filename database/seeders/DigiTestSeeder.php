<?php
// ============================================================
// database/seeders/DigiTestSeeder.php
// ============================================================
namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DigiTestSeeder extends Seeder
{
    public function run(): void
    {
        // Classes
        $kelas7a = ClassRoom::create(['name' => 'VII A', 'grade' => '7']);
        $kelas7b = ClassRoom::create(['name' => 'VII B', 'grade' => '7']);
        $kelas8a = ClassRoom::create(['name' => 'VIII A', 'grade' => '8']);
        $kelas9a = ClassRoom::create(['name' => 'IX A', 'grade' => '9']);

        // Admin
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Teacher 1 - IPA
        $guruUser1 = User::create([
            'name'     => 'Ni Luh Putu Putri Dewi, S.Pd.',
            'email'    => 'guru.ipa@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'guru',
        ]);
        $guru1 = Teacher::create([
            'user_id' => $guruUser1->id,
            'nip' => '198504152010012023'
        ]);

        // Teacher 2 - MTK
        $guruUser2 = User::create([
            'name'     => 'I Made Wirawan, S.Pd.',
            'email'    => 'guru.mtk@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'guru',
        ]);
        $guru2 = Teacher::create([
            'user_id' => $guruUser2->id,
            'nip' => '197811202005011015'
        ]);

        // Subjects
        $ipa  = Subject::create(['name' => 'IPA', 'code' => 'IPA-01', 'teacher_id' => $guru1->id]);
        $mtk  = Subject::create(['name' => 'Matematika', 'code' => 'MTK-01', 'teacher_id' => $guru2->id]);
        $bind = Subject::create(['name' => 'B. Indonesia', 'code' => 'BIND-01', 'teacher_id' => $guru1->id]);

        // Students
        $siswaData = [
            ['name' => 'I Gede Ari Pratama', 'nis' => '2024001', 'class' => $kelas9a->id],
            ['name' => 'Ni Putu Ayu Lestari', 'nis' => '2024002', 'class' => $kelas9a->id],
            ['name' => 'Komang Diva Cahyani', 'nis' => '2024003', 'class' => $kelas9a->id],
            ['name' => 'I Nyoman Bayu Saputra', 'nis' => '2024004', 'class' => $kelas9a->id],
            ['name' => 'Ni Kadek Sari Dewi', 'nis' => '2024005', 'class' => $kelas9a->id],
        ];

        foreach ($siswaData as $s) {
            $user = User::create([
                'name'     => $s['name'],
                'email'    => strtolower(str_replace(' ', '.', $s['name'])) . '@gmail.com',
                'password' => Hash::make('password'),
                'role'     => 'siswa',
            ]);

            Student::create([
                'user_id' => $user->id,
                'nis' => $s['nis'],
                'class_room_id' => $s['class']
            ]);
        }
    }
}