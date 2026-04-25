{{-- resources/views/livewire/teacher/exam-monitor.blade.php --}}
<x-layouts.digitest :title="'Monitor Ujian'">

@php
    $exam           = $this->exam->load(['subject','classRoom','sessions.student.user','sessions.violations']);
    $sessions       = $exam->sessions;
    $ongoingCount   = $sessions->where('status','aktif')->count();
    $finishedCount  = $sessions->where('status','selesai')->count();
    $totalCount     = $sessions->count();
    $avgScore       = $sessions->where('status','selesai')->avg('score');
@endphp

{{-- Back + header --}}
<div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
    <a href="javascript:history.back()" style="width:36px;height:36px;border-radius:0.5rem;background:#F3F4F6;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#374151;font-size:1.1rem;">←</a>
    <div>
        <div style="font-size:0.75rem;color:#9CA3AF;font-weight:600;">MONITOR UJIAN</div>
        <h1 style="margin:0;font-size:1.1rem;font-weight:800;color:#1F2937;">{{ $exam->title }}</h1>
        <div style="font-size:0.8rem;color:#6B7280;">{{ $exam->subject->name }} &nbsp;·&nbsp; {{ $exam->classRoom?->name ?? 'Semua Kelas' }}</div>
    </div>
    <div style="margin-left:auto;display:flex;align-items:center;gap:0.75rem;">
        {{-- Token display --}}
        <div style="background:#FDEDEC;border:2px solid #C0392B;padding:0.5rem 1rem;border-radius:0.5rem;text-align:center;">
            <div style="font-size:0.6rem;color:#C0392B;font-weight:700;text-transform:uppercase;">Token Ujian</div>
            <div style="font-size:1.5rem;font-weight:800;color:#C0392B;letter-spacing:0.2em;font-family:monospace;">{{ $exam->token }}</div>
        </div>
        {{-- Status --}}
        @if($exam->status==='aktif')
            <span class="badge-aktif" style="padding:0.35rem 0.875rem;">● UJIAN AKTIF</span>
        @else
            <span class="badge-draft">{{ ucfirst($exam->status) }}</span>
        @endif
    </div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0.875rem;margin-bottom:1.5rem;">
    <div class="digi-stat" style="border-left:4px solid #E67E22;">
        <div class="digi-stat-value" style="color:#E67E22;">{{ $ongoingCount }}</div>
        <div class="digi-stat-label">Sedang Mengerjakan</div>
    </div>
    <div class="digi-stat" style="border-left:4px solid #27AE60;">
        <div class="digi-stat-value" style="color:#27AE60;">{{ $finishedCount }}</div>
        <div class="digi-stat-label">Sudah Selesai</div>
    </div>
    <div class="digi-stat" style="border-left:4px solid #3498DB;">
        <div class="digi-stat-value" style="color:#3498DB;">{{ $totalCount }}</div>
        <div class="digi-stat-label">Total Peserta</div>
    </div>
    <div class="digi-stat" style="border-left:4px solid #C0392B;">
        <div class="digi-stat-value">{{ $avgScore ? round($avgScore) : '—' }}</div>
        <div class="digi-stat-label">Rata-rata Nilai</div>
    </div>
    <div class="digi-stat" style="border-left:4px solid #7D3C98;">
        <div class="digi-stat-value" style="color:#7D3C98;">
            {{ $sessions->sum(fn($s) => $s->violations->count()) }}
        </div>
        <div class="digi-stat-label">Total Pelanggaran</div>
    </div>
</div>

{{-- Live monitor table --}}
<div class="digi-card" style="padding:0;overflow:hidden;" wire:poll.5000ms>
    <div style="padding:0.875rem 1.25rem;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:0.875rem;font-weight:600;color:#374151;">👁️ Status Peserta (auto-refresh setiap 5 detik)</span>
        <div style="width:8px;height:8px;border-radius:50%;background:#27AE60;animation:pulse 1.5s infinite;"></div>
    </div>

    @if($sessions->isEmpty())
    <div style="padding:3rem;text-align:center;color:#9CA3AF;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">👥</div>
        <p style="margin:0;">Belum ada siswa yang bergabung</p>
    </div>
    @else
    <table class="digi-table">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Nama Siswa</th>
                <th style="width:80px;">NIS</th>
                <th style="width:100px;">Status</th>
                <th style="width:80px;">Mulai</th>
                <th style="width:80px;">Nilai</th>
                <th style="width:80px;">Pelanggaran</th>
                <th style="width:80px;">Waktu Sisa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions->sortByDesc('started_at') as $i => $ses)
            @php
                $timeLeft = $ses->status === 'aktif' ? $ses->getTimeLeftSeconds() : null;
                $mins     = $timeLeft !== null ? floor($timeLeft / 60) : null;
                $secs     = $timeLeft !== null ? $timeLeft % 60 : null;
                $vCount   = $ses->violations->count();
                $rowBg    = $vCount >= 2 ? '#FFF5F5' : ($ses->status === 'selesai' ? '#F7FFF7' : 'white');
            @endphp
            <tr style="background:{{ $rowBg }};">
                <td style="color:#9CA3AF;font-size:0.8rem;">{{ $i + 1 }}</td>
                <td>
                    <div style="font-weight:600;font-size:0.85rem;color:#1F2937;">
                        {{ $ses->student?->user?->name ?? '—' }}
                    </div>
                </td>
                <td style="font-size:0.8rem;color:#6B7280;">{{ $ses->student?->nis ?? '—' }}</td>
                <td>
                    @if($ses->status === 'aktif')
                        <span class="badge-aktif" style="font-size:0.7rem;">● Aktif</span>
                    @elseif($ses->status === 'selesai')
                        <span class="badge-selesai" style="font-size:0.7rem;">✓ Selesai</span>
                    @else
                        <span class="badge-draft" style="font-size:0.7rem;">Belum</span>
                    @endif
                </td>
                <td style="font-size:0.78rem;color:#6B7280;">
                    {{ $ses->started_at?->format('H:i') ?? '—' }}
                </td>
                <td>
                    @if($ses->score !== null)
                    <strong style="color:{{ $ses->score >= 75 ? '#27AE60' : '#C0392B' }};font-size:1rem;">
                        {{ $ses->score }}
                    </strong>
                    @else
                    <span style="color:#D1D5DB;">—</span>
                    @endif
                </td>
                <td>
                    @if($vCount > 0)
                    <span style="background:#FDEDEC;color:#C0392B;padding:0.2rem 0.5rem;border-radius:999px;font-size:0.75rem;font-weight:700;">
                        ⚠️ {{ $vCount }}
                    </span>
                    @else
                    <span style="color:#D1D5DB;">—</span>
                    @endif
                </td>
                <td>
                    @if($ses->status === 'aktif' && $timeLeft !== null)
                    <span style="font-family:monospace;font-size:0.85rem;font-weight:700;color:{{ $timeLeft < 300 ? '#C0392B' : '#374151' }};">
                        {{ sprintf('%02d:%02d', $mins, $secs) }}
                    </span>
                    @else
                    <span style="color:#D1D5DB;">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

</x-layouts.digitest>
