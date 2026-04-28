<?php

namespace App\Livewire\Student;

use App\Models\Answer;
use App\Models\Violation;
use App\Models\ExamSession;
use Livewire\Component;

class ExamPage extends Component
{
    public ExamSession $session;
    public int $currentIndex       = 0;
    public array $answers          = [];
    public int $violationCount     = 0;
    public bool $showSubmitConfirm = false;

    public function mount(ExamSession $session)
    {
        // Validasi kepemilikan
        if ($session->student_id !== auth()->user()->student?->id) {
            abort(403);
        }

        // Jika sudah selesai, redirect ke result
        if ($session->status === 'selesai') {
            return $this->redirect(route('student.result', $session->id), navigate: true);
        }

        // Jika ada reentry_token (siswa sedang di-lock), redirect ke dashboard
        if ($session->reentry_token) {
            return $this->redirect(route('student.dashboard'), navigate: true);
        }

        $this->session = $session;

        // Pre-load answers
        foreach ($session->answers as $ans) {
            $this->answers[$ans->question_id] = $ans->answer;
        }

        $this->violationCount = $session->violations()->count();
    }

    public function getQuestionsProperty()
    {
        if ($this->session->exam->google_form_url) {
            return collect();
        }
        return $this->session->exam->questions ?? collect();
    }

    public function saveAnswer(int $questionId, string $answer)
    {
        $this->answers[$questionId] = $answer;

        Answer::updateOrCreate(
            ['exam_session_id' => $this->session->id, 'question_id' => $questionId],
            ['answer' => $answer]
        );
    }

    public function goTo(int $index)
    {
        $this->currentIndex = $index;
    }

    /**
     * Dipanggil dari JS saat deteksi pindah tab / blur window.
     * Catat violation, SNAPSHOT waktu (timer berhenti di titik ini),
     * generate reentry token, redirect ke dashboard.
     *
     * Saat siswa re-entry dengan token yang valid, getTimeLeftSeconds()
     * akan menghitung dari remaining_seconds yang di-snapshot ini.
     */
    public function reportViolationAndLock(string $type)
    {
        // Simpan violation
        Violation::create([
            'exam_session_id' => $this->session->id,
            'type'            => $type,
            'description'     => 'Pelanggaran otomatis: ' . $type . ' pada ' . now()->format('H:i:s'),
        ]);

        $this->violationCount++;

        // PENTING: Snapshot sisa waktu → timer berhenti di detik ini
        // Saat re-entry, getTimeLeftSeconds() pakai remaining_seconds ini sebagai basis
        $this->session->snapshotRemainingTime();

        // Generate reentry token
        $this->session->generateReentryToken();

        // Redirect ke dashboard
        $this->redirect(route('student.dashboard'), navigate: true);
    }

    /**
     * Backward compat
     */
    public function reportViolation(string $type)
    {
        $this->reportViolationAndLock($type);
    }

    public function confirmSubmit()
    {
        $this->showSubmitConfirm = true;
    }

    public function submit()
    {
        if (!$this->session->exam->google_form_url) {
            $this->autoScore();
        }

        $this->session->update([
            'status'        => 'selesai',
            'submitted_at'  => now(),
            'reentry_token' => null,
        ]);

        return $this->redirect(route('student.result', $this->session->id), navigate: true);
    }

    private function autoScore()
    {
        $totalPg   = 0;
        $correctPg = 0;

        foreach (($this->session->exam->questions ?? collect()) as $question) {
            if ($question->type === 'pg') {
                $totalPg++;
                $userAnswer = $this->answers[$question->id] ?? null;
                if ($userAnswer && strtoupper($userAnswer) === strtoupper($question->answer_key)) {
                    $correctPg++;
                    Answer::updateOrCreate(
                        ['exam_session_id' => $this->session->id, 'question_id' => $question->id],
                        ['answer' => $userAnswer, 'score' => 1]
                    );
                }
            }
        }

        $score = $totalPg > 0 ? round(($correctPg / $totalPg) * 100) : 0;
        $this->session->update(['score' => $score]);
    }

    public function autoSave()
    {
        // Triggered by wire:poll setiap 30 detik
    }

    public function render()
    {
        return view('livewire.student.exam-page')
            ->layout('components.layouts.exam', ['title' => $this->session->exam->title]);
    }
}