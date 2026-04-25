<?php

namespace App\Livewire\Student;
 
use App\Models\Exam;
use App\Models\ExamSession;
use Livewire\Component;

class TokenEntry extends Component
{
    public Exam $exam;
    public string $token = '';
    public string $error = '';
 
    public function submit()
    {
        $this->error = '';
        if (strtoupper($this->token) !== strtoupper($this->exam->token)) {
            $this->error = 'Token tidak valid. Periksa kembali token dari guru Anda.';
            return;
        }
        if (!$this->exam->isActive()) {
            $this->error = 'Ujian belum aktif atau sudah berakhir.';
            return;
        }
 
        $student = auth()->user()->student;
        $existing = ExamSession::where('exam_id', $this->exam->id)
            ->where('student_id', $student->id)->first();
 
        if ($existing && $existing->status === 'selesai') {
            $this->error = 'Anda sudah menyelesaikan ujian ini.';
            return;
        }
 
        if (!$existing) {
            $existing = ExamSession::create([
                'exam_id'    => $this->exam->id,
                'student_id' => $student->id,
                'status'     => 'aktif',
                'started_at' => now(),
            ]);
        }
 
        return $this->redirect(route('student.exam', $existing->id), navigate: true);
    }
 
    public function render()
    {
        return view('livewire.student.token-entry');
    }
}