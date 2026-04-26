<?php
// app/Livewire/Admin/Users.php
namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ClassRoom;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class Users extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public bool $showForm = false;
    public ?int $editId = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'siswa';
    public string $nip = '';
    public string $nis = '';
    public int $classRoomId = 0;

    public function mount()
    {
        // Baca filter role dari query string (misal dari dashboard)
        if (request()->has('role')) {
            $this->roleFilter = request('role');
        }
    }

    public function getUsersProperty()
    {
        return User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn($q) => $q->where('role', $this->roleFilter))
            ->latest()
            ->paginate(10);
    }

    public function save()
    {
        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($this->editId ? ",{$this->editId}" : ''),
            'role'  => 'required|in:admin,guru,siswa',
        ];
        if (!$this->editId) $rules['password'] = 'required|min:8';
        $this->validate($rules);

        $data = ['name' => $this->name, 'email' => $this->email, 'role' => $this->role];
        if ($this->password) $data['password'] = Hash::make($this->password);

        $user = User::updateOrCreate(['id' => $this->editId], $data);

        if ($this->role === 'guru') {
            Teacher::updateOrCreate(['user_id' => $user->id], ['nip' => $this->nip ?: null]);
        } elseif ($this->role === 'siswa') {
            Student::updateOrCreate(['user_id' => $user->id], [
                'nis'           => $this->nis ?: null,
                'class_room_id' => $this->classRoomId ?: null,
            ]);
        }

        $this->reset(['showForm', 'editId', 'name', 'email', 'password', 'nip', 'nis', 'classRoomId']);
        session()->flash('success', 'Pengguna berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $user = User::with(['teacher', 'student'])->find($id);
        $this->editId      = $id;
        $this->name        = $user->name;
        $this->email       = $user->email;
        $this->role        = $user->role;
        $this->nip         = $user->teacher?->nip ?? '';
        $this->nis         = $user->student?->nis ?? '';
        $this->classRoomId = $user->student?->class_room_id ?? 0;
        $this->showForm    = true;
    }

    public function delete(int $id)
    {
        User::find($id)?->delete();
        session()->flash('success', 'Pengguna berhasil dihapus.');
    }

    public function render()
    {
        $classRooms = ClassRoom::all();
        return view('livewire.admin.users', compact('classRooms'))
            ->layout('components.layouts.digitest', ['title' => 'Manajemen Pengguna']);
    }
}