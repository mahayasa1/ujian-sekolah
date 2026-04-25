{{-- resources/views/livewire/teacher/exam-results.blade.php --}}
<x-layouts.digitest :title="'Rekap Nilai'">

@php
    $exam     = $this->exam->load(['subject','classRoom','sessions.student.user','sessions.answers']);
    $sessions = $exam->sessions->where('status','selesai')->sortByDesc('score');
    $passing  = $sessions->where('score','>=',75)->count();
    $failing  = $sessions->where('score','<',75)->count();
    $highest  = $sessions->max('score');
    $lowest   = $sessions->min('score');
    $avg      = $sessions->avg('score');
@endphp

{{-- Header --}}
<div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
    <a href="javascript:history.back()" style="width:36px;height:36px;border-radius:0.5rem;background:#F3F4F6;display:flex;align-items:center;justify-content:center;text-decoration:none;color:#374151;font-size:1.1rem;">←</a>
    <div>
        <div style="font-size:0.75rem;color:#9CA3AF;font-weight:600;">REKAP NILAI</div>
        <h1 style="margin:0;font-size:1.1rem;font-weight:800;color:#1F2937;">{{ $exam->title }}</h1>
        <div style="font-size:0.8rem;color:#6B7280;">{{ $exam->subject->name }} · {{ $exam->classRoom?->name ?? 'Semua Kelas' }}</div>
    </div>
    <div style="margin-left:auto;">
        <button onclick="window.print()" class="btn-digi-outline" style="font-size:0.8rem;">
            🖨️ Cetak Rekap
        </button>
    </div>
</div>

{{-- Summary stats --}}
<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:0.875rem;margin-bottom:1.5rem;">
    <div class="digi-stat" style="text-align:center;">
        <div class="digi-stat-value">{{ $sessions->count() }}</div>
        <div class="digi-stat-label">Peserta</div>
    </div>
    <div class="digi-stat" style="text-align:center;">
        <div class="digi-stat-value" style="color:#27AE60;">{{ $passing }}</div>
        <div class="digi-stat-label">Lulus</div>
    </div>
    <div class="digi-stat" style="text-align:center;">
        <div class="digi-stat-value" style="color:#C0392B;">{{ $failing }}</div>
        <div class="digi-stat-label">Belum Lulus</div>
    </div>
    <div class="digi-stat" style="text-align:center;">
        <div class="digi-stat-value">{{ $avg ? round($avg,1) : '—' }}</div>
        <div class="digi-stat-label">Rata-rata</div>
    </div>
    <div class="digi-stat" style="text-align:center;">
        <div class="digi-stat-value" style="color:#27AE60;">{{ $highest ?? '—' }}</div>
        <div class="digi-stat-label">Tertinggi</div>
    </div>
    <div class="digi-stat" style="text-align:center;">
        <div class="digi-stat-value" style="color:#C0392B;">{{ $lowest ?? '—' }}</div>
        <div class="digi-stat-label">Terendah</div>
    </div>
</div>

{{-- Grade distribution bar --}}
@if($sessions->isNotEmpty())
<div class="digi-card" style="margin-bottom:1.5rem;">
    <div style="font-size:0.875rem;font-weight:700;color:#374151;margin-bottom:1rem;">📊 Distribusi Nilai</div>
    <div style="display:flex;gap:0.5rem;height:80px;align-items:flex-end;">
        @php
            $ranges = [
                ['label'=>'E (0-59)','min'=>0,'max'=>59,'color'=>'#C0392B'],
                ['label'=>'D (60-69)','min'=>60,'max'=>69,'color'=>'#E67E22'],
                ['label'=>'C (70-74)','min'=>70,'max'=>74,'color'=>'#F39C12'],
                ['label'=>'B (75-84)','min'=>75,'max'=>84,'color'=>'#27AE60'],
                ['label'=>'A (85-100)','min'=>85,'max'=>100,'color'=>'#2980B9'],
            ];
            $maxCount = 1;
            foreach($ranges as $r) {
                $cnt = $sessions->whereBetween('score',[$r['min'],$r['max']])->count();
                if($cnt > $maxCount) $maxCount = $cnt;
            }
        @endphp
        @foreach($ranges as $r)
        @php $cnt = $sessions->whereBetween('score',[$r['min'],$r['max']])->count(); @endphp
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:0.25rem;">
            <div style="font-size:0.75rem;font-weight:700;color:{{ $r['color'] }};">{{ $cnt }}</div>
            <div style="width:100%;background:{{ $r['color'] }};border-radius:0.25rem 0.25rem 0 0;height:{{ $maxCount > 0 ? ($cnt/$maxCount)*60 : 4 }}px;min-height:4px;transition:height 0.5s;"></div>
            <div style="font-size:0.65rem;color:#9CA3AF;text-align:center;line-height:1.2;">{{ $r['label'] }}</div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Results table --}}
<div class="digi-card" style="padding:0;overflow:hidden;">
    <div style="padding:0.875rem 1.25rem;border-bottom:1px solid #F3F4F6;">
        <span style="font-size:0.875rem;font-weight:600;color:#374151;">📋 Daftar Nilai Siswa</span>
    </div>

    @if($sessions->isEmpty())
    <div style="padding:3rem;text-align:center;color:#9CA3AF;">
        <div style="font-size:2rem;margin-bottom:0.5rem;">📭</div>
        <p style="margin:0;">Belum ada siswa yang menyelesaikan ujian ini</p>
    </div>
    @else
    <table class="digi-table">
        <thead>
            <tr>
                <th style="width:40px;">No</th>
                <th>Nama Siswa</th>
                <th style="width:80px;">NIS</th>
                <th style="width:80px;">Nilai</th>
                <th style="width:70px;">Predikat</th>
                <th style="width:80px;">Status</th>
                <th style="width:100px;">Waktu Selesai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $rank => $ses)
            @php
                $score = $ses->score ?? 0;
                $grade = $score >= 90 ? 'A' : ($score >= 80 ? 'B' : ($score >= 75 ? 'C' : ($score >= 60 ? 'D' : 'E')));
                $passed = $score >= 75;
                $gradeColor = $passed ? '#27AE60' : '#C0392B';
            @endphp
            <tr>
                <td style="color:#9CA3AF;font-size:0.8rem;text-align:center;">
                    @if($rank < 3)
                        <span style="font-size:1rem;">{{ ['🥇','🥈','🥉'][$rank] }}</span>
                    @else
                        {{ $rank + 1 }}
                    @endif
                </td>
                <td style="font-weight:600;font-size:0.875rem;color:#1F2937;">
                    {{ $ses->student?->user?->name ?? '—' }}
                </td>
                <td style="font-size:0.8rem;color:#6B7280;">{{ $ses->student?->nis ?? '—' }}</td>
                <td style="text-align:center;">
                    <strong style="font-size:1.15rem;color:{{ $gradeColor }};">{{ $score }}</strong>
                </td>
                <td style="text-align:center;">
                    <span style="background:{{ $passed ? '#D5F5E3' : '#FDEDEC' }};color:{{ $gradeColor }};padding:0.2rem 0.5rem;border-radius:999px;font-size:0.8rem;font-weight:700;">
                        {{ $grade }}
                    </span>
                </td>
                <td style="text-align:center;">
                    @if($passed)
                        <span class="badge-aktif" style="font-size:0.7rem;">Lulus</span>
                    @else
                        <span style="background:#FDEDEC;color:#C0392B;padding:0.2rem 0.6rem;border-radius:999px;font-size:0.7rem;font-weight:600;">Belum</span>
                    @endif
                </td>
                <td style="font-size:0.78rem;color:#6B7280;">
                    {{ $ses->submitted_at?->format('H:i') ?? '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

</x-layouts.digitest>
