<?php

namespace App\Livewire\Student;

use App\Models\Exam;
use App\Models\ExamSession;
use Livewire\Component;

class ExamPopup extends Component
{
    // ID ujian yang sedang dipilih
    public ?int $examId = null;

    // Input token dari siswa
    public string $tokenInput = '';

    // Pesan error jika token salah / ujian tidak valid
    public string $tokenError = '';

    // Data ujian yang dipilih (untuk ditampilkan di popup)
    public ?Exam $exam = null;

    /**
     * Dipanggil dari parent (Dashboard) via event:
     *   $dispatch('open-exam-popup', { examId: 1 })
     */
    public function openPopup(int $examId): void
    {
        $this->examId     = $examId;
        $this->exam       = Exam::with(['subject', 'classRoom'])->find($examId);
        $this->tokenInput = '';
        $this->tokenError = '';
    }

    /**
     * Tutup popup & reset semua state
     */
    public function closePopup(): void
    {
        $this->reset(['examId', 'exam', 'tokenInput', 'tokenError']);
    }

    /**
     * Validasi token dan mulai sesi ujian
     */
    public function submitToken(): void
    {
        $this->tokenError = '';

        // 1. Pastikan ujian ditemukan
        if (!$this->exam) {
            $this->tokenError = 'Ujian tidak ditemukan.';
            return;
        }

        // 2. Validasi token (case-insensitive)
        if (strtoupper(trim($this->tokenInput)) !== strtoupper($this->exam->token)) {
            $this->tokenError = 'Token tidak valid. Periksa kembali token dari guru pengawas.';
            return;
        }

        // 3. Pastikan ujian sedang aktif (status aktif + dalam rentang waktu)
        if (!$this->exam->isActive()) {
            $this->tokenError = 'Ujian belum aktif atau sudah berakhir.';
            return;
        }

        // 4. Ambil data siswa yang sedang login
        $student = auth()->user()->student;
        if (!$student) {
            $this->tokenError = 'Data siswa tidak ditemukan. Hubungi admin.';
            return;
        }

        // 5. Cek apakah sudah ada sesi ujian sebelumnya
        $existing = ExamSession::where('exam_id', $this->exam->id)
            ->where('student_id', $student->id)
            ->first();

        // 6. Jika sudah selesai, larang masuk lagi
        if ($existing && $existing->status === 'selesai') {
            $this->tokenError = 'Anda sudah menyelesaikan ujian ini. Lihat hasil di riwayat ujian.';
            return;
        }

        // 7. Jika belum ada sesi, buat sesi baru
        if (!$existing) {
            $existing = ExamSession::create([
                'exam_id'    => $this->exam->id,
                'student_id' => $student->id,
                'status'     => 'aktif',
                'started_at' => now(),
            ]);
        }

        // 8. Jika sesi sudah ada tapi belum selesai (status aktif),
        //    langsung lanjutkan ke halaman ujian
        $this->closePopup();

        $this->redirect(
            route('student.exam', $existing->id),
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.student.exam-popup');
    }
}