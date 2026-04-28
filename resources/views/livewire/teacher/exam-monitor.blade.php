{{-- resources/views/livewire/teacher/exam-monitor.blade.php --}}
{{-- FIX: Wrapped entire content in single <div> root element --}}
<div>

@php
    $exam           = $this->exam->load(['subject','classRoom','sessions.student.user','sessions.violations']);
    $sessions       = $exam->sessions;
    $ongoingCount   = $sessions->where('status','aktif')->count();
    $finishedCount  = $sessions->where('status','selesai')->count();
    $totalCount     = $sessions->count();
    $avgScore       = $sessions->where('status','selesai')->avg('score');
@endphp

{{-- Back button --}}
<a href="{{ route('teacher.subject', $exam->subject_id) }}?tab=ujian" wire:navigate
   style="display:inline-flex;align-items:center;gap:6px;color:#C0392B;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:14px;">
    ← Kembali ke Ujian
</a>

{{-- Header --}}
<div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:14px;">
    <div style="display:flex;align-items:start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="flex:1;min-width:0;">
            <div style="font-size:11px;color:#9CA3AF;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">MONITOR UJIAN</div>
            <h1 style="margin:0 0 4px;font-size:15px;font-weight:800;color:#1F2937;line-height:1.3;">{{ $exam->title }}</h1>
            <div style="font-size:12px;color:#6B7280;">{{ $exam->subject->name }} · {{ $exam->classRoom?->name ?? 'Semua Kelas' }}</div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
            <div style="background:#FDEDEC;border:2px solid #C0392B;padding:6px 12px;border-radius:8px;text-align:center;">
                <div style="font-size:9px;color:#C0392B;font-weight:700;text-transform:uppercase;">Token Ujian</div>
                <div style="font-size:18px;font-weight:800;color:#C0392B;letter-spacing:0.15em;font-family:monospace;">{{ $exam->token }}</div>
            </div>
            @if($exam->status==='aktif')
                <span class="badge-aktif">● AKTIF</span>
            @else
                <span class="badge-draft">{{ ucfirst($exam->status) }}</span>
            @endif
        </div>
    </div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin-bottom:14px;">
    <div style="background:white;border-radius:10px;padding:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border-left:4px solid #E67E22;text-align:center;">
        <div style="font-size:22px;font-weight:700;color:#E67E22;">{{ $ongoingCount }}</div>
        <div style="font-size:10px;color:#9CA3AF;font-weight:500;">Mengerjakan</div>
    </div>
    <div style="background:white;border-radius:10px;padding:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border-left:4px solid #27AE60;text-align:center;">
        <div style="font-size:22px;font-weight:700;color:#27AE60;">{{ $finishedCount }}</div>
        <div style="font-size:10px;color:#9CA3AF;font-weight:500;">Selesai</div>
    </div>
    <div style="background:white;border-radius:10px;padding:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border-left:4px solid #3498DB;text-align:center;">
        <div style="font-size:22px;font-weight:700;color:#3498DB;">{{ $totalCount }}</div>
        <div style="font-size:10px;color:#9CA3AF;font-weight:500;">Total Peserta</div>
    </div>
    <div style="background:white;border-radius:10px;padding:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border-left:4px solid #C0392B;text-align:center;">
        <div style="font-size:22px;font-weight:700;color:#C0392B;">{{ $avgScore ? round($avgScore) : '—' }}</div>
        <div style="font-size:10px;color:#9CA3AF;font-weight:500;">Rata-rata</div>
    </div>
</div>

{{-- Live monitor table --}}
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;" wire:poll.5000ms>
    <div style="padding:12px 14px;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:13px;font-weight:600;color:#374151;">👁️ Status Peserta <span style="font-size:11px;color:#9CA3AF;font-weight:400;">(auto-refresh 5 detik)</span></span>
        <div style="width:8px;height:8px;border-radius:50%;background:#27AE60;animation:pulse-dot 1.5s infinite;"></div>
    </div>

    @if($sessions->isEmpty())
    <div style="padding:2.5rem;text-align:center;color:#9CA3AF;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">👥</div>
        <p style="margin:0;font-size:13px;">Belum ada siswa yang bergabung</p>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:0;">
        @foreach($sessions->sortByDesc('started_at') as $i => $ses)
        @php
            $timeLeft = $ses->status === 'aktif' ? $ses->getTimeLeftSeconds() : null;
            $mins     = $timeLeft !== null ? floor($timeLeft / 60) : null;
            $secs     = $timeLeft !== null ? $timeLeft % 60 : null;
            $vCount   = $ses->violations->count();
            $isLocked = $ses->reentry_token !== null; // siswa sedang di-lock / di luar
        @endphp
        <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;border-bottom:0.5px solid #F3F4F6;{{ $vCount >= 2 ? 'background:#FFF5F5;' : '' }}{{ $isLocked ? 'background:#FFFBEB;' : '' }}">
            <div style="width:32px;height:32px;border-radius:50%;background:#F3F4F6;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#374151;flex-shrink:0;">
                {{ $i + 1 }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:#1F2937;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $ses->student?->user?->name ?? '—' }}
                </div>
                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                    @if($isLocked)
                        <span style="background:#FEF3C7;color:#92400E;padding:2px 6px;border-radius:999px;font-size:10px;font-weight:700;">🔒 Di Luar</span>
                        {{-- Tampilkan reentry token untuk guru sampaikan ke siswa --}}
                        <span style="font-family:monospace;font-size:11px;font-weight:700;color:#92400E;background:#FEF9C3;padding:2px 8px;border-radius:4px;letter-spacing:0.1em;">
                            RE-ENTRY: {{ $ses->reentry_token }}
                        </span>
                    @elseif($ses->status === 'aktif')
                        <span class="badge-aktif" style="font-size:10px;padding:2px 6px;">● Aktif</span>
                    @elseif($ses->status === 'selesai')
                        <span class="badge-selesai" style="font-size:10px;padding:2px 6px;">✓ Selesai</span>
                    @else
                        <span class="badge-draft" style="font-size:10px;padding:2px 6px;">Belum</span>
                    @endif

                    @if($vCount > 0)
                    <span style="background:#FDEDEC;color:#C0392B;padding:2px 6px;border-radius:999px;font-size:10px;font-weight:700;">⚠️ {{ $vCount }}x pelanggaran</span>
                    @endif

                    @if($ses->status === 'aktif' && $timeLeft !== null && !$isLocked)
                    <span style="font-family:monospace;font-size:11px;font-weight:700;color:{{ $timeLeft < 300 ? '#C0392B' : '#374151' }};">
                        ⏱ {{ sprintf('%02d:%02d', $mins, $secs) }}
                    </span>
                    @endif

                    @if($ses->last_violation_at && $isLocked)
                    <span style="font-size:10px;color:#9CA3AF;">Keluar: {{ $ses->last_violation_at->format('H:i:s') }}</span>
                    @endif
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                @if($ses->score !== null)
                <strong style="font-size:18px;color:{{ $ses->score >= 75 ? '#27AE60' : '#C0392B' }};">{{ $ses->score }}</strong>
                @else
                <span style="color:#D1D5DB;font-size:13px;">—</span>
                @endif
                <div style="font-size:10px;color:#9CA3AF;">{{ $ses->started_at?->format('H:i') ?? '—' }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<style>
@keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:0.4} }
</style>

</div>