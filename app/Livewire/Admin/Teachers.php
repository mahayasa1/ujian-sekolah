<?php
// app/Livewire/Admin/Teachers.php

namespace App\Livewire\Admin;

use App\Models\Teacher;
use App\Models\User;
use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Teachers extends Component
{
    use WithPagination;

    // --- Search ---
    public string $search  = '';

    // --- Form state ---
    public bool   $showForm = false;
    public ?int   $editId   = null;   // user_id

    // --- Form fields ---
    public string $name     = '';
    public string $email    = '';
    public string $password = '';
    public string $nip      = '';

    // ----------------------------------------------------------------
    public function getTeachersProperty()
    {
        return Teacher::with(['user', 'subjects'])
            ->when($this->search, fn($q) =>
                $q->whereHas('user', fn($u) =>
                    $u->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                )->orWhere('nip', 'like', "%{$this->search}%")
            )
            ->latest()
            ->paginate(15);
    }

    // ----------------------------------------------------------------
    public function save()
    {
        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($this->editId ? ",{$this->editId}" : ''),
            'nip'   => 'nullable|string|max:30',
        ];

        if (! $this->editId) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        DB::transaction(function () {
            $data = [
                'name'  => $this->name,
                'email' => strtolower($this->email),
                'role'  => 'guru',
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            if ($this->editId) {
                $user = User::findOrFail($this->editId);
                $user->update($data);
            } else {
                $user = User::create($data);
            }

            Teacher::updateOrCreate(
                ['user_id' => $user->id],
                ['nip'     => $this->nip ?: null]
            );
        });

        $this->resetForm();
        session()->flash('success', 'Data guru berhasil disimpan.');
    }

    public function edit(int $userId)
    {
        $user = User::with('teacher')->findOrFail($userId);

        $this->editId   = $userId;
        $this->name     = $user->name;
        $this->email    = $user->email;
        $this->password = '';
        $this->nip      = $user->teacher?->nip ?? '';
        $this->showForm = true;
    }

    public function delete(int $userId)
    {
        $user = User::find($userId);
        if ($user && $user->id !== auth()->id()) {
            $user->delete();
            session()->flash('success', 'Guru berhasil dihapus.');
        }
    }

    public function resetForm()
    {
        $this->reset(['showForm', 'editId', 'name', 'email', 'password', 'nip']);
    }

    // ----------------------------------------------------------------
    public function render()
    {
        return view('livewire.admin.teachers')
            ->layout('components.layouts.digitest', ['title' => 'Manajemen Guru']);
    }
}