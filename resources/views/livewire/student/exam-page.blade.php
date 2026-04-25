{{-- resources/views/livewire/student/exam-page.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $session->exam->title }} - DigiTest SELSA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    @livewireStyles
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #F3F4F6; }
    </style>
</head>
<body>

{{-- Exam Header --}}
<header style="background:linear-gradient(135deg,#C0392B,#922B21);height:56px;display:flex;align-items:center;padding:0 1.5rem;position:sticky;top:0;z-index:50;gap:1rem;">
    <div style="display:flex;align-items:center;gap:0.75rem;flex:1;">
        <div style="font-size:1.25rem;">📋</div>
        <div>
            <div style="font-size:0.65rem;color:rgba(255,255,255,0.7);">{{ $session->exam->subject->name }}</div>
            <div style="font-size:0.9rem;font-weight:700;color:white;">{{ $session->exam->title }}</div>
        </div>
    </div>

    {{-- Violation indicator --}}
    @if($violationCount > 0)
    <div style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);padding:0.3rem 0.75rem;border-radius:0.4rem;color:white;font-size:0.75rem;font-weight:600;">
        ⚠️ Pelanggaran: {{ $violationCount }}/3
    </div>
    @endif

    <div style="color:rgba(255,255,255,0.8);font-size:0.8rem;">
        {{ auth()->user()->name }}
    </div>
</header>

{{-- Anti-cheat JS --}}
<script>
    let violationShown = false;
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            @this.call('reportViolation', 'tab_switch');
            if (!violationShown) {
                violationShown = true;
                showAlert('⚠️ Peringatan: Jangan pindah tab! Pelanggaran tercatat.');
                setTimeout(() => { violationShown = false; }, 3000);
            }
        }
    });

    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && ['c','v','a','p'].includes(e.key.toLowerCase())) {
            e.preventDefault();
        }
        if (e.key === 'PrintScreen') {
            e.preventDefault();
            @this.call('reportViolation', 'screenshot');
        }
    });

    function showAlert(msg) {
        const el = document.createElement('div');
        el.className = 'violation-alert';
        el.textContent = msg;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3000);
    }

    // Auto fullscreen request
    // document.documentElement.requestFullscreen?.();
</script>

<div style="max-width:1100px;margin:1.5rem auto;padding:0 1rem;">
<div class="exam-container">

    {{-- QUESTION PANEL --}}
    <div class="exam-question-panel" wire:poll.30000ms="autoSave">
        @php
            $questions = $this->questions;
            $question  = $questions->get($currentIndex);
            $total     = $questions->count();
        @endphp

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid #F3F4F6;">
            <div>
                <span style="font-size:0.75rem;font-weight:700;color:#C0392B;text-transform:uppercase;letter-spacing:0.05em;">Soal {{ $currentIndex + 1 }} dari {{ $total }}</span>
                <div style="width:200px;height:4px;background:#F3F4F6;border-radius:2px;margin-top:0.4rem;">
                    <div style="height:100%;background:linear-gradient(90deg,#C0392B,#F39C12);border-radius:2px;width:{{ (($currentIndex+1)/$total)*100 }}%;transition:width 0.3s;"></div>
                </div>
            </div>
            <span style="background:#FDEDEC;color:#C0392B;padding:0.2rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:600;">
                {{ strtoupper($question->type === 'pg' ? 'Pilihan Ganda' : 'Essay') }}
            </span>
        </div>

        {{-- Question text --}}
        @if($question->image)
        <div style="margin-bottom:1rem;">
            <img src="{{ asset('storage/'.$question->image) }}" alt="Gambar soal" style="max-width:100%;border-radius:0.5rem;border:1px solid #E5E7EB;">
        </div>
        @endif

        <div style="font-size:1rem;line-height:1.75;color:#1F2937;margin-bottom:1.75rem;font-weight:500;">
            {{ $question->question }}
        </div>

        {{-- Options --}}
        @if($question->type === 'pg')
        <div>
            @foreach(['A','B','C','D','E'] as $opt)
            @php $val = 'option_'.strtolower($opt); @endphp
            @if($question->$val)
            <button
                type="button"
                class="option-btn {{ ($answers[$question->id] ?? '') === $opt ? 'selected' : '' }}"
                wire:click="saveAnswer({{ $question->id }}, '{{ $opt }}')"
            >
                <span class="option-circle">{{ $opt }}</span>
                <span>{{ $question->$val }}</span>
            </button>
            @endif
            @endforeach
        </div>
        @else
        {{-- Essay --}}
        <div>
            <label style="font-size:0.8rem;font-weight:600;color:#6B7280;display:block;margin-bottom:0.5rem;">Jawaban Anda:</label>
            <textarea
                wire:model.lazy="answers.{{ $question->id }}"
                wire:change="saveAnswer({{ $question->id }}, $event.target.value)"
                rows="6"
                placeholder="Tuliskan jawaban Anda di sini..."
                style="width:100%;padding:0.875rem;border:2px solid #E5E7EB;border-radius:0.75rem;font-size:0.9rem;resize:vertical;transition:border-color 0.2s;font-family:inherit;line-height:1.6;"
                onfocus="this.style.borderColor='#C0392B'"
                onblur="this.style.borderColor='#E5E7EB'"
            >{{ $answers[$question->id] ?? '' }}</textarea>
        </div>
        @endif

        {{-- Navigation --}}
        <div style="display:flex;justify-content:space-between;margin-top:1.75rem;padding-top:1rem;border-top:1px solid #F3F4F6;">
            <button
                class="btn-digi-outline"
                wire:click="goTo({{ max(0, $currentIndex - 1) }})"
                @if($currentIndex === 0) disabled style="opacity:0.4;cursor:not-allowed;" @endif
            >
                ← Sebelumnya
            </button>

            @if($currentIndex < $total - 1)
            <button class="btn-digi-primary" wire:click="goTo({{ $currentIndex + 1 }})">
                Selanjutnya →
            </button>
            @else
            <button class="btn-digi-success" wire:click="confirmSubmit">
                ✅ Selesaikan Ujian
            </button>
            @endif
        </div>
    </div>

    {{-- SIDEBAR PANEL --}}
    <div class="exam-sidebar-panel">
        {{-- Timer --}}
        <div class="exam-timer" id="exam-timer">
            <div style="font-size:0.7rem;opacity:0.85;margin-bottom:0.25rem;text-transform:uppercase;letter-spacing:0.05em;">Sisa Waktu</div>
            <div class="exam-timer-value" id="timer-display">--:--</div>
        </div>

        {{-- Progress --}}
        <div style="margin-bottom:1rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;">
                <span style="font-size:0.75rem;color:#6B7280;">Terjawab</span>
                <span style="font-size:0.75rem;font-weight:600;color:#27AE60;">{{ count($answers) }} / {{ $total }}</span>
            </div>
            <div style="height:6px;background:#F3F4F6;border-radius:3px;">
                <div style="height:100%;background:#27AE60;border-radius:3px;width:{{ $total > 0 ? (count($answers)/$total)*100 : 0 }}%;transition:width 0.3s;"></div>
            </div>
        </div>

        {{-- Number grid --}}
        <div style="font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.5rem;">Navigasi Soal</div>
        <div class="question-number-grid">
            @foreach($questions as $i => $q)
            <button
                class="q-num-btn {{ $i === $currentIndex ? 'current' : (isset($answers[$q->id]) ? 'answered' : '') }}"
                wire:click="goTo({{ $i }})"
            >{{ $i + 1 }}</button>
            @endforeach
        </div>

        {{-- Legend --}}
        <div style="display:flex;gap:0.75rem;margin-top:0.75rem;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:0.3rem;font-size:0.7rem;color:#6B7280;">
                <div style="width:14px;height:14px;border-radius:3px;background:#D5F5E3;border:1px solid #27AE60;"></div> Terjawab
            </div>
            <div style="display:flex;align-items:center;gap:0.3rem;font-size:0.7rem;color:#6B7280;">
                <div style="width:14px;height:14px;border-radius:3px;background:#C0392B;"></div> Sekarang
            </div>
            <div style="display:flex;align-items:center;gap:0.3rem;font-size:0.7rem;color:#6B7280;">
                <div style="width:14px;height:14px;border-radius:3px;background:white;border:1px solid #E5E7EB;"></div> Belum
            </div>
        </div>

        {{-- Submit button --}}
        <div style="margin-top:1.25rem;">
            <button class="btn-digi-success" style="width:100%;justify-content:center;display:flex;" wire:click="confirmSubmit">
                ✅ Kumpulkan Jawaban
            </button>
        </div>

        {{-- Violation count --}}
        @if($violationCount > 0)
        <div style="margin-top:0.75rem;background:#FDEDEC;border:1px solid #F1948A;border-radius:0.5rem;padding:0.6rem 0.875rem;font-size:0.78rem;color:#C0392B;font-weight:600;">
            ⚠️ {{ $violationCount }}/3 Pelanggaran
        </div>
        @endif
    </div>
</div>
</div>

{{-- Submit Confirmation Modal --}}
@if($showSubmitConfirm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:100;">
    <div style="background:white;border-radius:1rem;padding:2rem;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="font-size:3rem;margin-bottom:0.75rem;">📋</div>
        <h2 style="margin:0 0 0.5rem;font-size:1.25rem;font-weight:700;color:#1F2937;">Kumpulkan Jawaban?</h2>
        <p style="margin:0 0 0.5rem;font-size:0.875rem;color:#6B7280;">
            Anda telah menjawab <strong style="color:#27AE60;">{{ count($answers) }}</strong> dari <strong>{{ $total }}</strong> soal.
        </p>
        @php $unanswered = $total - count($answers); @endphp
        @if($unanswered > 0)
        <p style="margin:0 0 1.5rem;font-size:0.875rem;color:#E67E22;">
            ⚠️ {{ $unanswered }} soal belum dijawab
        </p>
        @else
        <p style="margin:0 0 1.5rem;font-size:0.875rem;color:#27AE60;">
            ✅ Semua soal sudah dijawab
        </p>
        @endif
        <div style="display:flex;gap:0.75rem;justify-content:center;">
            <button class="btn-digi-outline" wire:click="$set('showSubmitConfirm', false)">
                Batal
            </button>
            <button class="btn-digi-success" wire:click="submit">
                ✅ Ya, Kumpulkan
            </button>
        </div>
    </div>
</div>
@endif

{{-- Timer Script --}}
<script>
(function() {
    const endTimestamp = {{ $session->started_at->addMinutes($session->exam->duration)->timestamp }} * 1000;

    function updateTimer() {
        const now = Date.now();
        const remaining = Math.max(0, Math.floor((endTimestamp - now) / 1000));

        const hours   = Math.floor(remaining / 3600);
        const minutes = Math.floor((remaining % 3600) / 60);
        const seconds = remaining % 60;

        const display = remaining >= 3600
            ? `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`
            : `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;

        document.getElementById('timer-display').textContent = display;

        const timerEl = document.getElementById('exam-timer');
        if (remaining <= 60) {
            timerEl.className = 'exam-timer danger';
        } else if (remaining <= 300) {
            timerEl.className = 'exam-timer warning';
        }

        if (remaining <= 0) {
            @this.call('submit');
            return;
        }

        setTimeout(updateTimer, 1000);
    }
    updateTimer();
})();
</script>

@livewireScripts
</body>
</html>
