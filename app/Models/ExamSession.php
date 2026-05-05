<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamSession extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'started_at', 'submitted_at',
        'status', 'score',
        'reentry_token', 'last_violation_at', 'remaining_seconds', 'ends_at',
    ];
    
    protected $casts = [
        'started_at'        => 'datetime',
        'submitted_at'      => 'datetime',
        'last_violation_at' => 'datetime',
        'ends_at'           => 'datetime',
    ];

    public function exam()       { return $this->belongsTo(Exam::class); }
    public function student()    { return $this->belongsTo(Student::class); }
    public function answers()    { return $this->hasMany(Answer::class); }
    public function violations() { return $this->hasMany(Violation::class); }

    public function getAnswerFor(int $questionId): ?Answer
    {
        return $this->answers->firstWhere('question_id', $questionId);
    }

    /**
     * 🔥 CORE TIMER (FIX FINAL)
     */
    public function getTimeLeftSeconds(): int
    {
        if (!$this->started_at) return 0;

        // ✅ MODE: setelah violation (freeze system)
        if ($this->remaining_seconds !== null && $this->last_violation_at) {

            // ❗ HARUS pakai timestamp, bukan diffInSeconds
            $elapsed = now()->timestamp - $this->last_violation_at->timestamp;

            return max(0, $this->remaining_seconds - $elapsed);
        }

        // ✅ MODE: normal
        $totalSeconds = $this->exam->duration * 60;

        // ❗ FIX: pakai timestamp juga
        $elapsed = max(0, now()->timestamp - $this->started_at->timestamp);

        return max(0, $totalSeconds - $elapsed);
    }

    /**
     * Untuk JS timer (frontend)
     */
    public function getEndTimestamp(): int
    {
        // Kalau sudah pernah diset, pakai itu
        if ($this->ends_at) {
            return $this->ends_at->timestamp;
        }

        // Kalau belum, generate SEKALI
        $end = $this->started_at->copy()->addMinutes($this->exam->duration);

        // Simpan ke DB supaya FIX
        $this->update(['ends_at' => $end]);

        return $end->timestamp;
    }

    /**
     * 🔒 Saat violation → FREEZE waktu
     */
    public function snapshotRemainingTime(): void
    {
        // ❗ Jangan overwrite kalau sudah pernah snapshot
        if ($this->reentry_token) return;

        $secondsLeft = $this->getTimeLeftSeconds();

        $this->update([
            'remaining_seconds' => $secondsLeft,
            'last_violation_at' => now(),
            'ends_at'           => null, // ❗ disable old logic
        ]);
    }

    /**
     * 🔓 Saat re-entry → lanjutkan timer
     */
    public function processReentry(): void
    {
        $this->update([
            'reentry_token'     => null,
            'last_violation_at' => now(), // ❗ RESET basis waktu
        ]);
    }

    /**
     * 🔑 Generate token
     */
    public function generateReentryToken(): string
    {
        $token = strtoupper(
            substr(md5($this->id . '-' . $this->student_id . '-' . Str::random(16)), 0, 8)
        );

        $this->update(['reentry_token' => $token]);

        return $token;
    }

    public function clearReentryToken(): void
    {
        $this->update(['reentry_token' => null]);
    }
}