{{-- resources/views/livewire/student/results-list.blade.php --}}
<div>
<div style="margin-bottom:1.25rem;">
    <h2 style="font-size:1rem;font-weight:700;color:#1F2937;margin:0 0 0.25rem;">📊 Semua Hasil Ujian</h2>
    <p style="margin:0;font-size:0.85rem;color:#6B7280;">Riwayat seluruh ujian yang telah kamu selesaikan</p>
</div>

@if($sessions->isEmpty())
<div style="background:white;border:1px solid #E5E7EB;border-radius:0.75rem;padding:3rem;text-align:center;color:#9CA3AF;">
    <div style="font-size:3rem;margin-bottom:0.75rem;">📭</div>
    <p style="margin:0 0 1rem;font-weight:500;">Kamu belum mengikuti ujian apapun</p>
    <a href="{{ route('student.dashboard') }}" wire:navigate class="btn-digi-primary" style="display:inline-flex;">
        → Lihat Ujian Tersedia
    </a>
</div>
@else
<div class="digi-card" style="padding:0;overflow:hidden;">
    <table class="digi-table">
        <thead>
            <tr>
                <th>Mata Pelajaran</th>
                <th>Judul Ujian</th>
                <th style="width:100px;">Tanggal</th>
                <th style="width:80px;text-align:center;">Nilai</th>
                <th style="width:80px;text-align:center;">Predikat</th>
                <th style="width:80px;text-align:center;">Status</th>
                <th style="width:80px;">Detail</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $ses)
            @php
                $score  = $ses->score ?? 0;
                $grade  = $score >= 90 ? 'A' : ($score >= 80 ? 'B' : ($score >= 75 ? 'C' : ($score >= 60 ? 'D' : 'E')));
                $passed = $score >= 75;
                $gc     = $passed ? '#27AE60' : '#C0392B';
            @endphp
            <tr>
                <td style="font-weight:600;font-size:0.875rem;">{{ $ses->exam->subject->name }}</td>
                <td style="font-size:0.85rem;color:#374151;">{{ Str::limit($ses->exam->title, 50) }}</td>
                <td style="font-size:0.78rem;color:#6B7280;">{{ $ses->submitted_at?->format('d/m/Y') }}</td>
                <td style="text-align:center;">
                    <strong style="font-size:1.1rem;color:{{ $gc }};">{{ $score }}</strong>
                </td>
                <td style="text-align:center;">
                    <span style="background:{{ $passed ? '#D5F5E3' : '#FDEDEC' }};color:{{ $gc }};padding:0.2rem 0.5rem;border-radius:999px;font-size:0.8rem;font-weight:700;">
                        {{ $grade }}
                    </span>
                </td>
                <td style="text-align:center;">
                    @if($passed)
                        <span class="badge-aktif" style="font-size:0.7rem;">Lulus</span>
                    @else
                        <span style="background:#FDEDEC;color:#C0392B;padding:0.2rem 0.5rem;border-radius:999px;font-size:0.7rem;font-weight:600;">Remidi</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('student.result', $ses->id) }}" wire:navigate
                       style="color:#C0392B;font-size:0.8rem;font-weight:600;text-decoration:none;">
                        Lihat →
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($sessions->hasPages())
    <div style="padding:0.875rem 1.25rem;border-top:1px solid #F3F4F6;">
        {{ $sessions->links() }}
    </div>
    @endif
</div>
@endif
</div>

---

# PANDUAN INSTALASI DIGITEST SELSA
# Simpan sebagai: INSTALL.md

## Struktur File — Taruh di Mana?

```
digitest.css         → resources/css/digitest.css
migrations/...       → database/migrations/
models/User.php      → app/Models/User.php  (replace)
models/AllModels.php → pisahkan menjadi file terpisah:
                       app/Models/Teacher.php
                       app/Models/Student.php
                       app/Models/ClassRoom.php
                       app/Models/Subject.php
                       app/Models/Question.php
                       app/Models/Exam.php
                       app/Models/ExamQuestion.php
                       app/Models/ExamSession.php
                       app/Models/Answer.php
                       app/Models/Violation.php

livewire/StudentComponents.php → pisahkan:
  app/Livewire/Student/Dashboard.php
  app/Livewire/Student/TokenEntry.php
  app/Livewire/Student/ExamPage.php
  app/Livewire/Student/Result.php

livewire/TeacherComponents.php → pisahkan:
  app/Livewire/Teacher/Dashboard.php
  app/Livewire/Teacher/QuestionBank.php
  app/Livewire/Teacher/ExamManager.php
  app/Livewire/Teacher/ExamMonitor.php
  app/Livewire/Teacher/ExamResults.php

views/layouts/digitest.blade.php    → resources/views/layouts/digitest.blade.php
views/layouts/partials.blade.php    → pisahkan:
  resources/views/layouts/partials/student-nav.blade.php
  resources/views/layouts/partials/teacher-nav.blade.php
  resources/views/layouts/partials/admin-nav.blade.php

views/auth/login.blade.php          → resources/views/pages/auth/login.blade.php
views/student/dashboard.blade.php   → resources/views/livewire/student/dashboard.blade.php
views/student/token-entry.blade.php → resources/views/livewire/student/token-entry.blade.php
views/student/exam-page.blade.php   → resources/views/livewire/student/exam-page.blade.php
views/student/result.blade.php      → resources/views/livewire/student/result.blade.php
views/teacher/dashboard.blade.php   → resources/views/livewire/teacher/dashboard.blade.php
views/teacher/subject.blade.php     → resources/views/livewire/teacher/subject.blade.php
views/teacher/question-bank.blade.php → resources/views/livewire/teacher/question-bank.blade.php
views/teacher/exam-manager.blade.php  → resources/views/livewire/teacher/exam-manager.blade.php
views/teacher/exam-monitor.blade.php  → resources/views/livewire/teacher/exam-monitor.blade.php
views/teacher/exam-results.blade.php  → resources/views/livewire/teacher/exam-results.blade.php
routes/web.php      → routes/web.php  (replace)
seeders/DigiTestSeeder.php → database/seeders/DigiTestSeeder.php
```

## resources/css/app.css — Tambahkan import:
```css
@import 'tailwindcss';
@import './digitest.css';   ← TAMBAHKAN INI
@import '../../vendor/livewire/flux/dist/flux.css';
```

## Jalankan migrasi & seeder:
```bash
php artisan migrate
php artisan db:seed --class=DigiTestSeeder
```

## Login Demo:
| Role  | Email                        | Password  |
|-------|------------------------------|-----------|
| Admin | admin@digitest.sch.id        | password  |
| Guru  | guru.ipa@digitest.sch.id     | password  |
| Siswa | siswa@digitest.sch.id        | password  |

## Token Ujian Demo: IPA001
