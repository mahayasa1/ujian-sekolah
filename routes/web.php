<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Models\Subject;

Route::view('/', 'auth.login')->name('home');

// ============================================================
// REDIRECT after login based on role
// ============================================================
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru'  => redirect()->route('teacher.dashboard'),
        default => redirect()->route('student.dashboard'),
    };
})->name('dashboard');

// ============================================================
// STUDENT ROUTES
// ============================================================
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {

    Route::get('/dashboard', \App\Livewire\Student\Dashboard::class)->name('dashboard');

    Route::get('/exam/{session}', \App\Livewire\Student\ExamPage::class)->name('exam');

    Route::get('/result/{session}', \App\Livewire\Student\Result::class)->name('result');

    Route::get('/results', function () {
        $student  = auth()->user()->student;
        $sessions = \App\Models\ExamSession::with(['exam.subject'])
            ->where('student_id', $student?->id)
            ->where('status', 'selesai')
            ->latest()
            ->paginate(15);
        return view('livewire.student.results-list', compact('sessions'))
            ->layout('components.layouts.digitest', ['title' => 'Hasil Ujian Saya']);
    })->name('results');
});

// ============================================================
// TEACHER ROUTES
// ============================================================
Route::middleware(['auth', 'verified'])->prefix('teacher')->name('teacher.')->group(function () {

    Route::get('/dashboard', \App\Livewire\Teacher\Dashboard::class)->name('dashboard');

    Route::get('/subject/{subject}', function (Subject $subject) {
        if ($subject->teacher_id !== auth()->user()->teacher?->id && !auth()->user()->isAdmin()) {
            abort(403);
        }
        return view('livewire.teacher.subject', compact('subject'))
            ->layout('components.layouts.digitest', ['title' => $subject->name]);
    })->name('subject');

    Route::get('/monitor/{exam}', \App\Livewire\Teacher\ExamMonitor::class)->name('monitor');
});

// ============================================================
// ADMIN ROUTES
// ============================================================
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');

    // ── Guru (dedicated page) ────────────────────────────────
    Route::get('/teachers', \App\Livewire\Admin\Teachers::class)->name('teachers');

    // ── Siswa (dedicated page) ───────────────────────────────
    Route::get('/students', \App\Livewire\Admin\Students::class)->name('students');

    // ── Mata Pelajaran ───────────────────────────────────────
    Route::get('/subjects', \App\Livewire\Admin\Subjects::class)->name('subjects');

    // ── Kelas ────────────────────────────────────────────────
    // Route::get('/classes', \App\Livewire\Admin\Classes::class)->name('classes');

    // ── Users (general, kept for backward compat) ────────────
    Route::get('/users', \App\Livewire\Admin\Users::class)->name('users');

    // ── Legacy data-guru redirect ────────────────────────────
    Route::redirect('/data-guru',  '/admin/teachers')->name('data_guru');

    // ── Legacy data-siswa redirect ───────────────────────────
    Route::redirect('/data-siswa', '/admin/students')->name('data_siswa');
});

require __DIR__.'/settings.php';