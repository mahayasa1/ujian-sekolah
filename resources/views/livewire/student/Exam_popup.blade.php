{{-- resources/views/livewire/student/exam-popup.blade.php --}}
{{--
    Komponen popup token ujian.
    Dipanggil dari livewire.student.dashboard via:
        <livewire:student.exam-popup />
    Dan di-trigger via dispatch event:
        $dispatch('open-exam-popup', { examId: examId })
--}}

@if($examId && $exam)
{{-- ============================================================
     OVERLAY BACKDROP
     Klik di luar popup → tutup popup
     ============================================================ --}}
<div
    style="position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px;"
    wire:click.self="closePopup"
>
    {{-- ============================================================
         POPUP CARD
         ============================================================ --}}
    <div style="background:white;border-radius:16px;width:100%;max-width:360px;box-shadow:0 20px 60px rgba(0,0,0,0.25);overflow:hidden;">

        {{-- ---- HEADER POPUP (gradient merah) ---- --}}
        <div style="background:linear-gradient(135deg,#C0392B,#922B21);padding:20px 16px;text-align:center;position:relative;">

            {{-- Tombol tutup (✕) --}}
            <button
                wire:click="closePopup"
                style="position:absolute;top:12px;right:14px;background:rgba(255,255,255,0.2);border:none;color:white;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;font-family:inherit;"
                aria-label="Tutup popup"
            >✕</button>

            {{-- Ikon mata pelajaran --}}
            @php
                $icons = [
                    'Matematika' => '➕', 'IPA' => '🔬', 'IPS' => '🌍',
                    'B. Indonesia' => '📝', 'B. Inggris' => '🇬🇧',
                    'PPKn' => '🏛️', 'Agama' => '🕌', 'Seni' => '🎨',
                    'Olahraga' => '⚽',
                ];
                $icon = $icons[$exam->subject->name] ?? '📘';
            @endphp

            <div style="font-size:36px;margin-bottom:8px;">{{ $icon }}</div>

            <div style="font-size:16px;font-weight:700;color:white;margin-bottom:2px;">
                {{ $exam->title }}
            </div>
            <div style="font-size:12px;color:rgba(255,255,255,0.8);">
                {{ $exam->subject->name }}
            </div>
        </div>

        {{-- ---- INFO UJIAN (durasi & kelas) ---- --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;border-bottom:0.5px solid #E5E5EA;">
            <div style="padding:12px;text-align:center;border-right:0.5px solid #E5E5EA;">
                <div style="font-size:18px;font-weight:700;color:#C0392B;">
                    {{ $exam->duration }}
                </div>
                <div style="font-size:11px;color:#8E8E93;">menit</div>
            </div>
            <div style="padding:12px;text-align:center;">
                <div style="font-size:18px;font-weight:700;color:#C0392B;">
                    {{ $exam->classRoom?->name ?? 'Semua' }}
                </div>
                <div style="font-size:11px;color:#8E8E93;">kelas</div>
            </div>
        </div>

        {{-- ---- INPUT TOKEN ---- --}}
        <div style="padding:20px 16px;">

            {{-- Label input token --}}
            <div style="text-align:center;margin-bottom:16px;">
                <div style="font-size:24px;margin-bottom:6px;">🔑</div>
                <div style="font-size:15px;font-weight:600;color:#1C1C1E;">Masukkan Token Ujian</div>
                <div style="font-size:13px;color:#8E8E93;margin-top:2px;">
                    Minta token dari guru pengawas
                </div>
            </div>

            {{-- Field token --}}
            <input
                type="text"
                wire:model="tokenInput"
                placeholder="Contoh: ABC123"
                maxlength="10"
                autocomplete="off"
                autocapitalize="characters"
                wire:keydown.enter="submitToken"
                style="
                    width:100%;
                    text-align:center;
                    font-size:24px;
                    font-weight:700;
                    letter-spacing:0.3em;
                    padding:14px 12px;
                    border:2px solid {{ $tokenError ? '#C0392B' : '#E5E5EA' }};
                    border-radius:10px;
                    color:#C0392B;
                    text-transform:uppercase;
                    background:#F9FAFB;
                    outline:none;
                    font-family:inherit;
                    box-sizing:border-box;
                "
                onfocus="this.style.borderColor='#C0392B';this.style.background='white';"
                onblur="this.style.background='#F9FAFB';"
            >

            {{-- Pesan error token --}}
            @if($tokenError)
            <div style="background:#FDEDEC;border:0.5px solid #F1948A;color:#C0392B;padding:10px 12px;border-radius:8px;font-size:13px;font-weight:500;margin-top:10px;display:flex;align-items:center;gap:6px;">
                <span>⚠️</span>
                <span>{{ $tokenError }}</span>
            </div>
            @endif

            {{-- Peringatan keamanan ujian --}}
            <div style="background:#FFFBEB;border:0.5px solid #FCD34D;border-radius:8px;padding:10px 12px;margin-top:12px;font-size:12px;color:#78350F;line-height:1.6;">
                <strong>⚠️ Perhatian:</strong> Setelah ujian dimulai, jangan berpindah tab atau jendela.
                Berpindah tab akan menghentikan ujian secara otomatis.
            </div>
        </div>

        {{-- ---- TOMBOL AKSI ---- --}}
        <div style="padding:0 16px 20px;display:flex;gap:10px;">

            {{-- Tombol Batal --}}
            <button
                wire:click="closePopup"
                style="flex:1;padding:12px;background:white;border:1px solid #E5E5EA;border-radius:10px;font-size:14px;font-weight:600;color:#8E8E93;cursor:pointer;font-family:inherit;"
            >
                Batal
            </button>

            {{-- Tombol Mulai Ujian --}}
            <button
                wire:click="submitToken"
                wire:loading.attr="disabled"
                style="flex:2;padding:12px;background:#C0392B;border:none;border-radius:10px;font-size:14px;font-weight:700;color:white;cursor:pointer;font-family:inherit;"
            >
                <span wire:loading.remove wire:target="submitToken">🚀 Mulai Ujian</span>
                <span wire:loading wire:target="submitToken">⏳ Memverifikasi...</span>
            </button>
        </div>

    </div>{{-- end popup card --}}
</div>{{-- end overlay --}}
@endif