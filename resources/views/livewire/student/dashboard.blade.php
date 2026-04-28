{{-- resources/views/livewire/student/dashboard.blade.php --}}
{{-- UPDATED: Added NISN display --}}
<div>

@php
    $student = auth()->user()->student;
    $icons   = ['Matematika'=>'➕','IPA'=>'🔬','IPS'=>'🌍','B. Indonesia'=>'📝','B. Inggris'=>'🇬🇧','PPKn'=>'🏛️','Agama'=>'🕌','Seni'=>'🎨','Olahraga'=>'⚽'];
@endphp

{{-- Kartu Selamat Datang --}}
<div style="background:white;border-radius:12px;padding:20px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:20px;">
    <div style="font-size:18px;font-weight:600;color:#1C1C1E;margin-bottom:16px;">Selamat Datang !</div>

    <div style="margin-bottom:10px;">
        <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:6px;">Nama</div>
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px 12px;font-size:15px;color:#1C1C1E;">
            {{ auth()->user()->name }}
        </div>
    </div>

    <div style="margin-bottom:10px;">
        <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:6px;">Kelas</div>
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px 12px;font-size:15px;color:#1C1C1E;">
            {{ $student?->classRoom?->name ?? '-' }}
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
            <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:6px;">NIS</div>
            <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px 12px;font-size:15px;color:#1C1C1E;">
                {{ $student?->nis ?? '-' }}
            </div>
        </div>
        <div>
            <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:6px;">NISN</div>
            <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px 12px;font-size:15px;color:#1C1C1E;">
                {{ $student?->nisn ?? '-' }}
            </div>
        </div>
    </div>
</div>

{{-- Label Ujian --}}
<div style="font-size:17px;font-weight:600;color:#1C1C1E;margin-bottom:12px;">Ujian Tersedia</div>

{{-- Grid ujian --}}
@if($exams->isEmpty())
<div style="background:white;border-radius:12px;padding:40px 16px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
    <div style="font-size:32px;margin-bottom:10px;">📭</div>
    <div style="font-size:14px;color:#8E8E93;font-weight:500;">Belum ada ujian aktif untuk kelas Anda</div>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
    @foreach($exams as $exam)
    @php
        $session   = $exam->session;
        $isDone    = $session && $session->status === 'selesai';
        $isOngoing = $session && $session->status === 'aktif' && !$session->reentry_token;
        $isLocked  = $session && $session->status === 'aktif' && $session->reentry_token;
        $icon      = $icons[$exam->subject->name] ?? '📘';
    @endphp

    @if($isDone)
        <a href="{{ route('student.result', $session->id) }}"
           style="background:white;border-radius:12px;aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);cursor:pointer;padding:12px 8px;text-align:center;text-decoration:none;gap:6px;position:relative;">
            <div style="position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:#34C759;"></div>
            <div style="font-size:28px;line-height:1;">{{ $icon }}</div>
            <div style="font-size:11px;font-weight:600;color:#1C1C1E;line-height:1.2;">{{ Str::limit($exam->subject->name, 10) }}</div>
            <div style="font-size:9px;color:#8E8E93;line-height:1.2;text-align:center;">{{ Str::limit($exam->title, 12) }}</div>
            <div style="font-size:10px;color:#34C759;font-weight:700;">{{ $session->score ?? '-' }}</div>
        </a>

    @elseif($isLocked)
        <button wire:click="selectExam({{ $exam->id }})"
            style="background:white;border-radius:12px;aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);cursor:pointer;padding:12px 8px;text-align:center;border:2px solid #FCD34D;gap:6px;width:100%;position:relative;">
            <div style="position:absolute;top:6px;right:6px;font-size:12px;">🔒</div>
            <div style="font-size:28px;line-height:1;">{{ $icon }}</div>
            <div style="font-size:11px;font-weight:600;color:#1C1C1E;line-height:1.2;">{{ Str::limit($exam->subject->name, 10) }}</div>
            <div style="font-size:9px;color:#8E8E93;line-height:1.2;text-align:center;">{{ Str::limit($exam->title, 12) }}</div>
            <div style="font-size:10px;color:#92400E;font-weight:600;">Re-Entry</div>
        </button>

    @elseif($isOngoing)
        <a href="{{ route('student.exam', $session->id) }}"
           style="background:white;border-radius:12px;aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);cursor:pointer;padding:12px 8px;text-align:center;text-decoration:none;gap:6px;position:relative;">
            <div style="position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:#FF9500;animation:pulse-dot 1.5s infinite;"></div>
            <div style="font-size:28px;line-height:1;">{{ $icon }}</div>
            <div style="font-size:11px;font-weight:600;color:#1C1C1E;line-height:1.2;">{{ Str::limit($exam->subject->name, 10) }}</div>
            <div style="font-size:9px;color:#8E8E93;line-height:1.2;text-align:center;">{{ Str::limit($exam->title, 12) }}</div>
            <div style="font-size:10px;color:#FF9500;font-weight:600;">Lanjutkan</div>
        </a>

    @else
        <button wire:click="selectExam({{ $exam->id }})"
            style="background:white;border-radius:12px;aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);cursor:pointer;padding:12px 8px;text-align:center;border:none;gap:6px;width:100%;">
            <div style="font-size:28px;line-height:1;">{{ $icon }}</div>
            <div style="font-size:11px;font-weight:600;color:#1C1C1E;line-height:1.2;">{{ Str::limit($exam->subject->name, 10) }}</div>
            <div style="font-size:9px;color:#8E8E93;line-height:1.2;text-align:center;">{{ Str::limit($exam->title, 12) }}</div>
            <div style="font-size:10px;color:#C0392B;font-weight:600;">Mulai</div>
        </button>
    @endif

    @endforeach

    {{-- Fill grid to minimum 9 cells --}}
    @for($i = $exams->count(); $i < max(9, $exams->count()); $i++)
    <div style="background:white;border-radius:12px;aspect-ratio:1;box-shadow:0 1px 3px rgba(0,0,0,0.08);opacity:0.4;"></div>
    @endfor
</div>
@endif

{{-- Riwayat ujian --}}
@if($completedSessions->isNotEmpty())
<div style="margin-top:24px;">
    <div style="font-size:17px;font-weight:600;color:#1C1C1E;margin-bottom:12px;">Riwayat Ujian</div>
    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">
        @foreach($completedSessions as $ses)
        <a href="{{ route('student.result', $ses->id) }}"
           style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:0.5px solid #E5E5EA;text-decoration:none;color:inherit;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:600;color:#1C1C1E;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $ses->exam->title }}</div>
                <div style="font-size:12px;color:#8E8E93;">{{ $ses->exam->subject->name }} · {{ $ses->submitted_at?->format('d M Y') }}</div>
            </div>
            <div style="font-size:20px;font-weight:700;color:{{ ($ses->score ?? 0) >= 75 ? '#34C759' : '#C0392B' }};margin-left:12px;flex-shrink:0;">
                {{ $ses->score ?? '-' }}
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ============================================================
     POPUP TOKEN UJIAN (mulai baru ATAU re-entry)
     ============================================================ --}}
@if($selectedExamId && $selectedExam)
<div
    style="position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:200;display:flex;align-items:flex-end;justify-content:center;"
    wire:click.self="closePopup"
>
    <div style="background:white;border-radius:20px 20px 0 0;width:100%;max-width:480px;box-shadow:0 -8px 40px rgba(0,0,0,0.25);overflow:hidden;max-height:92vh;overflow-y:auto;">

        {{-- Header popup --}}
        <div style="background:linear-gradient(135deg,{{ $isReentry ? '#92400E' : '#C0392B' }},{{ $isReentry ? '#78350F' : '#922B21' }});padding:24px 16px 20px;text-align:center;position:relative;">
            <button wire:click="closePopup"
                style="position:absolute;top:12px;right:14px;background:rgba(255,255,255,0.2);border:none;color:white;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:15px;display:flex;align-items:center;justify-content:center;font-family:inherit;">
                ✕
            </button>
            <div style="font-size:40px;margin-bottom:10px;">
                @if($isReentry) 🔒 @else {{ $icons[$selectedExam->subject->name] ?? '📘' }} @endif
            </div>
            <div style="font-size:17px;font-weight:700;color:white;margin-bottom:4px;">
                @if($isReentry) Masuk Kembali ke Ujian @else {{ $selectedExam->title }} @endif
            </div>
            <div style="font-size:13px;color:rgba(255,255,255,0.8);">
                {{ $selectedExam->subject->name }}
            </div>
        </div>

        {{-- Info ujian --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;border-bottom:0.5px solid #E5E5EA;">
            <div style="padding:14px;text-align:center;border-right:0.5px solid #E5E5EA;">
                <div style="font-size:20px;font-weight:700;color:{{ $isReentry ? '#92400E' : '#C0392B' }};">{{ $selectedExam->duration }}</div>
                <div style="font-size:12px;color:#8E8E93;">menit</div>
            </div>
            <div style="padding:14px;text-align:center;">
                <div style="font-size:20px;font-weight:700;color:{{ $isReentry ? '#92400E' : '#C0392B' }};">{{ $selectedExam->classRoom?->name ?? 'Semua' }}</div>
                <div style="font-size:12px;color:#8E8E93;">kelas</div>
            </div>
        </div>

        {{-- Info reentry --}}
        @if($isReentry && $selectedSession)
        <div style="background:#FFFBEB;border-bottom:0.5px solid #FCD34D;padding:12px 16px;">
            <div style="font-size:12px;color:#78350F;line-height:1.7;">
                <strong>⏰ Info Sesi Ujian:</strong><br>
                @if($selectedSession->last_violation_at)
                Keluar pada: <strong>{{ $selectedSession->last_violation_at->format('H:i:s') }}</strong><br>
                @endif
                @php $remaining = $selectedSession->getTimeLeftSeconds(); $remMins = floor($remaining/60); $remSecs = $remaining % 60; @endphp
                Sisa waktu: <strong>{{ $remMins }} menit {{ $remSecs }} detik</strong>
            </div>
        </div>
        @endif

        {{-- Input token --}}
        <div style="padding:20px 16px;">
            <div style="text-align:center;margin-bottom:16px;">
                <div style="font-size:26px;margin-bottom:8px;">{{ $isReentry ? '🗝️' : '🔑' }}</div>
                <div style="font-size:16px;font-weight:700;color:#1C1C1E;">
                    {{ $isReentry ? 'Masukkan Token Re-Entry' : 'Masukkan Token Ujian' }}
                </div>
                <div style="font-size:13px;color:#8E8E93;margin-top:4px;">
                    {{ $isReentry ? 'Token diberikan guru setelah pelanggaran' : 'Minta token dari guru pengawas' }}
                </div>
            </div>

            <input
                type="text"
                wire:model="tokenInput"
                placeholder="{{ $isReentry ? 'Token Re-Entry' : 'Contoh: ABC123' }}"
                maxlength="12"
                autocomplete="off"
                autocapitalize="characters"
                wire:keydown.enter="submitToken"
                style="width:100%;text-align:center;font-size:26px;font-weight:700;letter-spacing:0.25em;padding:16px 12px;border:2px solid {{ $tokenError ? '#C0392B' : '#E5E5EA' }};border-radius:12px;color:{{ $isReentry ? '#92400E' : '#C0392B' }};text-transform:uppercase;background:#F9FAFB;outline:none;font-family:inherit;box-sizing:border-box;"
                onfocus="this.style.borderColor='{{ $isReentry ? '#92400E' : '#C0392B' }}';this.style.background='white';"
                onblur="this.style.background='#F9FAFB';"
            >

            @if($tokenError)
            <div style="background:#FDEDEC;border:0.5px solid #F1948A;color:#C0392B;padding:10px 12px;border-radius:8px;font-size:13px;font-weight:500;margin-top:10px;display:flex;align-items:center;gap:6px;">
                <span>⚠️</span> {{ $tokenError }}
            </div>
            @endif

            @if(!$isReentry)
            <div style="background:#FFFBEB;border:0.5px solid #FCD34D;border-radius:8px;padding:10px 12px;margin-top:12px;font-size:12px;color:#78350F;line-height:1.6;">
                <strong>⚠️ Perhatian:</strong> Keluar dari halaman ujian akan mengunci ujian. Kamu butuh token re-entry dari guru untuk kembali.
            </div>
            @endif
        </div>

        {{-- Tombol --}}
        <div style="padding:0 16px 24px;display:flex;gap:10px;">
            <button wire:click="closePopup"
                style="flex:1;padding:14px;background:white;border:2px solid #E5E5EA;border-radius:12px;font-size:14px;font-weight:600;color:#8E8E93;cursor:pointer;font-family:inherit;">
                Batal
            </button>
            <button wire:click="submitToken" wire:loading.attr="disabled"
                style="flex:2;padding:14px;background:{{ $isReentry ? '#92400E' : '#C0392B' }};border:none;border-radius:12px;font-size:15px;font-weight:700;color:white;cursor:pointer;font-family:inherit;">
                <span wire:loading.remove>{{ $isReentry ? '🗝️ Lanjutkan Ujian' : '🚀 Mulai Ujian' }}</span>
                <span wire:loading>⏳ Memverifikasi...</span>
            </button>
        </div>
    </div>
</div>
@endif

<style>
@keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:0.4} }
</style>

</div>