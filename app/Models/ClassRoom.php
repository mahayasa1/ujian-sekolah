<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = ['name', 'grade'];
 
    public function students() { return $this->hasMany(Student::class); }
    public function exams()    { return $this->hasMany(Exam::class); }
}