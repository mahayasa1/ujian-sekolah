<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'nis', 'class_room_id'];
 
    public function user()      { return $this->belongsTo(User::class); }
    public function classRoom() { return $this->belongsTo(ClassRoom::class); }
    public function examSessions() { return $this->hasMany(ExamSession::class); }
}