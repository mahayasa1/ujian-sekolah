<?php

namespace App\Livewire\Teacher;

use App\Models\Exam;
use App\Models\ExamSession;
use Livewire\Component;

class ExamMonitor extends Component
{
    public Exam $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function render()
    {
        $sessions = ExamSession::where('exam_id', $this->exam->id)
            ->with(['student.user', 'violations'])
            ->get();

        return view('livewire.teacher.exam-monitor', compact('sessions'))
        ->layout('components.layouts.digitest', ['title' => 'Monitor Ujian']);
    }
}