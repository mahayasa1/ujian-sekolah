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

    public function getSubjectsProperty()
    {
        return Subject::with('teacher.user')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('code', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);
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
