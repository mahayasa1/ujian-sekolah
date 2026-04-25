<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'subject_id', 'type', 'question', 'option_a', 'option_b',
        'option_c', 'option_d', 'option_e', 'answer_key', 'difficulty',
        'image', 'created_by'
    ];
 
    public function subject() { return $this->belongsTo(Subject::class); }
    public function exams()   { return $this->belongsToMany(Exam::class, 'exam_questions')->withPivot('order'); }
}