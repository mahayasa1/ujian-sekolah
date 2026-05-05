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
     * Hitung sisa waktu dalam detik.
     *
     * DUA MODE:
     *
     * Mode A — Normal (belum pernah violation):
     *   remaining = total_durasi - (now - started_at)
     *
     * Mode B — Setelah violation/re-entry (ada remaining_seconds):
     *   remaining = remaining_seconds - (now - last_violation_at)
     *
     *   last_violation_at di-update DUA kali:
     *     1. Saat violation terjadi   → snapshot waktu tersisa ke remaining_seconds
     *     2. Saat re-entry berhasil   → reset basis agar timer lanjut dari titik re-entry
     *
     *   Dengan ini, setiap refresh halaman akan menghitung:
     *     elapsed = now - last_violation_at (= sejak siswa masuk kembali)
     *     sisa    = remaining_seconds - elapsed  ✓ BENAR
     */
    public function getTimeLeftSeconds(): int
    {
        if (!$this->started_at) return 0;
    
        // Pakai ends_at absolut jika ada
        if ($this->ends_at) {
            $diff = now()->diffInSeconds($this->ends_at, false);
            return max(0, (int) $diff);
        }
    
        // Normal: hitung dari started_at
        $totalSeconds = $this->exam->duration * 60;
        $elapsed = (int) now()->diffInSeconds($this->started_at);
        return max(0, $totalSeconds - $elapsed);
    }
    
    public function getEndTimestamp(): int
    {
        if ($this->ends_at) {
            return $this->ends_at->timestamp;
        }
        return now()->addSeconds($this->getTimeLeftSeconds())->timestamp;
    }
    
    /**
     * Saat violation: snapshot sisa waktu sebagai ends_at absolut
     * ends_at = now + sisa_waktu → FREEZE di titik ini
     */
    public function snapshotRemainingTime(): void
    {
        $secondsLeft = $this->getTimeLeftSeconds();
        $this->update([
            'remaining_seconds' => $secondsLeft,
            'last_violation_at' => now(),
            'ends_at'           => now()->addSeconds($secondsLeft), // ← FREEZE
        ]);
    }
    
    /**
     * Saat re-entry: ends_at TIDAK berubah
     * Timer lanjut dari ends_at yang sudah di-freeze
     */
    public function processReentry(): void
    {
        $this->update([
            'reentry_token' => null,
            // ends_at tetap, last_violation_at tidak perlu di-update
        ]);
    }

    /**
     * Generate dan simpan reentry token baru.
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
     * @deprecated Pakai processReentry() agar last_violation_at juga diupdate.
     */
    public function clearReentryToken(): void
    {
        $this->update(['reentry_token' => null]);
    }
}