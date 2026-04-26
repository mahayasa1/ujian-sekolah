<?php
// app/Livewire/Admin/Dashboard.php
namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Subject;
use App\Models\ClassRoom;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_users'    => User::count(),
            'total_guru'     => User::where('role', 'guru')->count(),
            'total_siswa'    => User::where('role', 'siswa')->count(),
            'total_mapel'    => Subject::count(),
            'total_kelas'    => ClassRoom::count(),
            'total_ujian'    => Exam::count(),
            'ujian_aktif'    => Exam::where('status', 'aktif')->count(),
            'total_sesi'     => ExamSession::where('status', 'selesai')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentExams = Exam::with(['subject', 'classRoom', 'sessions'])
            ->latest()->take(5)->get();

        $examStats = [
            'draft'   => Exam::where('status', 'draft')->count(),
            'aktif'   => Exam::where('status', 'aktif')->count(),
            'selesai' => Exam::where('status', 'selesai')->count(),
        ];

        return view('livewire.admin.dashboard', compact('stats', 'recentUsers', 'recentExams', 'examStats'))
            ->layout('components.layouts.digitest', ['title' => 'Dashboard Admin']);
    }
}
