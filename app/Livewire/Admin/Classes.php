<?php
// app/Livewire/Admin/Classes.php
namespace App\Livewire\Admin;

use App\Models\ClassRoom;
use Livewire\Component;

class Classes extends Component
{
    public bool $showForm = false;
    public ?int $editId = null;
    public string $name = '';
    public string $grade = '';

    public function getClassesProperty()
    {
        return ClassRoom::withCount('students')->orderBy('grade')->orderBy('name')->get();
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required|string|max:50',
            'grade' => 'required|string|max:10',
        ]);

        ClassRoom::updateOrCreate(['id' => $this->editId], [
            'name'  => $this->name,
            'grade' => $this->grade,
        ]);

        $this->reset(['showForm', 'editId', 'name', 'grade']);
        session()->flash('success', 'Kelas berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $c = ClassRoom::find($id);
        $this->editId = $id;
        $this->name   = $c->name;
        $this->grade  = $c->grade;
        $this->showForm = true;
    }

    public function delete(int $id)
    {
        ClassRoom::find($id)?->delete();
        session()->flash('success', 'Kelas berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.classes')
            ->layout('components.layouts.digitest', ['title' => 'Manajemen Kelas']);
    }
}
