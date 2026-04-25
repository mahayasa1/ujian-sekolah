<?php

namespace App\Livewire\Teacher;

use App\Models\Exam;
use App\Models\ClassRoom;
use App\Models\Question;
use Livewire\Component;

class ExamManager extends Component
{
    public int $subjectId;
    public bool $showForm = false;
 
    // Form
    public string $title     = '';
    public int $classRoomId  = 0;
    public int $duration     = 90;
    public string $startAt   = '';
    public string $endAt     = '';
    public bool $randomQuestion = false;
    public array $selectedQuestions = [];
    public ?int $editId = null;
 
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
        $token = strtoupper(\Illuminate\Support\Str::random(6));
 
        $exam = Exam::updateOrCreate(
            ['id' => $this->editId],
            [
                'title'           => $this->title,
                'subject_id'      => $this->subjectId,
                'class_room_id'   => $this->classRoomId ?: null,
                'duration'        => $this->duration,
                'start_at'        => $this->startAt ?: null,
                'end_at'          => $this->endAt ?: null,
                'token'           => $this->editId
                    ? Exam::find($this->editId)?->token ?? $token
                    : $token,
                'random_question' => $this->randomQuestion,
                'total_questions' => count($this->selectedQuestions),
                'created_by'      => auth()->id(),
            ]
        );
 
        // Sync questions
        $questionData = collect($this->selectedQuestions)->mapWithKeys(fn ($id, $i) => [$id => ['order' => $i]]);
        $exam->questions()->sync($questionData);
 
        $this->reset(['showForm', 'editId', 'title', 'selectedQuestions']);
        session()->flash('success', 'Ujian berhasil disimpan.');
    }
 
    public function toggleStatus(int $examId)
    {
        $exam = Exam::find($examId);
        $exam->status = $exam->status === 'aktif' ? 'draft' : 'aktif';
        $exam->save();
    }
 
    public function render()
    {
        $classRooms = ClassRoom::all();
        $questions  = Question::where('subject_id', $this->subjectId)->get();
        return view('livewire.teacher.exam-manager', compact('classRooms', 'questions'));
    }
}