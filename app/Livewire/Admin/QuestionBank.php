<?php
// app/Livewire/Admin/QuestionBank.php

namespace App\Livewire\Admin;

use App\Models\Question;
use App\Models\Subject;
use Livewire\Component;
use Livewire\WithPagination;

class QuestionBank extends Component
{
    use WithPagination;

    // Form state
    public bool $showForm    = false;
    public ?int $editId      = null;
    public bool $showPreview = false;
    public ?int $previewId   = null;
    public string $search    = '';

    // Form fields
    public string $title             = '';
    public string $googleFormUrl     = '';
    public string $googleFormEditUrl = '';
    public string $googleSheetUrl    = '';
    public string $description       = '';
    public int    $duration          = 60;
    public bool   $isActive          = true;
    public int    $subjectId         = 0;

    public function getQuestionsProperty()
    {
        return Question::with(['subject'])
            ->when($this->search, fn($q) => $q->whereHas('subject', fn($sq) =>
                $sq->where('name', 'like', "%{$this->search}%"))
                ->orWhere('title', 'like', "%{$this->search}%")
            )
            ->latest()
            ->paginate(10);
    }

    public function save(): void
    {
        $this->validate([
            'title'             => 'required|string|max:255',
            'googleFormUrl'     => 'required|url|max:1000',
            'googleFormEditUrl' => 'nullable|url|max:1000',
            'googleSheetUrl'    => 'nullable|url|max:1000',
            'description'       => 'nullable|string|max:1000',
            'duration'          => 'required|integer|min:5|max:300',
            'subjectId'         => 'required|integer|min:1',
        ]);

        Question::updateOrCreate(
            ['id' => $this->editId],
            [
                'subject_id'           => $this->subjectId,
                'title'                => $this->title,
                'google_form_url'      => $this->googleFormUrl,
                'google_form_edit_url' => $this->googleFormEditUrl ?: null,
                'google_sheet_url'     => $this->googleSheetUrl ?: null,
                'description'          => $this->description ?: null,
                'duration'             => $this->duration,
                'is_active'            => $this->isActive,
                'created_by'           => $this->editId ? Question::find($this->editId)?->created_by : auth()->id(),
            ]
        );

        $this->resetForm();
        session()->flash('success', 'Bank soal berhasil disimpan.');
    }

    public function edit(int $id): void
    {
        $q = Question::findOrFail($id);
        $this->editId            = $id;
        $this->subjectId         = $q->subject_id;
        $this->title             = $q->title;
        $this->googleFormUrl     = $q->google_form_url;
        $this->googleFormEditUrl = $q->google_form_edit_url ?? '';
        $this->googleSheetUrl    = $q->google_sheet_url ?? '';
        $this->description       = $q->description ?? '';
        $this->duration          = $q->duration;
        $this->isActive          = $q->is_active;
        $this->showForm          = true;
    }

    public function delete(int $id): void
    {
        Question::find($id)?->delete();
        session()->flash('success', 'Soal berhasil dihapus.');
    }

    public function toggleActive(int $id): void
    {
        $q = Question::find($id);
        if ($q) {
            $q->is_active = !$q->is_active;
            $q->save();
        }
    }

    public function openPreview(int $id): void
    {
        $this->previewId   = $id;
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->previewId   = null;
        $this->showPreview = false;
    }

    public function resetForm(): void
    {
        $this->reset([
            'showForm', 'editId', 'title', 'googleFormUrl',
            'googleFormEditUrl', 'googleSheetUrl', 'description', 'subjectId',
        ]);
        $this->duration = 60;
        $this->isActive = true;
    }

    public function render()
    {
        $subjects = Subject::with('teacher.user')->orderBy('name')->get();
        return view('livewire.admin.question-bank', compact('subjects'))
            ->layout('components.layouts.digitest', ['title' => 'Bank Soal']);
    }
}