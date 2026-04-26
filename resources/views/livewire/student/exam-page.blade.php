{{-- resources/views/livewire/student/exam-page.blade.php --}}
{{-- Security: tab-switch → auto-submit + redirect dashboard --}}
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

        .exam-container {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 1.25rem;
        }
        @media (max-width: 768px) {
            .exam-container { grid-template-columns: 1fr; }
            .exam-sidebar-panel { order: -1; }
        }

        .exam-question-panel {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .exam-sidebar-panel {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            height: fit-content;
            position: sticky;
            top: 70px;
        }

        .exam-timer {
            background: linear-gradient(135deg, #C0392B, #922B21);
            color: white;
            border-radius: 0.75rem;
            padding: 1rem;
            text-align: center;
            margin-bottom: 1rem;
        }
        .exam-timer.warning { background: linear-gradient(135deg, #E67E22, #CA6F1E); }
        .exam-timer.danger  { background: linear-gradient(135deg, #C0392B, #7B241C); animation: pulse-bg 1s infinite; }

        @keyframes pulse-bg {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.85; }
        }

        .exam-timer-value {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'SF Mono', monospace;
            letter-spacing: 2px;
        }

        .option-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            text-align: left;
            padding: 12px 14px;
            border: 1.5px solid #E5E7EB;
            border-radius: 0.625rem;
            background: white;
            cursor: pointer;
            margin-bottom: 8px;
            font-size: 14px;
            color: #374151;
            transition: all 0.15s;
        }
        .option-btn:hover:not(.selected) {
            border-color: #C0392B;
            background: #FDEDEC;
        }
        .option-btn.selected {
            border-color: #C0392B;
            background: rgba(192,57,43,0.06);
            color: #C0392B;
            font-weight: 600;
        }

        .option-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1.5px solid currentColor;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }
        .option-btn.selected .option-circle {
            background: #C0392B;
            border-color: #C0392B;
            color: white;
        }

        .question-number-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 5px;
        }

        .q-num-btn {
            aspect-ratio: 1;
            border-radius: 0.375rem;
            border: 1px solid #E5E7EB;
            background: white;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            color: #9CA3AF;
            transition: all 0.1s;
        }
        .q-num-btn.answered {
            background: #D5F5E3;
            border-color: #27AE60;
            color: #1E8449;
        }
        .q-num-btn.current {
            background: #C0392B;
            border-color: #C0392B;
            color: white;
        }

        .btn-digi-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #C0392B;
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-digi-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: white;
            color: #374151;
            border: 1.5px solid #E5E7EB;
            border-radius: 0.5rem;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-digi-success {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #27AE60;
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
        }
        .btn-digi-primary:disabled,
        .btn-digi-success:disabled { opacity: 0.6; cursor: not-allowed; }

        /* Overlay peringatan tab-switch */
        #tab-warning-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
            padding: 2rem;
        }
        #tab-warning-overlay.show { display: flex; }

        .violation-alert {
            position: fixed;
            top: 72px;
            left: 50%;
            transform: translateX(-50%);
            background: #C0392B;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            z-index: 300;
            box-shadow: 0 4px 16px rgba(192,57,43,0.4);
            white-space: nowrap;
        }

        /* Google Form embed */
        .google-form-iframe {
            width: 100%;
            border: none;
            border-radius: 0.75rem;
            min-height: 600px;
        }
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

    @if($violationCount > 0)
    <div style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);padding:0.3rem 0.75rem;border-radius:0.4rem;color:white;font-size:0.75rem;font-weight:600;">
        ⚠️ Pelanggaran: {{ $violationCount }}/3
    </div>
    @endif

    <div style="color:rgba(255,255,255,0.8);font-size:0.8rem;">{{ auth()->user()->name }}</div>
</header>

{{-- Overlay Tab-Switch Warning --}}
<div id="tab-warning-overlay">
    <div style="font-size:4rem;margin-bottom:1rem;">🚫</div>
    <h2 style="font-size:1.5rem;font-weight:800;margin:0 0 0.5rem;">Pelanggaran Terdeteksi!</h2>
    <p style="font-size:1rem;opacity:0.85;margin:0 0 1.5rem;max-width:360px;">
        Kamu berpindah tab/jendela selama ujian berlangsung. Ujian telah dihentikan otomatis dan jawabanmu telah dikumpulkan.
    </p>
    <div id="redirect-countdown" style="font-size:1rem;opacity:0.75;margin-bottom:1rem;">
        Mengalihkan ke dashboard dalam <span id="countdown-num" style="font-weight:700;">5</span> detik...
    </div>
</div>

{{-- ============================================================
     SECURITY: Tab-Switch auto-submit & redirect
     ============================================================ --}}
<script>
(function () {
    let submitted = false;

    function submitAndRedirect() {
        if (submitted) return;
        submitted = true;

        // Tampilkan overlay
        const overlay = document.getElementById('tab-warning-overlay');
        overlay.classList.add('show');

        // Countdown
        let secs = 5;
        const numEl = document.getElementById('countdown-num');
        const interval = setInterval(() => {
            secs--;
            if (numEl) numEl.textContent = secs;
            if (secs <= 0) {
                clearInterval(interval);
                // Submit lewat Livewire lalu redirect
                if (typeof Livewire !== 'undefined') {
                    Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'))
                        ?.call('submit')
                        .catch(() => {});
                }
                // Fallback redirect paksa
                setTimeout(() => {
                    window.location.href = '{{ route('student.dashboard') }}';
                }, 1500);
            }
        }, 1000);

        // Trigger Livewire submit sekarang juga
        setTimeout(() => {
            if (typeof Livewire !== 'undefined') {
                const wireEl = document.querySelector('[wire\\:id]');
                if (wireEl) {
                    Livewire.find(wireEl.getAttribute('wire:id'))
                        ?.call('submit')
                        .catch(() => {});
                }
            }
        }, 300);
    }

    // Deteksi pindah tab
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && !submitted) {
            submitAndRedirect();
        }
    });

    // Deteksi blur window (pindah ke aplikasi lain)
    window.addEventListener('blur', () => {
        if (!submitted) {
            submitAndRedirect();
        }
    });

    // Blokir klik kanan & shortcut copy
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && ['c','v','a','p','u'].includes(e.key.toLowerCase())) {
            e.preventDefault();
        }
        if (e.key === 'PrintScreen') {
            e.preventDefault();
        }
    });
})();
</script>

<div style="max-width:1100px;margin:1.5rem auto;padding:0 1rem;">
<div class="exam-container">

    {{-- PANEL SOAL / GOOGLE FORM --}}
    <div class="exam-question-panel" wire:poll.30000ms="autoSave">

        @if($session->exam->google_form_url)
            {{-- Mode Google Form Embed --}}
            <div style="margin-bottom:1rem;padding-bottom:1rem;border-bottom:1px solid #F3F4F6;">
                <div style="font-size:0.75rem;font-weight:700;color:#C0392B;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">
                    📋 Ujian via Google Form
                </div>
                <div style="font-size:0.875rem;color:#374151;font-weight:500;">{{ $session->exam->title }}</div>
                <div style="font-size:0.78rem;color:#9CA3AF;margin-top:2px;">
                    Kerjakan soal di form di bawah ini. Setelah selesai, klik tombol Kumpulkan.
                </div>
            </div>

            <iframe
                class="google-form-iframe"
                src="{{ $session->exam->google_form_url }}"
                frameborder="0"
                marginheight="0"
                marginwidth="0"
                allow="camera; microphone"
                style="height:700px;"
            >
                Memuat Google Form...
            </iframe>

            {{-- Tombol kumpulkan manual --}}
            <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid #F3F4F6;display:flex;justify-content:flex-end;">
                <button class="btn-digi-success" wire:click="confirmSubmit">
                    ✅ Saya Sudah Selesai — Kumpulkan
                </button>
            </div>

        @else
            {{-- Mode soal manual (PG / Essay) --}}
            @php
                $questions = $this->questions;
                $question  = $questions->get($currentIndex);
                $total     = $questions->count();
            @endphp

            @if(!$question)
            <div style="text-align:center;padding:3rem;color:#9CA3AF;">
                <div style="font-size:2rem;margin-bottom:0.5rem;">📭</div>
                <p>Belum ada soal pada ujian ini.</p>
            </div>
            @else

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

            @if($question->image)
            <div style="margin-bottom:1rem;">
                <img src="{{ asset('storage/'.$question->image) }}" alt="Gambar soal" style="max-width:100%;border-radius:0.5rem;border:1px solid #E5E7EB;">
            </div>
            @endif

            <div style="font-size:1rem;line-height:1.75;color:#1F2937;margin-bottom:1.75rem;font-weight:500;">
                {{ $question->question }}
            </div>

            @if($question->type === 'pg')
            <div>
                @foreach(['A','B','C','D','E'] as $opt)
                @php $val = 'option_'.strtolower($opt); @endphp
                @if($question->$val)
                <button type="button"
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
            <div>
                <label style="font-size:0.8rem;font-weight:600;color:#6B7280;display:block;margin-bottom:0.5rem;">Jawaban Anda:</label>
                <textarea
                    wire:model.lazy="answers.{{ $question->id }}"
                    wire:change="saveAnswer({{ $question->id }}, $event.target.value)"
                    rows="6"
                    placeholder="Tuliskan jawaban Anda di sini..."
                    style="width:100%;padding:0.875rem;border:2px solid #E5E7EB;border-radius:0.75rem;font-size:0.9rem;resize:vertical;font-family:inherit;line-height:1.6;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'"
                    onblur="this.style.borderColor='#E5E7EB'"
                >{{ $answers[$question->id] ?? '' }}</textarea>
            </div>
            @endif

            <div style="display:flex;justify-content:space-between;margin-top:1.75rem;padding-top:1rem;border-top:1px solid #F3F4F6;">
                <button class="btn-digi-outline" wire:click="goTo({{ max(0, $currentIndex - 1) }})"
                    @if($currentIndex === 0) disabled style="opacity:0.4;cursor:not-allowed;" @endif>
                    ← Sebelumnya
                </button>
                @if($currentIndex < $total - 1)
                <button class="btn-digi-primary" wire:click="goTo({{ $currentIndex + 1 }})">Selanjutnya →</button>
                @else
                <button class="btn-digi-success" wire:click="confirmSubmit">✅ Selesaikan Ujian</button>
                @endif
            </div>
            @endif
        @endif
    </div>

    {{-- SIDEBAR --}}
    <div class="exam-sidebar-panel">
        {{-- Timer --}}
        <div class="exam-timer" id="exam-timer">
            <div style="font-size:0.7rem;opacity:0.85;margin-bottom:0.25rem;text-transform:uppercase;letter-spacing:0.05em;">Sisa Waktu</div>
            <div class="exam-timer-value" id="timer-display">--:--</div>
        </div>

        @if(!$session->exam->google_form_url)
        {{-- Progress soal (hanya mode manual) --}}
        @php $total2 = $this->questions->count(); @endphp
        <div style="margin-bottom:1rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:0.4rem;">
                <span style="font-size:0.75rem;color:#6B7280;">Terjawab</span>
                <span style="font-size:0.75rem;font-weight:600;color:#27AE60;">{{ count($answers) }} / {{ $total2 }}</span>
            </div>
            <div style="height:6px;background:#F3F4F6;border-radius:3px;">
                <div style="height:100%;background:#27AE60;border-radius:3px;width:{{ $total2 > 0 ? (count($answers)/$total2)*100 : 0 }}%;transition:width 0.3s;"></div>
            </div>
        </div>

        <div style="font-size:0.75rem;font-weight:600;color:#6B7280;margin-bottom:0.5rem;">Navigasi Soal</div>
        <div class="question-number-grid">
            @foreach($this->questions as $i => $q)
            <button class="q-num-btn {{ $i === $currentIndex ? 'current' : (isset($answers[$q->id]) ? 'answered' : '') }}"
                wire:click="goTo({{ $i }})">{{ $i + 1 }}</button>
            @endforeach
        </div>

        <div style="display:flex;gap:0.75rem;margin-top:0.75rem;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:0.3rem;font-size:0.7rem;color:#6B7280;">
                <div style="width:14px;height:14px;border-radius:3px;background:#D5F5E3;border:1px solid #27AE60;"></div> Terjawab
            </div>
            <div style="display:flex;align-items:center;gap:0.3rem;font-size:0.7rem;color:#6B7280;">
                <div style="width:14px;height:14px;border-radius:3px;background:#C0392B;"></div> Sekarang
            </div>
        </div>
        @endif

        <div style="margin-top:1.25rem;">
            <button class="btn-digi-success" style="width:100%;justify-content:center;display:flex;" wire:click="confirmSubmit">
                ✅ Kumpulkan Jawaban
            </button>
        </div>

        @if($violationCount > 0)
        <div style="margin-top:0.75rem;background:#FDEDEC;border:1px solid #F1948A;border-radius:0.5rem;padding:0.6rem 0.875rem;font-size:0.78rem;color:#C0392B;font-weight:600;">
            ⚠️ {{ $violationCount }}/3 Pelanggaran
        </div>
        @endif

        {{-- Peringatan keamanan --}}
        <div style="margin-top:1rem;background:#FFFBEB;border:0.5px solid #FCD34D;border-radius:0.5rem;padding:0.6rem 0.875rem;font-size:0.72rem;color:#78350F;line-height:1.6;">
            🔒 <strong>Mode Aman Aktif</strong><br>
            Berpindah tab akan menghentikan ujian otomatis.
        </div>
    </div>

</div>
</div>

{{-- Modal Konfirmasi Submit --}}
@if($showSubmitConfirm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:100;">
    <div style="background:white;border-radius:1rem;padding:2rem;max-width:420px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.3);">
        <div style="font-size:3rem;margin-bottom:0.75rem;">📋</div>
        <h2 style="margin:0 0 0.5rem;font-size:1.25rem;font-weight:700;color:#1F2937;">Kumpulkan Jawaban?</h2>

        @if(!$session->exam->google_form_url)
        @php $unanswered2 = $this->questions->count() - count($answers); @endphp
        <p style="margin:0 0 0.5rem;font-size:0.875rem;color:#6B7280;">
            Anda telah menjawab <strong style="color:#27AE60;">{{ count($answers) }}</strong> dari
            <strong>{{ $this->questions->count() }}</strong> soal.
        </p>
        @if($unanswered2 > 0)
        <p style="margin:0 0 1.5rem;font-size:0.875rem;color:#E67E22;">⚠️ {{ $unanswered2 }} soal belum dijawab</p>
        @else
        <p style="margin:0 0 1.5rem;font-size:0.875rem;color:#27AE60;">✅ Semua soal sudah dijawab</p>
        @endif
        @else
        <p style="margin:0 0 1.5rem;font-size:0.875rem;color:#6B7280;">
            Pastikan kamu sudah submit jawaban di Google Form sebelum mengklik tombol ini.
        </p>
        @endif

        <div style="display:flex;gap:0.75rem;justify-content:center;">
            <button class="btn-digi-outline" wire:click="$set('showSubmitConfirm', false)">Batal</button>
            <button class="btn-digi-success" wire:click="submit">✅ Ya, Kumpulkan</button>
        </div>
    </div>
</div>
@endif

{{-- Timer Script --}}
<script>
(function() {
    const endTimestamp = {{ $session->started_at->addMinutes($session->exam->duration)->timestamp }} * 1000;

    function updateTimer() {
        const now       = Date.now();
        const remaining = Math.max(0, Math.floor((endTimestamp - now) / 1000));
        const hours     = Math.floor(remaining / 3600);
        const minutes   = Math.floor((remaining % 3600) / 60);
        const seconds   = remaining % 60;

        const display = remaining >= 3600
            ? `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`
            : `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;

        const el = document.getElementById('timer-display');
        if (el) el.textContent = display;

        const timerEl = document.getElementById('exam-timer');
        if (timerEl) {
            if (remaining <= 60)       timerEl.className = 'exam-timer danger';
            else if (remaining <= 300) timerEl.className = 'exam-timer warning';
        }

        if (remaining <= 0) {
            if (typeof Livewire !== 'undefined') {
                const wireEl = document.querySelector('[wire\\:id]');
                if (wireEl) {
                    Livewire.find(wireEl.getAttribute('wire:id'))?.call('submit');
                }
            }
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