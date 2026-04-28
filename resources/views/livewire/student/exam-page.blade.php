{{-- resources/views/livewire/student/exam-page.blade.php --}}
{{-- UPDATED: No sidebar layout, timer stops on violation, blur grace period with warning --}}
<div
    x-data="examSecurity(@js($session->id), @js($session->exam->google_form_url ? true : false))"
    x-init="init()"
    style="background:#F3F4F6;min-height:100vh;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,sans-serif;"
>

{{-- ============================================================
     BLUR WARNING OVERLAY — Muncul saat blur, countdown 8 detik
     ============================================================ --}}
<div
    x-show="showBlurWarning"
    x-cloak
    style="position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9998;display:flex;align-items:center;justify-content:center;padding:24px;"
>
    <div style="background:white;border-radius:20px;max-width:360px;width:100%;overflow:hidden;box-shadow:0 25px 80px rgba(0,0,0,0.5);text-align:center;">

        {{-- Yellow warning top bar --}}
        <div style="background:linear-gradient(135deg,#F39C12,#D68910);padding:24px 24px 18px;">
            <div style="font-size:48px;margin-bottom:8px;">⚠️</div>
            <div style="font-size:18px;font-weight:800;color:white;line-height:1.3;">Peringatan!</div>
            <div style="font-size:13px;color:rgba(255,255,255,0.85);margin-top:4px;">Kamu keluar dari jendela ujian</div>
        </div>

        <div style="padding:20px 24px;">
            <p style="font-size:14px;color:#374151;line-height:1.7;margin:0 0 16px;">
                Segera kembali ke halaman ujian!<br>
                Jika tidak, ujian akan <strong style="color:#C0392B;">dikunci otomatis</strong> dalam:
            </p>

            {{-- Countdown circle --}}
            <div style="display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                <div style="
                    width:90px;height:90px;border-radius:50%;
                    background:linear-gradient(135deg,#FEF9C3,#FEF08A);
                    border:4px solid #F39C12;
                    display:flex;flex-direction:column;align-items:center;justify-content:center;
                    box-shadow:0 0 0 4px rgba(243,156,18,0.2);
                ">
                    <div x-text="blurCountdown" style="font-size:36px;font-weight:800;color:#92400E;line-height:1;"></div>
                    <div style="font-size:9px;color:#92400E;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;">detik</div>
                </div>
            </div>

            <div style="background:#FFFBEB;border:1px solid #FCD34D;border-radius:10px;padding:10px 14px;font-size:12px;color:#78350F;line-height:1.6;text-align:left;">
                🔒 Setelah dikunci, kamu perlu <strong>token re-entry</strong> dari guru untuk melanjutkan ujian.
            </div>

            {{-- Button kembali --}}
            <button
                @click="cancelBlurViolation()"
                style="margin-top:14px;width:100%;padding:13px;background:#27AE60;border:none;border-radius:10px;font-size:14px;font-weight:700;color:white;cursor:pointer;font-family:inherit;"
            >
                ✅ Saya Sudah Kembali
            </button>
        </div>
    </div>
</div>

{{-- ============================================================
     VIOLATION OVERLAY — Fullscreen, 5 detik countdown (tab switch)
     ============================================================ --}}
<div
    x-show="showViolationOverlay"
    x-cloak
    style="position:fixed;inset:0;background:rgba(0,0,0,0.95);z-index:9999;display:flex;align-items:center;justify-content:center;padding:24px;"
>
    <div style="background:white;border-radius:20px;max-width:380px;width:100%;overflow:hidden;box-shadow:0 25px 80px rgba(0,0,0,0.5);text-align:center;">

        <div style="background:linear-gradient(135deg,#C0392B,#7B241C);padding:28px 24px 20px;">
            <div style="font-size:48px;margin-bottom:8px;">🚫</div>
            <div style="font-size:18px;font-weight:800;color:white;line-height:1.3;">Pelanggaran Terdeteksi!</div>
        </div>

        <div style="padding:24px;">
            <p style="font-size:14px;color:#374151;line-height:1.7;margin:0 0 20px;">
                Kamu berpindah tab/jendela saat ujian.<br>
                Ujian dikunci otomatis. Minta <strong style="color:#C0392B;">token re-entry</strong> dari guru pengawas untuk melanjutkan.
            </p>

            <div style="display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                <div style="
                    width:80px;height:80px;border-radius:50%;
                    background:linear-gradient(135deg,#FDEDEC,#F8D7DA);
                    border:4px solid #C0392B;
                    display:flex;flex-direction:column;align-items:center;justify-content:center;
                    box-shadow:0 0 0 4px rgba(192,57,43,0.15);
                ">
                    <div x-text="redirectCountdown" style="font-size:28px;font-weight:800;color:#C0392B;line-height:1;"></div>
                    <div style="font-size:9px;color:#C0392B;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;">detik</div>
                </div>
            </div>

            <div style="background:#FFF3CD;border:1px solid #FFC107;border-radius:10px;padding:12px 16px;font-size:12px;color:#856404;line-height:1.6;">
                ⏰ Mengalihkan ke dashboard dalam <strong x-text="redirectCountdown"></strong> detik...<br>
                <span style="font-size:11px;opacity:0.8;">⏱ Waktu ujian <strong>berhenti</strong> saat ini dan akan lanjut dari sini saat re-entry</span>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     HEADER UJIAN (standalone, tanpa sidebar)
     ============================================================ --}}
<header style="
    background:linear-gradient(135deg,#C0392B,#922B21);
    height:54px;
    display:flex;
    align-items:center;
    padding:0 12px;
    position:sticky;
    top:0;
    z-index:50;
    gap:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.25);
">
    <div style="display:flex;align-items:center;gap:8px;flex:1;min-width:0;">
        <div style="font-size:18px;flex-shrink:0;">📋</div>
        <div style="min-width:0;">
            <div style="font-size:10px;color:rgba(255,255,255,0.7);line-height:1;">{{ $session->exam->subject->name }}</div>
            <div style="font-size:13px;font-weight:700;color:white;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $session->exam->title }}</div>
        </div>
    </div>

    @if($violationCount > 0)
    <div style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);padding:3px 8px;border-radius:6px;color:white;font-size:11px;font-weight:600;flex-shrink:0;">
        ⚠️ {{ $violationCount }}
    </div>
    @endif

    <div style="color:rgba(255,255,255,0.85);font-size:11px;flex-shrink:0;max-width:90px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</div>
</header>

{{-- ============================================================
     TIMER BAR
     ============================================================ --}}
<div id="exam-timer" style="
    background:linear-gradient(135deg,#C0392B,#922B21);
    color:white;
    text-align:center;
    padding:10px 16px;
    position:sticky;
    top:54px;
    z-index:40;
    box-shadow:0 2px 6px rgba(192,57,43,0.3);
">
    <div style="font-size:9px;font-weight:700;opacity:0.8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:2px;">⏱ SISA WAKTU</div>
    <div id="timer-display" style="font-size:28px;font-weight:800;font-family:'SF Mono','Courier New',monospace;letter-spacing:3px;">--:--</div>
</div>

{{-- ============================================================
     MAIN CONTENT
     ============================================================ --}}
<div style="max-width:800px;margin:0 auto;padding:12px;">

    @if(!$session->exam->google_form_url)
    @php $totalQ = $this->questions->count(); @endphp
    <div style="background:white;border-radius:10px;padding:10px 14px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:10px;display:flex;align-items:center;gap:10px;">
        <div style="flex:1;">
            <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                <span style="font-size:11px;color:#6B7280;font-weight:500;">Progress</span>
                <span style="font-size:11px;font-weight:700;color:#27AE60;">{{ count($answers) }}/{{ $totalQ }} terjawab</span>
            </div>
            <div style="height:5px;background:#F3F4F6;border-radius:3px;overflow:hidden;">
                <div style="height:100%;background:linear-gradient(90deg,#27AE60,#2ECC71);border-radius:3px;width:{{ $totalQ > 0 ? round((count($answers)/$totalQ)*100) : 0 }}%;transition:width 0.4s;"></div>
            </div>
        </div>
        <div style="font-size:18px;font-weight:800;color:#27AE60;flex-shrink:0;">{{ $totalQ > 0 ? round((count($answers)/$totalQ)*100) : 0 }}%</div>
    </div>
    @endif

    {{-- ===== GOOGLE FORM MODE ===== --}}
    @if($session->exam->google_form_url)
    <div style="background:white;border-radius:12px;padding:14px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:10px;">
        <div style="font-size:12px;font-weight:700;color:#C0392B;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">📋 Ujian via Google Form</div>
        <div style="font-size:13px;color:#374151;margin-bottom:4px;font-weight:500;">{{ $session->exam->title }}</div>
        <div style="font-size:11px;color:#9CA3AF;">Kerjakan soal di form di bawah ini, lalu klik tombol Kumpulkan.</div>
    </div>

    <div x-data="{ frameKey: Date.now() }">
        <iframe
            :key="frameKey"
            src="{{ $session->exam->google_form_url . (str_contains($session->exam->google_form_url, '?') ? '&' : '?') . 'embedded=true' }}"
            style="width:100%;border:none;border-radius:12px;min-height:70vh;box-shadow:0 1px 3px rgba(0,0,0,0.08);"
            frameborder="0"
            marginheight="0"
            marginwidth="0"
            allow="camera; microphone"
        >Memuat...</iframe>
    </div>

    <div style="margin-top:12px;">
        <button class="btn-digi-success" style="width:100%;justify-content:center;padding:14px;font-size:15px;" wire:click="confirmSubmit">
            ✅ Saya Sudah Selesai — Kumpulkan
        </button>
    </div>

    {{-- ===== MANUAL QUESTION MODE ===== --}}
    @else
    @php
        $questions = $this->questions;
        $question  = $questions->get($currentIndex);
        $total     = $questions->count();
    @endphp

    @if(!$question)
    <div style="background:white;border-radius:12px;padding:40px 16px;text-align:center;color:#9CA3AF;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
        <div style="font-size:32px;margin-bottom:8px;">📭</div>
        <p style="font-size:14px;margin:0;">Belum ada soal pada ujian ini.</p>
    </div>
    @else

    <div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:10px;" wire:poll.30000ms="autoSave">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-size:12px;font-weight:700;color:#C0392B;text-transform:uppercase;letter-spacing:0.5px;">
                Soal {{ $currentIndex + 1 }} / {{ $total }}
            </div>
            <span style="background:#FDEDEC;color:#C0392B;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;">
                {{ $question->type === 'pg' ? 'Pilihan Ganda' : 'Essay' }}
            </span>
        </div>

        <div style="height:3px;background:#F3F4F6;border-radius:2px;margin-bottom:16px;overflow:hidden;">
            <div style="height:100%;background:linear-gradient(90deg,#C0392B,#F39C12);border-radius:2px;width:{{ (($currentIndex+1)/$total)*100 }}%;transition:width 0.3s;"></div>
        </div>

        @if(isset($question->image) && $question->image)
        <div style="margin-bottom:14px;">
            <img src="{{ asset('storage/'.$question->image) }}" alt="Gambar soal" style="max-width:100%;border-radius:8px;border:1px solid #E5E7EB;">
        </div>
        @endif

        <div style="font-size:15px;line-height:1.75;color:#1F2937;margin-bottom:18px;font-weight:500;">
            {{ $question->question }}
        </div>

        @if($question->type === 'pg')
        <div>
            @foreach(['A','B','C','D','E'] as $opt)
            @php $val = 'option_'.strtolower($opt); @endphp
            @if(isset($question->$val) && $question->$val)
            <button
                type="button"
                wire:click="saveAnswer({{ $question->id }}, '{{ $opt }}')"
                style="
                    display:flex;align-items:center;gap:12px;
                    width:100%;text-align:left;
                    padding:12px 14px;
                    border:2px solid {{ ($answers[$question->id] ?? '') === $opt ? '#C0392B' : '#E5E7EB' }};
                    border-radius:10px;
                    background:{{ ($answers[$question->id] ?? '') === $opt ? 'rgba(192,57,43,0.06)' : 'white' }};
                    cursor:pointer;
                    margin-bottom:8px;
                    font-size:14px;
                    color:{{ ($answers[$question->id] ?? '') === $opt ? '#C0392B' : '#374151' }};
                    font-weight:{{ ($answers[$question->id] ?? '') === $opt ? '600' : '400' }};
                    transition:all 0.15s;
                    -webkit-appearance:none;
                    font-family:inherit;
                "
            >
                <span style="
                    width:32px;height:32px;border-radius:50%;
                    border:2px solid {{ ($answers[$question->id] ?? '') === $opt ? '#C0392B' : '#D1D5DB' }};
                    background:{{ ($answers[$question->id] ?? '') === $opt ? '#C0392B' : 'white' }};
                    display:flex;align-items:center;justify-content:center;
                    font-weight:700;font-size:13px;
                    color:{{ ($answers[$question->id] ?? '') === $opt ? 'white' : '#6B7280' }};
                    flex-shrink:0;
                ">{{ $opt }}</span>
                <span>{{ $question->$val }}</span>
            </button>
            @endif
            @endforeach
        </div>
        @else
        <div>
            <label style="font-size:12px;font-weight:600;color:#6B7280;display:block;margin-bottom:6px;">Jawaban Anda:</label>
            <textarea
                wire:model.lazy="answers.{{ $question->id }}"
                wire:change="saveAnswer({{ $question->id }}, $event.target.value)"
                rows="5"
                placeholder="Tuliskan jawaban Anda di sini..."
                style="width:100%;padding:12px;border:2px solid #E5E7EB;border-radius:10px;font-size:14px;resize:vertical;font-family:inherit;line-height:1.6;outline:none;box-sizing:border-box;"
                onfocus="this.style.borderColor='#C0392B'"
                onblur="this.style.borderColor='#E5E7EB'"
            >{{ $answers[$question->id] ?? '' }}</textarea>
        </div>
        @endif

        <div style="display:flex;justify-content:space-between;gap:10px;margin-top:16px;padding-top:14px;border-top:1px solid #F3F4F6;">
            <button
                wire:click="goTo({{ max(0, $currentIndex - 1) }})"
                @if($currentIndex === 0) disabled @endif
                style="
                    flex:1;padding:11px;border:2px solid #E5E7EB;border-radius:10px;
                    background:white;color:#374151;font-size:14px;font-weight:600;
                    cursor:{{ $currentIndex === 0 ? 'not-allowed' : 'pointer' }};
                    opacity:{{ $currentIndex === 0 ? '0.4' : '1' }};
                    font-family:inherit;
                ">
                ← Sebelumnya
            </button>
            @if($currentIndex < $total - 1)
            <button
                wire:click="goTo({{ $currentIndex + 1 }})"
                style="flex:1;padding:11px;background:#C0392B;border:none;border-radius:10px;color:white;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;">
                Selanjutnya →
            </button>
            @else
            <button
                wire:click="confirmSubmit"
                style="flex:1;padding:11px;background:#27AE60;border:none;border-radius:10px;color:white;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;">
                ✅ Selesai
            </button>
            @endif
        </div>
    </div>

    {{-- Number navigation grid --}}
    <div style="background:white;border-radius:12px;padding:14px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:10px;">
        <div style="font-size:11px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;">Navigasi Soal</div>
        <div style="display:grid;grid-template-columns:repeat(8,1fr);gap:5px;">
            @foreach($this->questions as $i => $q)
            <button
                wire:click="goTo({{ $i }})"
                style="
                    aspect-ratio:1;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;
                    border:2px solid {{ $i === $currentIndex ? '#C0392B' : (isset($answers[$q->id]) ? '#27AE60' : '#E5E7EB') }};
                    background:{{ $i === $currentIndex ? '#C0392B' : (isset($answers[$q->id]) ? '#D5F5E3' : 'white') }};
                    color:{{ $i === $currentIndex ? 'white' : (isset($answers[$q->id]) ? '#1E8449' : '#9CA3AF') }};
                "
            >{{ $i + 1 }}</button>
            @endforeach
        </div>
        <div style="display:flex;gap:12px;margin-top:10px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:4px;font-size:10px;color:#6B7280;">
                <div style="width:12px;height:12px;border-radius:3px;background:#D5F5E3;border:1.5px solid #27AE60;"></div> Terjawab
            </div>
            <div style="display:flex;align-items:center;gap:4px;font-size:10px;color:#6B7280;">
                <div style="width:12px;height:12px;border-radius:3px;background:#C0392B;"></div> Sekarang
            </div>
            <div style="display:flex;align-items:center;gap:4px;font-size:10px;color:#6B7280;">
                <div style="width:12px;height:12px;border-radius:3px;background:white;border:1.5px solid #E5E7EB;"></div> Belum
            </div>
        </div>
    </div>

    @endif
    @endif

    {{-- Submit button --}}
    <div style="background:white;border-radius:12px;padding:14px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:10px;">
        <button
            wire:click="confirmSubmit"
            style="width:100%;padding:13px;background:#27AE60;border:none;border-radius:10px;color:white;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:8px;">
            ✅ Kumpulkan Jawaban
        </button>
        @if($violationCount > 0)
        <div style="margin-top:10px;background:#FDEDEC;border:1px solid #F1948A;border-radius:8px;padding:8px 12px;font-size:12px;color:#C0392B;font-weight:600;text-align:center;">
            ⚠️ Pelanggaran: {{ $violationCount }} — Hati-hati, jangan berpindah tab!
        </div>
        @endif
        <div style="margin-top:8px;background:#FFFBEB;border:1px solid #FCD34D;border-radius:8px;padding:8px 12px;font-size:11px;color:#78350F;line-height:1.6;text-align:center;">
            🔒 Keluar dari halaman akan mengunci ujian. Butuh token re-entry dari guru.
        </div>
    </div>

</div>

{{-- ============================================================
     MODAL KONFIRMASI SUBMIT
     ============================================================ --}}
@if($showSubmitConfirm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:flex-end;justify-content:center;z-index:100;">
    <div style="background:white;border-radius:20px 20px 0 0;padding:24px;width:100%;max-width:480px;box-shadow:0 -8px 30px rgba(0,0,0,0.2);">
        <div style="text-align:center;margin-bottom:20px;">
            <div style="font-size:40px;margin-bottom:10px;">📋</div>
            <h2 style="margin:0 0 6px;font-size:18px;font-weight:800;color:#1F2937;">Kumpulkan Jawaban?</h2>

            @if(!$session->exam->google_form_url)
            @php $unanswered2 = $this->questions->count() - count($answers); @endphp
            <p style="margin:0 0 6px;font-size:14px;color:#6B7280;">
                Terjawab: <strong style="color:#27AE60;">{{ count($answers) }}</strong> dari <strong>{{ $this->questions->count() }}</strong> soal
            </p>
            @if($unanswered2 > 0)
            <div style="background:#FFF3CD;border:1px solid #FFC107;border-radius:8px;padding:8px;font-size:13px;color:#856404;margin-bottom:8px;">
                ⚠️ {{ $unanswered2 }} soal belum dijawab
            </div>
            @else
            <div style="background:#D5F5E3;border:1px solid #27AE60;border-radius:8px;padding:8px;font-size:13px;color:#1E8449;margin-bottom:8px;">
                ✅ Semua soal sudah dijawab
            </div>
            @endif
            @else
            <p style="margin:0 0 10px;font-size:14px;color:#6B7280;">
                Pastikan kamu sudah submit jawaban di Google Form sebelum mengklik tombol ini.
            </p>
            @endif
        </div>

        <div style="display:flex;gap:10px;">
            <button wire:click="$set('showSubmitConfirm', false)"
                style="flex:1;padding:13px;background:white;border:2px solid #E5E7EB;border-radius:10px;font-size:14px;font-weight:600;color:#374151;cursor:pointer;font-family:inherit;">
                Batal
            </button>
            <button wire:click="submit"
                style="flex:2;padding:13px;background:#27AE60;border:none;border-radius:10px;font-size:14px;font-weight:700;color:white;cursor:pointer;font-family:inherit;">
                ✅ Ya, Kumpulkan!
            </button>
        </div>
    </div>
</div>
@endif

{{-- Timer Script --}}
<script>
(function() {
    var endTs = {{ $session->getEndTimestamp() }} * 1000;

    function pad(n) { return String(n).padStart(2, '0'); }

    function tick() {
        var now  = Date.now();
        var left = Math.max(0, Math.floor((endTs - now) / 1000));

        var h = Math.floor(left / 3600);
        var m = Math.floor((left % 3600) / 60);
        var s = left % 60;

        var text = left >= 3600
            ? pad(h) + ':' + pad(m) + ':' + pad(s)
            : pad(m) + ':' + pad(s);

        var el = document.getElementById('timer-display');
        if (el) el.textContent = text;

        var wrap = document.getElementById('exam-timer');
        if (wrap) {
            if (left <= 60) {
                wrap.style.background = 'linear-gradient(135deg,#7B241C,#521810)';
                wrap.style.animation = 'timerPulse 0.8s infinite alternate';
            } else if (left <= 300) {
                wrap.style.background = 'linear-gradient(135deg,#E67E22,#CA6F1E)';
            }
        }

        if (left <= 0) {
            var wireEl = document.querySelector('[wire\\:id]');
            if (wireEl && typeof Livewire !== 'undefined') {
                Livewire.find(wireEl.getAttribute('wire:id'))?.call('submit');
            }
            return;
        }
        setTimeout(tick, 1000);
    }
    tick();
})();
</script>

{{-- Alpine Security --}}
<script>
function examSecurity(sessionId, isGoogleForm) {
    return {
        showViolationOverlay: false,
        redirectCountdown: 5,
        showBlurWarning: false,
        blurCountdown: 8,

        _submitted: false,
        _violationReported: false,
        _blurInterval: null,
        _focusRestored: false,
        // Flag: ada pelanggaran yang perlu ditampilkan saat tab kembali
        _pendingViolation: false,
        _pendingType: '',

        init() {
            // Block right click & shortcuts
            document.addEventListener('contextmenu', e => e.preventDefault());
            document.addEventListener('keydown', e => {
                if ((e.ctrlKey || e.metaKey) && ['c','v','a','p','u','s'].includes(e.key.toLowerCase())) {
                    e.preventDefault();
                }
                if (e.key === 'PrintScreen' || e.key === 'F12') e.preventDefault();
            });

            // ============================================================
            // visibilitychange — deteksi pindah tab
            // Logika kunci:
            //   hidden  → catat pelanggaran di server (snapshot waktu), set flag pending
            //   visible → tampilkan popup baru (siswa sudah balik ke tab ini)
            // ============================================================
            document.addEventListener('visibilitychange', () => {

                if (document.hidden) {
                    // Tab disembunyikan — catat pelanggaran tapi jangan tampilkan
                    // overlay di sini karena tab tersembunyi = tidak bisa dilihat
                    if (!this._violationReported && !this._submitted) {
                        this._violationReported = true;
                        this._pendingViolation  = true;
                        this._pendingType       = 'tab_switch';

                        // Catat ke server & snapshot waktu sekarang
                        this._callLivewireViolation('tab_switch');
                    }
                } else {
                    // Tab kembali visible — sekarang baru tampilkan popup
                    if (this._pendingViolation) {
                        this._pendingViolation = false;
                        this._showViolationAndRedirect();
                    }
                }
            });

            // ============================================================
            // window.blur — pindah aplikasi (bukan pindah tab)
            // Tampilkan warning dengan grace period 8 detik
            // ============================================================
            // window.addEventListener('blur', () => {
            //     if (document.hidden) return;      // sudah ditangani visibilitychange
            //     if (this._violationReported || this._submitted) return;
            //     if (this.showBlurWarning || this.showViolationOverlay) return;

            //     this._startBlurWarning();
            // });

            // window.addEventListener('focus', () => {
            //     if (this.showBlurWarning && !this._violationReported) {
            //         this._cancelBlurViolation();
            //     }
            // });
        },

        // ============================================================
        // Panggil Livewire untuk catat pelanggaran & lock session di server
        // ============================================================
        _callLivewireViolation(type) {
            var wireEl = document.querySelector('[wire\\:id]');
            if (wireEl && typeof Livewire !== 'undefined') {
                Livewire.find(wireEl.getAttribute('wire:id'))
                    ?.call('reportViolationAndLock', type)
                    .catch(() => {});
            }
        },

        // ============================================================
        // Tampilkan popup violation + countdown redirect 5 detik
        // Dipanggil saat tab kembali visible ATAU saat blur timeout
        // ============================================================
        _showViolationAndRedirect() {
            this.showViolationOverlay = true;
            this.redirectCountdown    = 5;

            var self = this;
            var interval = setInterval(function() {
                self.redirectCountdown--;
                if (self.redirectCountdown <= 0) {
                    clearInterval(interval);
                    window.location.href = '/student/dashboard';
                }
            }, 1000);
        },

        // ============================================================
        // Blur warning — grace period 8 detik
        // ============================================================
        _startBlurWarning() {
            this.showBlurWarning = true;
            this.blurCountdown   = 8;
            this._focusRestored  = false;

            this._blurInterval = setInterval(() => {
                this.blurCountdown--;
                if (this.blurCountdown <= 0) {
                    clearInterval(this._blurInterval);
                    this._blurInterval = null;

                    if (!this._focusRestored && !this._violationReported && !this._submitted) {
                        // Grace period habis tanpa kembali → proses sebagai pelanggaran
                        this.showBlurWarning    = false;
                        this._violationReported = true;
                        this._callLivewireViolation('window_blur');
                        this._showViolationAndRedirect();
                    }
                }
            }, 1000);
        },

        _cancelBlurViolation() {
            this._focusRestored = true;
            this.showBlurWarning = false;
            if (this._blurInterval) {
                clearInterval(this._blurInterval);
                this._blurInterval = null;
            }
        },

        // Tombol "Saya Sudah Kembali" di blur warning
        cancelBlurViolation() {
            this._cancelBlurViolation();
        }
    };
}
</script>

<style>
*, *::before, *::after { box-sizing: border-box; }

@keyframes timerPulse {
    from { opacity: 1; }
    to   { opacity: 0.7; }
}

[x-cloak] { display: none !important; }

.btn-digi-success {
    display: inline-flex; align-items: center; gap: 6px;
    background: #27AE60; color: white; border: none;
    border-radius: 10px; padding: 11px 18px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    font-family: inherit; -webkit-appearance: none;
}
.btn-digi-success:disabled { opacity: 0.6; cursor: not-allowed; }
</style>

</div>