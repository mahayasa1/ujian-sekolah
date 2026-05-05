<?php

namespace App\Livewire\Student;

use App\Models\Violation;
use App\Models\ExamSession;
use Livewire\Component;

class ExamPage extends Component
{
    public ExamSession $session;
    public int $violationCount     = 0;
    public bool $showSubmitConfirm = false;

    public function mount(ExamSession $session)
    {
        if ($session->student_id !== auth()->user()->student?->id) {
            abort(403);
        }

        // ✅ Jika sudah selesai
        if ($session->status === 'selesai') {
            return $this->redirect(route('student.result', $session->id), navigate: true);
        }

        // ✅ Jika sedang lock
        if ($session->reentry_token) {
            return $this->redirect(route('student.dashboard'), navigate: true);
        }

        // ✅ Auto selesai jika waktu habis
        if ($session->getTimeLeftSeconds() <= 0) {
            $session->update([
                'status'       => 'selesai',
                'submitted_at' => now()
            ]);

            return $this->redirect(route('student.result', $session->id), navigate: true);
        }

        $session->load([
            'exam.subject',
            'violations'
        ]);

        $this->session = $session;
        $this->violationCount = $session->violations->count();
    }

    /**
     * 🚫 VIOLATION LOCK (ANTI SPAM)
     */
    public function reportViolationAndLock(string $type)
    {
        // ❗ Jangan double
        if ($this->session->reentry_token) {
            return;
        }

        Violation::create([
            'exam_session_id' => $this->session->id,
            'type'            => $type,
            'description'     => 'Pelanggaran: ' . $type . ' (' . now()->format('H:i:s') . ')',
        ]);

        $this->violationCount++;

        // 🔒 Freeze waktu
        $this->session->snapshotRemainingTime();

        // 🔑 Lock
        $this->session->generateReentryToken();

        return $this->redirect(route('student.dashboard'), navigate: true);
    }

    public function reportViolation(string $type)
    {
        $this->reportViolationAndLock($type);
    }

    public function confirmSubmit()
    {
        $this->showSubmitConfirm = true;
    }

    /**
     * ✅ SUBMIT FINAL
     */
    public function submit()
    {
        $this->session->update([
            'status'        => 'selesai',
            'submitted_at'  => now(),
            'reentry_token' => null,
        ]);

        $this->dispatch('exam-submitted', sessionId: $this->session->id);

        return $this->redirect(route('student.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.student.exam-page')
            ->layout('components.layouts.exam', [
                'title' => $this->session->exam->title
            ]);
    }
}