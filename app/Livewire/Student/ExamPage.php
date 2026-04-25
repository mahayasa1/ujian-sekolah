<?php

namespace App\Livewire\Student;

use App\Models\Answer;
use App\Models\Violation;
use App\Models\ExamSession;
use Livewire\Component;

class ExamPage extends Component
{
    public ExamSession $session;
    public int $currentIndex = 0;
    public array $answers = [];
    public int $violationCount = 0;
    public bool $showSubmitConfirm = false;
 
    public function mount(ExamSession $session)
    {
        if ($session->student_id !== auth()->user()->student?->id) {
            abort(403);
        }
        if ($session->status === 'selesai') {
            return $this->redirect(route('student.result', $session->id), navigate: true);
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
        $questions = $this->session->exam->questions;
        if ($this->session->exam->random_question) {
            return $questions->shuffle();
        }
        return $questions;
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
 
    public function reportViolation(string $type)
    {
        Violation::create([
            'exam_session_id' => $this->session->id,
            'type'            => $type,
            'description'     => 'Pelanggaran otomatis: ' . $type,
        ]);
 
        $this->violationCount++;
 
        if ($this->violationCount >= 3) {
            $this->autoSubmit();
        }
    }
 
    public function confirmSubmit()
    {
        $this->showSubmitConfirm = true;
    }
 
    public function submit()
    {
        $this->autoScore();
        $this->session->update([
            'status'       => 'selesai',
            'submitted_at' => now(),
        ]);
 
        return $this->redirect(route('student.result', $this->session->id), navigate: true);
    }
 
    private function autoSubmit()
    {
        $this->autoScore();
        $this->session->update([
            'status'       => 'selesai',
            'submitted_at' => now(),
        ]);
        $this->redirect(route('student.result', $this->session->id), navigate: true);
    }
 
    private function autoScore()
    {
        $totalPg  = 0;
        $correctPg = 0;
 
        foreach ($this->session->exam->questions as $question) {
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
        // Triggered by wire:poll every 30s
    }
 
    public function render()
    {
        return view('livewire.student.exam-page');
    }
}