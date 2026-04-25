<?php

namespace App\Livewire\Teacher;

use App\Models\Question;
use Livewire\Component;

class QuestionBank extends Component
{
    public int $subjectId;
    public string $search = '';
    public string $type   = '';
    public bool $showForm = false;
 
    // Form fields
    public string $questionType = 'pg';
    public string $questionText = '';
    public string $optionA = '', $optionB = '', $optionC = '', $optionD = '', $optionE = '';
    public string $answerKey  = '';
    public string $difficulty = 'sedang';
    public ?int $editId = null;
 
    public function mount(int $subjectId)
    {
        $this->subjectId = $subjectId;
    }
 
    public function getQuestionsProperty()
    {
        return Question::where('subject_id', $this->subjectId)
            ->when($this->search, fn ($q) => $q->where('question', 'like', "%{$this->search}%"))
            ->when($this->type,   fn ($q) => $q->where('type', $this->type))
            ->latest()
            ->paginate(10);
    }
 
    public function save()
    {
        $data = [
            'subject_id'  => $this->subjectId,
            'type'        => $this->questionType,
            'question'    => $this->questionText,
            'option_a'    => $this->optionA,
            'option_b'    => $this->optionB,
            'option_c'    => $this->optionC,
            'option_d'    => $this->optionD,
            'option_e'    => $this->optionE,
            'answer_key'  => $this->answerKey,
            'difficulty'  => $this->difficulty,
            'created_by'  => auth()->id(),
        ];
 
        if ($this->editId) {
            Question::find($this->editId)?->update($data);
        } else {
            Question::create($data);
        }
 
        $this->reset(['showForm', 'editId', 'questionText', 'optionA', 'optionB', 'optionC', 'optionD', 'optionE', 'answerKey']);
        session()->flash('success', 'Soal berhasil disimpan.');
    }
 
    public function edit(int $id)
    {
        $q = Question::find($id);
        $this->editId       = $id;
        $this->questionType = $q->type;
        $this->questionText = $q->question;
        $this->optionA      = $q->option_a ?? '';
        $this->optionB      = $q->option_b ?? '';
        $this->optionC      = $q->option_c ?? '';
        $this->optionD      = $q->option_d ?? '';
        $this->optionE      = $q->option_e ?? '';
        $this->answerKey    = $q->answer_key ?? '';
        $this->difficulty   = $q->difficulty;
        $this->showForm     = true;
    }
 
    public function delete(int $id)
    {
        Question::find($id)?->delete();
    }
 
    public function render()
    {
        return view('livewire.teacher.question-bank');
    }
}