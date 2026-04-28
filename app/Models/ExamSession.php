<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamSession extends Model
{
    protected $fillable = [
        'exam_id', 'student_id', 'started_at', 'submitted_at',
        'status', 'score',
        'reentry_token', 'last_violation_at', 'remaining_seconds',
    ];

    protected $casts = [
        'started_at'       => 'datetime',
        'submitted_at'     => 'datetime',
        'last_violation_at'=> 'datetime',
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
     * Hitung sisa waktu dalam detik berdasarkan server time.
     * Jika ada remaining_seconds (disimpan saat violation), pakai itu sebagai basis.
     */
    public function getTimeLeftSeconds(): int
    {
        if (!$this->started_at) return 0;

        $totalDurationSeconds = $this->exam->duration * 60;

        // Jika ada snapshot remaining_seconds (setelah violation/reentry),
        // sisa waktu = remaining_seconds - waktu yang sudah berlalu sejak last_violation_at
        if ($this->remaining_seconds !== null && $this->last_violation_at) {
            $elapsedSinceViolation = now()->diffInSeconds($this->last_violation_at, false);
            // elapsedSinceViolation bisa negatif (masa lalu), kita perlu detik berlalu
            $elapsed = (int) now()->diffInSeconds($this->last_violation_at);
            $remaining = max(0, $this->remaining_seconds - $elapsed);
            return $remaining;
        }

        // Normal: hitung dari started_at
        $elapsed = (int) now()->diffInSeconds($this->started_at);
        return max(0, $totalDurationSeconds - $elapsed);
    }

    /**
     * Hitung Unix timestamp kapan ujian berakhir (untuk countdown di client).
     */
    public function getEndTimestamp(): int
    {
        $timeLeft = $this->getTimeLeftSeconds();
        return now()->addSeconds($timeLeft)->timestamp;
    }

    /**
     * Generate dan simpan reentry token baru.
     * Format: session_id + student_id + random = lebih aman dari token global
     */
    public function generateReentryToken(): string
    {
        $token = strtoupper(
            substr(md5($this->id . '-' . $this->student_id . '-' . Str::random(16)), 0, 8)
        );
        $this->update(['reentry_token' => $token]);
        return $token;
    }

    /**
     * Simpan snapshot waktu tersisa saat violation terjadi.
     */
    public function snapshotRemainingTime(): void
    {
        $this->update([
            'remaining_seconds'  => $this->getTimeLeftSeconds(),
            'last_violation_at'  => now(),
        ]);
    }

    /**
     * Reset reentry token setelah berhasil masuk kembali.
     */
    public function clearReentryToken(): void
    {
        $this->update(['reentry_token' => null]);
    }
}