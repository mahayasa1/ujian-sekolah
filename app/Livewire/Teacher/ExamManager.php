<?php

namespace App\Livewire\Teacher;

use App\Models\Exam;
use App\Models\ClassRoom;
use Livewire\Component;
use Illuminate\Support\Str;

class ExamManager extends Component
{
    public int $subjectId;
    public bool $showForm = false;

    // Form
    public string $title         = '';
    public int $classRoomId      = 0;
    public int $duration         = 90;
    public string $startAt       = '';
    public string $endAt         = '';
    public string $googleFormUrl = '';   // Link Google Form
    public ?int $editId          = null;

    public function mount(int $subjectId)
    {
        $this->subjectId = $subjectId;
    }

    public function getExamsProperty()
    {
        return Exam::where('subject_id', $this->subjectId)
            ->with(['classRoom', 'sessions'])
            ->latest()->get();
    }

    public function save()
    {
        $this->validate([
            'title'         => 'required|string|max:255',
            'duration'      => 'required|integer|min:5|max:300',
            'classRoomId'   => 'nullable|integer',
            'googleFormUrl' => 'nullable|url|max:1000',
        ]);

        $token = strtoupper(Str::random(6));

        Exam::updateOrCreate(
            ['id' => $this->editId],
            [
                'title'           => $this->title,
                'subject_id'      => $this->subjectId,
                'class_room_id'   => $this->classRoomId ?: null,
                'duration'        => $this->duration,
                'start_at'        => $this->startAt ?: null,
                'end_at'          => $this->endAt ?: null,
                'token'           => $this->editId
                    ? (Exam::find($this->editId)?->token ?? $token)
                    : $token,
                'google_form_url' => $this->googleFormUrl ?: null,
                'created_by'      => auth()->id(),
            ]
        );

        $this->reset(['showForm', 'editId', 'title', 'googleFormUrl', 'startAt', 'endAt']);
        $this->duration     = 90;
        $this->classRoomId  = 0;
        session()->flash('success', 'Ujian berhasil disimpan.');
    }

    public function edit(int $examId)
    {
        $exam = Exam::findOrFail($examId);
        $this->editId        = $examId;
        $this->title         = $exam->title;
        $this->classRoomId   = $exam->class_room_id ?? 0;
        $this->duration      = $exam->duration;
        $this->startAt       = $exam->start_at?->format('Y-m-d\TH:i') ?? '';
        $this->endAt         = $exam->end_at?->format('Y-m-d\TH:i') ?? '';
        $this->googleFormUrl = $exam->google_form_url ?? '';
        $this->showForm      = true;
    }

    public function toggleStatus(int $examId)
    {
        $exam         = Exam::find($examId);
        $exam->status = $exam->status === 'aktif' ? 'draft' : 'aktif';
        $exam->save();
    }

    public function render()
    {
        $classRooms = ClassRoom::all();
        return view('livewire.teacher.exam-manager', compact('classRooms'));
    }
}