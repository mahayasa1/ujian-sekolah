<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'title', 'subject_id', 'class_room_id', 'duration',
        'start_at', 'end_at', 'token', 'status',
        'google_form_url', 'created_by'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function subject()   { return $this->belongsTo(Subject::class); }
    public function classRoom() { return $this->belongsTo(ClassRoom::class); }
    public function sessions()  { return $this->hasMany(ExamSession::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }

    public function isActive(): bool
    {
        return $this->status === 'aktif'
            && ($this->start_at === null || $this->start_at->lte(now()))
            && ($this->end_at === null || $this->end_at->gte(now()));
    }
}