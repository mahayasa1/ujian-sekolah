<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $fillable = ['exam_session_id', 'type', 'description'];
 
    public function examSession() { return $this->belongsTo(ExamSession::class); }
}