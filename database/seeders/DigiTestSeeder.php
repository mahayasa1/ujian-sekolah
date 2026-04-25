<?php
// ============================================================
// database/seeders/DigiTestSeeder.php
// ============================================================
namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Question;
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
            'email'    => 'admin@digitest.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Teacher 1 - IPA
        $guruUser1 = User::create([
            'name'     => 'Ni Luh Putu Putri Dewi, S.Pd.',
            'email'    => 'guru.ipa@digitest.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'guru',
        ]);
        $guru1 = Teacher::create(['user_id' => $guruUser1->id, 'nip' => '198504152010012023']);

        // Teacher 2 - MTK
        $guruUser2 = User::create([
            'name'     => 'I Made Wirawan, S.Pd.',
            'email'    => 'guru.mtk@digitest.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'guru',
        ]);
        $guru2 = Teacher::create(['user_id' => $guruUser2->id, 'nip' => '197811202005011015']);

        // Subjects
        $ipa = Subject::create(['name' => 'IPA', 'code' => 'IPA-01', 'teacher_id' => $guru1->id]);
        $mtk = Subject::create(['name' => 'Matematika', 'code' => 'MTK-01', 'teacher_id' => $guru2->id]);
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
                'email'    => strtolower(str_replace(' ', '.', $s['name'])) . '@siswa.digitest.sch.id',
                'password' => Hash::make('password'),
                'role'     => 'siswa',
            ]);
            Student::create(['user_id' => $user->id, 'nis' => $s['nis'], 'class_room_id' => $s['class']]);
        }

        // Demo student with easy login
        $demoUser = User::create([
            'name'     => 'Siswa Demo',
            'email'    => 'siswa@digitest.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);
        Student::create(['user_id' => $demoUser->id, 'nis' => '2024099', 'class_room_id' => $kelas9a->id]);

        // Sample IPA questions
        $ipaQuestions = [
            ['q' => 'Organel sel yang berfungsi sebagai pusat kontrol sel adalah...', 'a' => 'Mitokondria', 'b' => 'Nukleus', 'c' => 'Ribosom', 'd' => 'Lisosom', 'e' => 'Vakuola', 'key' => 'B'],
            ['q' => 'Proses fotosintesis menghasilkan...', 'a' => 'CO2 dan H2O', 'b' => 'O2 dan glukosa', 'c' => 'CO2 dan glukosa', 'd' => 'O2 dan H2O', 'e' => 'H2O dan glukosa', 'key' => 'B'],
            ['q' => 'Hukum Newton I menyatakan bahwa benda akan...', 'a' => 'Bergerak dengan percepatan konstan', 'b' => 'Diam atau bergerak lurus beraturan jika resultan gaya nol', 'c' => 'Memiliki percepatan berbanding lurus dengan gaya', 'd' => 'Bekerja dengan aksi dan reaksi', 'e' => 'Selalu bergerak melingkar', 'key' => 'B'],
            ['q' => 'Satuan kuat arus listrik dalam SI adalah...', 'a' => 'Volt', 'b' => 'Ohm', 'c' => 'Watt', 'd' => 'Ampere', 'e' => 'Coulomb', 'key' => 'D'],
            ['q' => 'Proses perubahan wujud dari cair menjadi gas disebut...', 'a' => 'Kondensasi', 'b' => 'Membeku', 'c' => 'Menguap', 'd' => 'Menyublim', 'e' => 'Mencair', 'key' => 'C'],
        ];

        $createdQIds = [];
        foreach ($ipaQuestions as $qData) {
            $q = Question::create([
                'subject_id' => $ipa->id,
                'type'       => 'pg',
                'question'   => $qData['q'],
                'option_a'   => $qData['a'],
                'option_b'   => $qData['b'],
                'option_c'   => $qData['c'],
                'option_d'   => $qData['d'],
                'option_e'   => $qData['e'],
                'answer_key' => $qData['key'],
                'difficulty' => 'sedang',
                'created_by' => $guruUser1->id,
            ]);
            $createdQIds[] = $q->id;
        }

        // Create a demo exam
        $exam = Exam::create([
            'title'           => 'Ujian Sekolah Ilmu Pengetahuan Alam Kelas IX Tahun Pelajaran 2024/2025',
            'subject_id'      => $ipa->id,
            'class_room_id'   => $kelas9a->id,
            'duration'        => 120,
            'token'           => 'IPA001',
            'status'          => 'aktif',
            'random_question' => false,
            'total_questions' => count($createdQIds),
            'created_by'      => $guruUser1->id,
            'start_at'        => now()->subHour(),
            'end_at'          => now()->addHours(2),
        ]);

        // Attach questions
        foreach ($createdQIds as $order => $qid) {
            ExamQuestion::create(['exam_id' => $exam->id, 'question_id' => $qid, 'order' => $order]);
        }
    }
}

// ============================================================
// app/Livewire/Teacher/ExamResults.php
// ============================================================
namespace App\Livewire\Teacher;

use App\Models\Exam;
use App\Models\ExamSession;
use Livewire\Component;

class ExamResults extends Component
{
    public Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function render()
    {
        return view('livewire.teacher.exam-results');
    }
}
