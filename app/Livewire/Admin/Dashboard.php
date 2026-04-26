<?php
// app/Livewire/Admin/Dashboard.php
namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Subject;
use App\Models\ClassRoom;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_guru'  => User::where('role', 'guru')->count(),
            'total_siswa' => User::where('role', 'siswa')->count(),
            'total_mapel' => Subject::count(),
            'total_kelas' => ClassRoom::count(),
        ];

        return view('livewire.admin.dashboard', compact('stats'))
            ->layout('components.layouts.digitest', ['title' => 'Dashboard Admin']);
    }
}