<?php

namespace App\Livewire\Student;

use App\Models\ExamSession;
use Livewire\Component;

class Result extends Component
{
    public ExamSession $session;
 
    public function mount(ExamSession $session)
    {
        if ($session->student_id !== auth()->user()->student?->id) {
            abort(403);
        }
        $this->session = $session;
    }
 
    public function render()
    {
        $session = $this->session->load(['exam.subject', 'exam.questions', 'answers.question']);
        $totalPg    = $session->exam->questions->where('type', 'pg')->count();
        $correctPg  = $session->answers->where('score', 1)->count();
        $unanswered = $session->exam->questions->count() - $session->answers->count();
 
        return view('livewire.student.result', compact('totalPg', 'correctPg', 'unanswered'));
    }
}