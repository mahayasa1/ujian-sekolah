<?php
// app/Livewire/Admin/Subjects.php
namespace App\Livewire\Admin;

use App\Models\Subject;
use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;

class Subjects extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showForm = false;
    public ?int $editId = null;

    public string $name = '';
    public string $code = '';
    public int $teacherId = 0;
    public string $sortField     = 'id';
    public string $sortDirection = 'asc';
    
    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }
    
    public function getSubjectsProperty()
    {
        $query = Subject::query()
            ->select('subjects.*')
            ->leftJoin('teachers', 'teachers.id', '=', 'subjects.teacher_id')
            ->leftJoin('users', 'users.id', '=', 'teachers.user_id')
            ->with('teacher.user')
            ->when($this->search, fn($q) =>
                $q->where('subjects.name', 'like', "%{$this->search}%")
                  ->orWhere('subjects.code', 'like', "%{$this->search}%")
            );
    
        match ($this->sortField) {
            'name'    => $query->orderBy('subjects.name', $this->sortDirection),
            'code'    => $query->orderBy('subjects.code', $this->sortDirection),
            'teacher' => $query->orderBy('users.name', $this->sortDirection),
            default   => $query->orderBy('subjects.id', $this->sortDirection),
        };
    
        return $query->paginate(10);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:subjects,code' . ($this->editId ? ",{$this->editId}" : ''),
        ]);

        Subject::updateOrCreate(['id' => $this->editId], [
            'name'       => $this->name,
            'code'       => $this->code ?: null,
            'teacher_id' => $this->teacherId ?: null,
        ]);

        $this->reset(['showForm', 'editId', 'name', 'code', 'teacherId']);
        session()->flash('success', 'Mata pelajaran berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $s = Subject::find($id);
        $this->editId   = $id;
        $this->name     = $s->name;
        $this->code     = $s->code ?? '';
        $this->teacherId = $s->teacher_id ?? 0;
        $this->showForm  = true;
    }

    public function delete(int $id)
    {
        Subject::find($id)?->delete();
        session()->flash('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function render()
    {
        $teachers = Teacher::with('user')->get();
        return view('livewire.admin.subjects', compact('teachers'))
            ->layout('components.layouts.digitest', ['title' => 'Manajemen Mata Pelajaran']);
    }
}
