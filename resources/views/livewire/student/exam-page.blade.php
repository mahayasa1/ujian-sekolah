<div
    x-data="examApp(
        {{ $session->id }},
        {{ $session->getTimeLeftSeconds() }},
        {{ now()->timestamp }}
    )"
    x-init="init()"
    style="background:#F3F4F6;min-height:100vh;font-family:sans-serif;"
>

<!-- ================= TIMER ================= -->
<div style="background:#C0392B;color:white;text-align:center;padding:12px;">
    <div style="font-size:12px;">⏱ SISA WAKTU</div>
    <div id="timer-display" style="font-size:28px;font-weight:bold;">--:--</div>
</div>

<!-- ================= VIOLATION POPUP ================= -->
<div x-show="showViolation" x-cloak
    style="position:fixed;inset:0;background:rgba(0,0,0,0.95);display:flex;align-items:center;justify-content:center;z-index:9999;">

    <div style="background:white;border-radius:15px;padding:25px;text-align:center;width:320px;">
        <div style="font-size:40px;">🚫</div>
        <h2 style="color:#C0392B;margin:10px 0;">Pelanggaran!</h2>

        <p style="font-size:14px;color:#444;">
            Kamu berpindah tab saat ujian.<br>
            Ujian dikunci.
        </p>

        <div style="font-size:40px;font-weight:bold;color:#C0392B;" x-text="countdown"></div>

        <p style="font-size:12px;color:#888;">Redirect ke dashboard...</p>
    </div>
</div>

<!-- ================= CONTENT ================= -->
<div style="max-width:800px;margin:0 auto;padding:12px;position:relative;z-index:1;">

    <!-- INFO -->
    <div style="background:white;border-radius:12px;padding:14px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:10px;">
        <div style="font-size:12px;font-weight:700;color:#C0392B;">
            📋 Ujian via Google Form
        </div>
        <div style="font-size:13px;color:#374151;font-weight:500;">
            {{ $session->exam->title }}
        </div>
    </div>

    <!-- GOOGLE FORM -->
    <div style="position:relative;z-index:1;">
        <iframe
            src="{{ $session->exam->google_form_url . (str_contains($session->exam->google_form_url, '?') ? '&' : '?') . 'embedded=true' }}"
            style="width:100%;border:none;border-radius:12px;min-height:75vh;"
        ></iframe>
    </div>

    <!-- SUBMIT -->
    <div style="background:white;border-radius:12px;padding:14px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:10px;">
    
        <button
            wire:click="confirmSubmit"
            wire:loading.attr="disabled"
            style="width:100%;padding:13px;background:#27AE60;border:none;border-radius:10px;color:white;font-size:15px;font-weight:700;cursor:pointer;font-family:inherit;">
            ✅ Kumpulkan Jawaban
        </button>

        @if($violationCount > 0)
        <div style="margin-top:10px;background:#FDEDEC;border:1px solid #F1948A;border-radius:8px;padding:8px 12px;font-size:12px;color:#C0392B;font-weight:600;text-align:center;">
            ⚠️ Pelanggaran: {{ $violationCount }}
        </div>
        @endif

    </div>

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

<script>
function examApp(sessionId, secondsLeft, serverNow) {
    return {

        // 🔥 FIX: gunakan offset server-client
        serverNow: serverNow * 1000,
        clientStart: Date.now(),

        duration: secondsLeft * 1000,

        showViolation: false,
        countdown: 5,
        violationTriggered: false,

        init() {
            this.startTimer();

            // BLOCK
            document.addEventListener('contextmenu', e => e.preventDefault());
            document.addEventListener('keydown', e => {
                if ((e.ctrlKey || e.metaKey) && ['c','v','a','u','s','p'].includes(e.key.toLowerCase())) {
                    e.preventDefault();
                }
                if (e.key === 'F12') e.preventDefault();
            });

            // TAB SWITCH
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    if (!this.violationTriggered) {
                        this.violationTriggered = true;
                        this.reportViolation();
                    }
                } else {
                    if (this.violationTriggered) {
                        this.showViolationPopup();
                    }
                }
            });
        },

        // 🔥 TIMER SUPER STABIL (ANTI DRIFT)
        startTimer() {
            const tick = () => {

                // waktu server sekarang = waktu awal server + selisih waktu client
                let nowServer = this.serverNow + (Date.now() - this.clientStart);

                // waktu habis = waktu server awal + durasi
                let endTime = this.serverNow + this.duration;

                let left = Math.max(0, Math.floor((endTime - nowServer) / 1000));

                let m = Math.floor(left / 60);
                let s = left % 60;

                let el = document.getElementById('timer-display');
                if (el) {
                    el.innerText =
                        String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                }

                if (left <= 0) {
                    let wireEl = document.querySelector('[wire\\:id]');
                    if (wireEl && typeof Livewire !== 'undefined') {
                        Livewire.find(wireEl.getAttribute('wire:id'))
                            ?.call('submit');
                    }
                    return;
                }

                setTimeout(tick, 1000);
            };

            tick();
        },

        showViolationPopup() {
            this.showViolation = true;

            let interval = setInterval(() => {
                this.countdown--;

                if (this.countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = '/student/dashboard';
                }
            }, 1000);
        },

        reportViolation() {
            let wireEl = document.querySelector('[wire\\:id]');
            if (wireEl && typeof Livewire !== 'undefined') {
                Livewire.find(wireEl.getAttribute('wire:id'))
                    ?.call('reportViolationAndLock', 'tab_switch');
            }
        }
    };
}
</script>
</div>
</div>