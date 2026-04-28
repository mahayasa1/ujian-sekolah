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
        'started_at'        => 'datetime',
        'submitted_at'      => 'datetime',
        'last_violation_at' => 'datetime',
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

        $totalSeconds = $this->exam->duration * 60;

        if ($this->remaining_seconds !== null && $this->last_violation_at) {
            $elapsed = (int) now()->diffInSeconds($this->last_violation_at);
            return max(0, $this->remaining_seconds - $elapsed);
        }

        $elapsed = (int) now()->diffInSeconds($this->started_at);
        return max(0, $totalSeconds - $elapsed);
    }

    /**
     * Unix timestamp kapan ujian berakhir — dipakai timer JS di client.
     */
    public function getEndTimestamp(): int
    {
        return now()->addSeconds($this->getTimeLeftSeconds())->timestamp;
    }

    /**
     * Dipanggil saat pelanggaran terjadi.
     * Simpan sisa waktu TEPAT saat ini + catat waktu snapshot.
     */
    public function snapshotRemainingTime(): void
    {
        $this->update([
            'remaining_seconds' => $this->getTimeLeftSeconds(),
            'last_violation_at' => now(),
        ]);
    }

    /**
     * Dipanggil saat re-entry berhasil (token valid, siswa masuk kembali).
     *
     * KUNCI: last_violation_at di-reset ke NOW().
     * Ini membuat getTimeLeftSeconds() menghitung elapsed dari titik ini,
     * bukan dari saat violation — sehingga timer tidak "melompat" saat refresh.
     *
     * remaining_seconds TETAP (tidak diubah) = sisa waktu saat violation.
     */
    public function processReentry(): void
    {
        $this->update([
            'reentry_token'     => null,
            'last_violation_at' => now(), // basis baru → timer lanjut dari sini
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