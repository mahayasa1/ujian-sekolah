<?php

namespace App\Livewire\Student;

use App\Models\Exam;
use App\Models\ExamSession;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int $selectedExamId = null;
    public string $tokenInput   = '';
    public string $tokenError   = '';

    public function selectExam(int $examId): void
    {
        $this->selectedExamId = $examId;
        $this->tokenInput     = '';
        $this->tokenError     = '';
    }

    public function closePopup(): void
    {
        $this->selectedExamId = null;
        $this->tokenInput     = '';
        $this->tokenError     = '';
    }

    public function submitToken()
    {
        $this->tokenError = '';

        $exam = Exam::find($this->selectedExamId);
        if (!$exam) {
            $this->tokenError = 'Ujian tidak ditemukan.';
            return;
        }

        if (strtoupper(trim($this->tokenInput)) !== strtoupper($exam->token)) {
            $this->tokenError = 'Token tidak valid. Periksa kembali token dari guru Anda.';
            return;
        }

        if (!$exam->isActive()) {
            $this->tokenError = 'Ujian belum aktif atau sudah berakhir.';
            return;
        }

        $student  = auth()->user()->student;
        if (!$student) {
            $this->tokenError = 'Data siswa tidak ditemukan. Hubungi admin.';
            return;
        }

        $existing = ExamSession::where('exam_id', $exam->id)
            ->where('student_id', $student->id)->first();

        if ($existing && $existing->status === 'selesai') {
            $this->tokenError = 'Anda sudah menyelesaikan ujian ini.';
            return;
        }

        if (!$existing) {
            $existing = ExamSession::create([
                'exam_id'    => $exam->id,
                'student_id' => $student->id,
                'status'     => 'aktif',
                'started_at' => now(),
            ]);
        }

        return $this->redirect(route('student.exam', $existing->id), navigate: true);
    }

    public function render()
    {
        $student     = auth()->user()->student;
        $classRoomId = $student?->class_room_id;

        // Ambil semua ujian aktif yang ditujukan untuk kelas siswa
        // Termasuk ujian tanpa kelas tertentu (berlaku untuk semua kelas)
        $exams = Exam::with(['subject', 'subject.teacher.user', 'classRoom'])
            ->where('status', 'aktif')
            ->where(function ($q) use ($classRoomId) {
                $q->where('class_room_id', $classRoomId)
                  ->orWhereNull('class_room_id');
            })
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

        $selectedExam = $this->selectedExamId
            ? Exam::with(['subject', 'classRoom'])->find($this->selectedExamId)
            : null;

        return view('livewire.student.dashboard',
            compact('exams', 'completedSessions', 'selectedExam')
        )->layout('components.layouts.digitest', ['title' => 'Dashboard Siswa']);
    }
}