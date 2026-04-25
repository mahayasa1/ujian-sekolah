{{-- resources/views/livewire/teacher/dashboard.blade.php --}}
<x-layouts.digitest :title="'Dashboard Guru'">

@php $teacher = auth()->user()->teacher; @endphp

{{-- Greeting --}}
<div style="margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 style="margin:0 0 0.25rem;font-size:1.25rem;font-weight:700;color:#1F2937;">
            Selamat Datang, {{ auth()->user()->name }}! 👨‍🏫
        </h1>
        <p style="margin:0;font-size:0.875rem;color:#6B7280;">
            NIP: <strong>{{ $teacher?->nip ?? '-' }}</strong>
            &nbsp;·&nbsp; {{ now()->isoFormat('dddd, D MMMM Y') }}
        </p>
    </div>
    <div style="font-size:0.85rem;color:#6B7280;background:white;border:1px solid #E5E7EB;padding:0.5rem 1rem;border-radius:0.5rem;">
        🕐 {{ now()->format('H:i') }} WITA
    </div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.75rem;">
    <div class="digi-stat" style="border-left:4px solid #C0392B;">
        <div class="digi-stat-value">{{ $subjects->count() }}</div>
        <div class="digi-stat-label">Mata Pelajaran</div>
    </div>
    <div class="digi-stat" style="border-left:4px solid #27AE60;">
        <div class="digi-stat-value" style="color:#27AE60;">{{ $activeExams->count() }}</div>
        <div class="digi-stat-label">Ujian Aktif</div>
    </div>
    <div class="digi-stat" style="border-left:4px solid #E67E22;">
        <div class="digi-stat-value" style="color:#E67E22;">
            {{ $activeExams->sum(fn($e) => $e->sessions->where('status','aktif')->count()) }}
        </div>
        <div class="digi-stat-label">Siswa Mengerjakan</div>
    </div>
    <div class="digi-stat" style="border-left:4px solid #7D3C98;">
        <div class="digi-stat-value" style="color:#7D3C98;">
            {{ $activeExams->sum(fn($e) => $e->sessions->where('status','selesai')->count()) }}
        </div>
        <div class="digi-stat-label">Sudah Selesai</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    {{-- LEFT: Mata Pelajaran --}}
    <div>
        <h2 style="font-size:1rem;font-weight:700;color:#1F2937;margin:0 0 1rem;display:flex;align-items:center;gap:0.5rem;">
            📚 Mata Pelajaran Saya
        </h2>

        @if($subjects->isEmpty())
        <div style="background:white;border:1px solid #E5E7EB;border-radius:0.75rem;padding:2rem;text-align:center;color:#9CA3AF;">
            <div style="font-size:2rem;margin-bottom:0.5rem;">📭</div>
            <p style="margin:0;font-size:0.85rem;">Belum ada mata pelajaran. Hubungi admin.</p>
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            @foreach($subjects as $subject)
            @php
                $totalQ   = $subject->questions()->count();
                $totalE   = $subject->exams()->count();
                $activeE  = $subject->exams()->where('status','aktif')->count();
                $icons    = ['Matematika'=>'➕','IPA'=>'🔬','IPS'=>'🌍','B. Indonesia'=>'📝','B. Inggris'=>'🇬🇧','PPKn'=>'🏛️','Agama'=>'🕌','Seni'=>'🎨','Olahraga'=>'⚽'];
                $icon     = $icons[$subject->name] ?? '📘';
            @endphp
            <div class="digi-card" style="padding:1rem;border-left:4px solid #C0392B;cursor:pointer;" onclick="window.location='{{ route('teacher.subject', $subject->id) }}'">
                <div style="display:flex;align-items:center;gap:0.875rem;">
                    <div style="width:44px;height:44px;border-radius:0.75rem;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:1.35rem;flex-shrink:0;">
                        {{ $icon }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:700;color:#1F2937;font-size:0.9rem;">{{ $subject->name }}</div>
                        <div style="font-size:0.75rem;color:#9CA3AF;margin-top:0.15rem;">
                            {{ $totalQ }} soal &nbsp;·&nbsp; {{ $totalE }} ujian
                            @if($activeE > 0)
                            &nbsp;·&nbsp; <span style="color:#27AE60;font-weight:600;">{{ $activeE }} aktif</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('teacher.subject', $subject->id) }}" wire:navigate style="color:#C0392B;font-size:1.25rem;text-decoration:none;">→</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- RIGHT: Active Exams --}}
    <div>
        <h2 style="font-size:1rem;font-weight:700;color:#1F2937;margin:0 0 1rem;display:flex;align-items:center;gap:0.5rem;">
            🔴 Ujian Berlangsung
        </h2>

        @if($activeExams->isEmpty())
        <div style="background:white;border:1px solid #E5E7EB;border-radius:0.75rem;padding:2rem;text-align:center;color:#9CA3AF;">
            <div style="font-size:2rem;margin-bottom:0.5rem;">✅</div>
            <p style="margin:0;font-size:0.85rem;">Tidak ada ujian yang sedang berlangsung</p>
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            @foreach($activeExams as $exam)
            @php
                $ongoingCount  = $exam->sessions->where('status','aktif')->count();
                $finishedCount = $exam->sessions->where('status','selesai')->count();
            @endphp
            <div class="digi-card" style="padding:1rem;border-left:4px solid #27AE60;">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:0.75rem;">
                    <div>
                        <div style="font-weight:700;font-size:0.9rem;color:#1F2937;">{{ $exam->title }}</div>
                        <div style="font-size:0.75rem;color:#9CA3AF;">{{ $exam->subject->name }} &nbsp;·&nbsp; {{ $exam->classRoom?->name ?? 'Semua Kelas' }}</div>
                    </div>
                    <span class="badge-aktif">AKTIF</span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.5rem;margin-bottom:0.75rem;">
                    <div style="text-align:center;background:#F9FAFB;border-radius:0.4rem;padding:0.4rem;">
                        <div style="font-size:1rem;font-weight:700;color:#E67E22;">{{ $ongoingCount }}</div>
                        <div style="font-size:0.65rem;color:#9CA3AF;">Mengerjakan</div>
                    </div>
                    <div style="text-align:center;background:#F9FAFB;border-radius:0.4rem;padding:0.4rem;">
                        <div style="font-size:1rem;font-weight:700;color:#27AE60;">{{ $finishedCount }}</div>
                        <div style="font-size:0.65rem;color:#9CA3AF;">Selesai</div>
                    </div>
                    <div style="text-align:center;background:#F9FAFB;border-radius:0.4rem;padding:0.4rem;">
                        <div style="font-size:0.85rem;font-weight:700;color:#C0392B;font-family:monospace;">{{ $exam->token }}</div>
                        <div style="font-size:0.65rem;color:#9CA3AF;">Token</div>
                    </div>
                </div>

                <a href="{{ route('teacher.monitor', $exam->id) }}" wire:navigate class="btn-digi-outline" style="width:100%;text-align:center;display:block;font-size:0.8rem;">
                    👁️ Monitor Ujian
                </a>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Recent exams --}}
        <h2 style="font-size:1rem;font-weight:700;color:#1F2937;margin:1.5rem 0 1rem;display:flex;align-items:center;gap:0.5rem;">
            📋 Ujian Terbaru
        </h2>
        <div class="digi-card" style="padding:0;overflow:hidden;">
            <table class="digi-table">
                <thead>
                    <tr>
                        <th>Ujian</th>
                        <th>Status</th>
                        <th>Token</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentExams as $exam)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('teacher.subject', $exam->subject_id) }}'">
                        <td>
                            <div style="font-weight:600;font-size:0.82rem;">{{ Str::limit($exam->title, 28) }}</div>
                            <div style="font-size:0.72rem;color:#9CA3AF;">{{ $exam->subject->name }}</div>
                        </td>
                        <td>
                            @if($exam->status === 'aktif')
                                <span class="badge-aktif">Aktif</span>
                            @elseif($exam->status === 'selesai')
                                <span class="badge-selesai">Selesai</span>
                            @else
                                <span class="badge-draft">Draft</span>
                            @endif
                        </td>
                        <td>
                            <code style="font-size:0.8rem;font-weight:700;color:#C0392B;background:#FDEDEC;padding:0.15rem 0.4rem;border-radius:0.3rem;">
                                {{ $exam->token }}
                            </code>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:#9CA3AF;font-size:0.85rem;">Belum ada ujian dibuat</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

</x-layouts.digitest>
