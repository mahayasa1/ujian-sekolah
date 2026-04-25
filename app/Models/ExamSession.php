<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = ['exam_id', 'student_id', 'started_at', 'submitted_at', 'status', 'score'];
    protected $casts = ['started_at' => 'datetime', 'submitted_at' => 'datetime'];
 
    public function exam()      { return $this->belongsTo(Exam::class); }
    public function student()   { return $this->belongsTo(Student::class); }
    public function answers()   { return $this->hasMany(Answer::class); }
    public function violations(){ return $this->hasMany(Violation::class); }
 
    public function getAnswerFor(int $questionId): ?Answer
    {
        return $this->answers->firstWhere('question_id', $questionId);
    }
 
    public function getTimeLeftSeconds(): int
    {
        if (!$this->started_at) return 0;
        $end = $this->started_at->addMinutes($this->exam->duration);
        return max(0, now()->diffInSeconds($end, false));
    }
}