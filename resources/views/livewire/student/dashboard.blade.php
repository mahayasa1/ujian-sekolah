{{-- resources/views/livewire/student/dashboard.blade.php --}}
<x-layouts.digitest :title="'Dashboard Siswa'">

{{-- Greeting --}}
<div style="margin-bottom:1.5rem;">
    <h1 style="margin:0 0 0.25rem;font-size:1.25rem;font-weight:700;color:#1F2937;">
        Selamat Datang, {{ auth()->user()->name }}! 👋
    </h1>
    @php $student = auth()->user()->student; @endphp
    <p style="margin:0;font-size:0.875rem;color:#6B7280;">
        Kelas: <strong>{{ $student?->classRoom?->name ?? '-' }}</strong>
        &nbsp;·&nbsp; NIS: <strong>{{ $student?->nis ?? '-' }}</strong>
    </p>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.75rem;">
    <div class="digi-stat">
        <div class="digi-stat-value">{{ $exams->count() }}</div>
        <div class="digi-stat-label">Ujian Tersedia</div>
    </div>
    <div class="digi-stat">
        <div class="digi-stat-value">{{ $completedSessions->count() }}</div>
        <div class="digi-stat-label">Ujian Selesai</div>
    </div>
    <div class="digi-stat">
        <div class="digi-stat-value">
            {{ $completedSessions->count() > 0 ? round($completedSessions->avg('score')) : '-' }}
        </div>
        <div class="digi-stat-label">Rata-rata Nilai</div>
    </div>
</div>

{{-- Available Exams --}}
<div style="margin-bottom:1.75rem;">
    <h2 style="font-size:1rem;font-weight:700;color:#1F2937;margin:0 0 1rem;">
        📚 Mata Pelajaran & Ujian Tersedia
    </h2>

    @if($exams->isEmpty())
    <div style="background:white;border:1px solid #E5E7EB;border-radius:0.75rem;padding:3rem;text-align:center;color:#9CA3AF;">
        <div style="font-size:2.5rem;margin-bottom:0.75rem;">📭</div>
        <p style="margin:0;font-weight:500;">Belum ada ujian aktif untuk kelas Anda</p>
    </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;">
        @foreach($exams as $exam)
        @php
            $session = $exam->session;
            $isDone  = $session && $session->status === 'selesai';
            $isOngoing = $session && $session->status === 'aktif';
        @endphp
        <div class="digi-card-subject">
            {{-- Subject icon --}}
            <div class="digi-subject-icon">
                @php
                    $icons = ['Matematika'=>'➕','IPA'=>'🔬','IPS'=>'🌍','B. Indonesia'=>'📝','B. Inggris'=>'🇬🇧','PPKn'=>'🏛️','Agama'=>'🕌','Seni'=>'🎨','Olahraga'=>'⚽'];
                    $icon = $icons[$exam->subject->name] ?? '📘';
                @endphp
                {{ $icon }}
            </div>

            <h3 style="margin:0 0 0.25rem;font-size:0.9rem;font-weight:700;color:#1F2937;">
                {{ $exam->subject->name }}
            </h3>
            <p style="margin:0 0 0.75rem;font-size:0.78rem;color:#6B7280;">
                {{ $exam->title }}
            </p>

            <div style="display:flex;gap:0.5rem;align-items:center;justify-content:center;margin-bottom:1rem;font-size:0.75rem;color:#6B7280;">
                <span>⏱ {{ $exam->duration }} menit</span>
                <span>·</span>
                <span>📝 {{ $exam->total_questions }} soal</span>
            </div>

            @if($isDone)
                <div class="badge-selesai" style="display:block;margin-bottom:0.5rem;">✅ Selesai</div>
                <div style="font-size:0.875rem;font-weight:700;color:#C0392B;">
                    Nilai: {{ $session->score ?? '-' }}
                </div>
                <a href="{{ route('student.result', $session->id) }}" class="btn-digi-outline" style="margin-top:0.75rem;width:100%;text-align:center;display:block;font-size:0.8rem;">
                    Lihat Hasil
                </a>
            @elseif($isOngoing)
                <div class="badge-aktif" style="display:block;margin-bottom:0.75rem;">▶ Sedang Berlangsung</div>
                <a href="{{ route('student.exam', $session->id) }}" class="btn-digi-primary" style="width:100%;justify-content:center;display:flex;font-size:0.8rem;">
                    Lanjutkan Ujian
                </a>
            @else
                <a href="{{ route('student.token', $exam->id) }}" class="btn-digi-primary" style="width:100%;justify-content:center;display:flex;font-size:0.8rem;">
                    🔑 Mulai Ujian
                </a>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Completed exams --}}
@if($completedSessions->isNotEmpty())
<div>
    <h2 style="font-size:1rem;font-weight:700;color:#1F2937;margin:0 0 1rem;">
        🏆 Riwayat Ujian
    </h2>
    <div class="digi-card" style="padding:0;overflow:hidden;">
        <table class="digi-table">
            <thead>
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Judul Ujian</th>
                    <th>Tanggal</th>
                    <th>Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completedSessions as $ses)
                <tr>
                    <td style="font-weight:600;">{{ $ses->exam->subject->name }}</td>
                    <td>{{ $ses->exam->title }}</td>
                    <td>{{ $ses->submitted_at?->format('d M Y H:i') ?? '-' }}</td>
                    <td>
                        <span style="font-size:1.1rem;font-weight:700;color:{{ ($ses->score ?? 0) >= 75 ? '#27AE60' : '#C0392B' }};">
                            {{ $ses->score ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('student.result', $ses->id) }}" style="color:#C0392B;font-size:0.8rem;font-weight:600;text-decoration:none;">
                            Detail →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

</x-layouts.digitest>
