<?php

namespace App\Livewire\Student;

use App\Models\Exam;
use App\Models\ExamSession;
use Livewire\Component;

class Dashboard extends Component
{
    public ?int $selectedExamId      = null;
    public ?int $selectedSessionId   = null; // untuk reentry: session yang mau dilanjutkan
    public string $tokenInput        = '';
    public string $tokenError        = '';
    public bool $isReentry           = false; // apakah popup untuk reentry (bukan mulai baru)

    public function selectExam(int $examId): void
    {
        $this->reset(['tokenInput', 'tokenError', 'selectedSessionId', 'isReentry']);
        $this->selectedExamId = $examId;

        // Cek apakah ada sesi aktif yang perlu reentry
        $student  = auth()->user()->student;
        $existing = ExamSession::where('exam_id', $examId)
            ->where('student_id', $student?->id)
            ->where('status', 'aktif')
            ->first();

        if ($existing) {
            $this->selectedSessionId = $existing->id;
            $this->isReentry = true;
        }
    }

    public function closePopup(): void
    {
        $this->reset(['selectedExamId', 'selectedSessionId', 'tokenInput', 'tokenError', 'isReentry']);
    }

    public function submitToken()
    {
        $this->tokenError = '';

        $student = auth()->user()->student;
        if (!$student) {
            $this->tokenError = 'Data siswa tidak ditemukan. Hubungi admin.';
            return;
        }

        // ========================
        // MODE REENTRY (session sudah ada, status aktif)
        // ========================
        if ($this->isReentry && $this->selectedSessionId) {
            $session = ExamSession::with('exam')->find($this->selectedSessionId);

            if (!$session) {
                $this->tokenError = 'Sesi ujian tidak ditemukan.';
                return;
            }

            // Validasi reentry token (case-insensitive)
            if (!$session->reentry_token || strtoupper(trim($this->tokenInput)) !== strtoupper($session->reentry_token)) {
                $this->tokenError = 'Token re-entry tidak valid. Periksa kembali token yang diberikan guru.';
                return;
            }

            // Cek apakah waktu masih ada
            if ($session->getTimeLeftSeconds() <= 0) {
                // Waktu habis, otomatis selesaikan
                $session->update(['status' => 'selesai', 'submitted_at' => now()]);
                $this->tokenError = 'Waktu ujian sudah habis. Ujian otomatis dikumpulkan.';
                return;
            }

            // Berhasil reentry — clear reentry token, update last_violation_at sebagai basis baru
            // Simpan sisa waktu yang akurat berdasarkan snapshot
            $remaining = $session->getTimeLeftSeconds();
            $session->update([
                'reentry_token'     => null,
                'last_violation_at' => now(),
                'remaining_seconds' => $remaining,
            ]);

            return $this->redirect(route('student.exam', $session->id), navigate: true);
        }

        // ========================
        // MODE MULAI BARU
        // ========================
        $exam = Exam::find($this->selectedExamId);
        if (!$exam) {
            $this->tokenError = 'Ujian tidak ditemukan.';
            return;
        }

        if (strtoupper(trim($this->tokenInput)) !== strtoupper($exam->token)) {
            $this->tokenError = 'Token tidak valid. Periksa kembali token dari guru pengawas.';
            return;
        }

        if (!$exam->isActive()) {
            $this->tokenError = 'Ujian belum aktif atau sudah berakhir.';
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

        $selectedSession = $this->selectedSessionId
            ? ExamSession::with('exam')->find($this->selectedSessionId)
            : null;

        return view('livewire.student.dashboard',
            compact('exams', 'completedSessions', 'selectedExam', 'selectedSession')
        )->layout('components.layouts.digitest', ['title' => 'Dashboard Siswa']);
    }
}