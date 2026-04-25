{{-- resources/views/livewire/student/result.blade.php --}}
<x-layouts.digitest :title="'Hasil Ujian'">

@php
    $score     = $session->score ?? 0;
    $passed    = $score >= 75;
    $grade     = $score >= 90 ? 'A' : ($score >= 80 ? 'B' : ($score >= 70 ? 'C' : ($score >= 60 ? 'D' : 'E')));
    $gradeColor = $score >= 75 ? '#27AE60' : '#C0392B';
    $pct        = $score . '%';
@endphp

<div style="max-width:680px;margin:0 auto;">

    {{-- Result hero --}}
    <div class="digi-card" style="text-align:center;padding:2.5rem 2rem;margin-bottom:1.25rem;border-top:4px solid {{ $gradeColor }};">

        {{-- Score circle --}}
        <div style="--score-percent:{{ $score * 3.6 }}deg;position:relative;width:160px;height:160px;margin:0 auto 1.5rem;">
            <svg viewBox="0 0 160 160" style="width:160px;height:160px;transform:rotate(-90deg);">
                <circle cx="80" cy="80" r="68" fill="none" stroke="#F3F4F6" stroke-width="14"/>
                <circle cx="80" cy="80" r="68" fill="none"
                    stroke="{{ $gradeColor }}"
                    stroke-width="14"
                    stroke-linecap="round"
                    stroke-dasharray="{{ round($score * 4.272) }} 427.2"
                    style="transition:stroke-dasharray 1s ease;"/>
            </svg>
            <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                <span style="font-size:2.5rem;font-weight:800;color:{{ $gradeColor }};line-height:1;">{{ $score }}</span>
                <span style="font-size:0.7rem;color:#9CA3AF;font-weight:600;letter-spacing:0.05em;">NILAI</span>
            </div>
        </div>

        {{-- Grade badge --}}
        <div style="display:inline-flex;align-items:center;gap:0.5rem;background:{{ $passed ? '#D5F5E3' : '#FDEDEC' }};color:{{ $gradeColor }};padding:0.4rem 1.25rem;border-radius:999px;font-size:0.85rem;font-weight:700;margin-bottom:1rem;">
            {{ $passed ? '✅ LULUS' : '❌ BELUM LULUS' }} &nbsp;·&nbsp; Predikat {{ $grade }}
        </div>

        <h2 style="margin:0 0 0.25rem;font-size:1.1rem;font-weight:700;color:#1F2937;">{{ $session->exam->title }}</h2>
        <p style="margin:0;font-size:0.85rem;color:#6B7280;">
            {{ $session->exam->subject->name }} &nbsp;·&nbsp;
            Dikumpulkan {{ $session->submitted_at?->format('d M Y, H:i') }}
        </p>
    </div>

    {{-- Stats row --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.875rem;margin-bottom:1.25rem;">
        <div class="digi-stat" style="text-align:center;">
            <div class="digi-stat-value" style="color:#27AE60;">{{ $correctPg }}</div>
            <div class="digi-stat-label">Benar</div>
        </div>
        <div class="digi-stat" style="text-align:center;">
            <div class="digi-stat-value" style="color:#C0392B;">{{ $totalPg - $correctPg }}</div>
            <div class="digi-stat-label">Salah</div>
        </div>
        <div class="digi-stat" style="text-align:center;">
            <div class="digi-stat-value" style="color:#E67E22;">{{ $unanswered }}</div>
            <div class="digi-stat-label">Kosong</div>
        </div>
        <div class="digi-stat" style="text-align:center;">
            <div class="digi-stat-value" style="color:#7D3C98;">{{ $session->violations()->count() }}</div>
            <div class="digi-stat-label">Pelanggaran</div>
        </div>
    </div>

    {{-- Answer review --}}
    <div class="digi-card" style="padding:0;overflow:hidden;margin-bottom:1.25rem;">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;gap:0.5rem;">
            <span style="font-size:1rem;">📝</span>
            <h3 style="margin:0;font-size:0.9rem;font-weight:700;color:#1F2937;">Review Jawaban</h3>
        </div>
        <div style="padding:0.75rem 1.25rem;">
            @foreach($session->exam->questions->where('type','pg') as $i => $q)
            @php
                $myAnswer  = $session->answers->firstWhere('question_id', $q->id)?->answer;
                $isCorrect = $myAnswer && strtoupper($myAnswer) === strtoupper($q->answer_key);
                $bg        = $isCorrect ? '#D5F5E3' : (!$myAnswer ? '#F9FAFB' : '#FDEDEC');
                $color     = $isCorrect ? '#1E8449' : (!$myAnswer ? '#9CA3AF' : '#C0392B');
            @endphp
            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.6rem 0;border-bottom:1px solid #F9FAFB;">
                <span style="width:28px;height:28px;border-radius:0.4rem;background:{{ $bg }};color:{{ $color }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;flex-shrink:0;">
                    {{ $i + 1 }}
                </span>
                <div style="flex:1;min-width:0;">
                    <p style="margin:0;font-size:0.82rem;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ Str::limit($q->question, 80) }}
                    </p>
                </div>
                <div style="display:flex;align-items:center;gap:0.4rem;flex-shrink:0;">
                    <span style="font-size:0.75rem;color:#6B7280;">Jawaban:</span>
                    <span style="font-size:0.8rem;font-weight:700;color:{{ $color }};">
                        {{ $myAnswer ?? '–' }}
                    </span>
                    @if(!$isCorrect && $myAnswer)
                    <span style="font-size:0.75rem;color:#9CA3AF;">(Kunci: <strong style="color:#27AE60;">{{ $q->answer_key }}</strong>)</span>
                    @elseif(!$myAnswer)
                    <span style="font-size:0.75rem;color:#9CA3AF;">(Kunci: {{ $q->answer_key }})</span>
                    @else
                    <span style="color:#27AE60;font-size:0.9rem;">✓</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    <div style="display:flex;gap:0.75rem;justify-content:center;">
        <a href="{{ route('student.dashboard') }}" wire:navigate class="btn-digi-outline">
            ← Dashboard
        </a>
        <button onclick="window.print()" class="btn-digi-primary">
            🖨️ Cetak Hasil
        </button>
    </div>
</div>

</x-layouts.digitest>
