<?php
namespace App\Livewire\Student;
 
use App\Models\Exam;
use App\Models\ExamSession;
use Livewire\Component;
 
class Dashboard extends Component
{
    public function render()
    {
        $student = auth()->user()->student;
        $classRoomId = $student?->class_room_id;
 
        $exams = Exam::with(['subject', 'subject.teacher.user'])
            ->where('class_room_id', $classRoomId)
            ->where('status', 'aktif')
            ->get()
            ->map(function ($exam) use ($student) {
                $session = $student
                    ? ExamSession::where('exam_id', $exam->id)
                        ->where('student_id', $student->id)->first()
                    : null;
                $exam->session = $session;
                return $exam;
            });
 
        $completedSessions = $student
            ? ExamSession::with(['exam.subject'])
                ->where('student_id', $student->id)
                ->where('status', 'selesai')
                ->latest()->get()
            : collect();
 
        return view('livewire.student.dashboard', compact('exams', 'completedSessions'));
    }
}