<?php

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

    // Halaman ujian (token diinput via popup di dashboard)
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

    Route::get('/exam/{exam}/results', \App\Livewire\Teacher\ExamResults::class)->name('exam-results');
});

// ============================================================
// ADMIN ROUTES
// ============================================================
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/users', \App\Livewire\Admin\Users::class)->name('users');
    Route::get('/subjects', \App\Livewire\Admin\Subjects::class)->name('subjects');
    Route::get('/classes', \App\Livewire\Admin\Classes::class)->name('classes');

    // Data Guru — tampil list user role guru
    Route::get('/data-guru', function () {
        $teachers = \App\Models\User::where('role', 'guru')
            ->with('teacher')
            ->latest()
            ->get();
        return view('livewire.admin.data_guru', compact('teachers'))
            ->layout('components.layouts.digitest', ['title' => 'Data Guru']);
    })->name('data_guru');
});

require __DIR__.'/settings.php';