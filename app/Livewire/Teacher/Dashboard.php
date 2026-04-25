<?php

namespace App\Livewire\Teacher;

use App\Models\Exam;
use App\Models\Subject;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $teacher = auth()->user()->teacher;

        $subjects = Subject::where('teacher_id', $teacher?->id)
            ->with('exams')
            ->get();

        $subjectIds = $subjects->pluck('id');

        $activeExams = Exam::whereIn('subject_id', $subjectIds)
            ->where('status', 'aktif')
            ->with(['subject', 'classRoom', 'sessions'])
            ->get();

        $recentExams = Exam::whereIn('subject_id', $subjectIds)
            ->with(['subject', 'classRoom'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.teacher.dashboard', compact(
            'subjects',
            'activeExams',
            'recentExams'
        ))->layout('components.layouts.digitest', [
            'title' => 'Dashboard Guru'
        ]);
    }
}