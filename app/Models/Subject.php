<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'teacher_id'];
 
    public function teacher()   { return $this->belongsTo(Teacher::class); }
    public function questions() { return $this->hasMany(Question::class); }
    public function exams()     { return $this->hasMany(Exam::class); }
}