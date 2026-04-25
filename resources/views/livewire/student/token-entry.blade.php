{{-- resources/views/livewire/student/token-entry.blade.php --}}
<x-layouts.digitest :title="'Token Ujian'">

<div style="max-width:480px;margin:2rem auto;">
    {{-- Exam info card --}}
    <div class="digi-card" style="margin-bottom:1.5rem;border-top:4px solid #C0392B;">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
            <div style="width:48px;height:48px;border-radius:0.75rem;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:1.5rem;">📋</div>
            <div>
                <h2 style="margin:0;font-size:1rem;font-weight:700;color:#1F2937;">{{ $exam->title }}</h2>
                <p style="margin:0;font-size:0.8rem;color:#6B7280;">{{ $exam->subject->name }}</p>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.75rem;padding-top:1rem;border-top:1px solid #F3F4F6;">
            <div style="text-align:center;">
                <div style="font-size:1.25rem;font-weight:700;color:#C0392B;">{{ $exam->duration }}</div>
                <div style="font-size:0.7rem;color:#9CA3AF;">menit</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.25rem;font-weight:700;color:#C0392B;">{{ $exam->total_questions }}</div>
                <div style="font-size:0.7rem;color:#9CA3AF;">soal</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.25rem;font-weight:700;color:#C0392B;">{{ $exam->classRoom?->name ?? 'Semua' }}</div>
                <div style="font-size:0.7rem;color:#9CA3AF;">kelas</div>
            </div>
        </div>
    </div>

    {{-- Token input --}}
    <div class="digi-card">
        <div style="text-align:center;margin-bottom:1.5rem;">
            <div style="font-size:2.5rem;margin-bottom:0.5rem;">🔑</div>
            <h2 style="margin:0 0 0.25rem;font-size:1.1rem;font-weight:700;color:#1F2937;">Masukkan Token Ujian</h2>
            <p style="margin:0;font-size:0.85rem;color:#6B7280;">Minta token kepada guru pengawas Anda</p>
        </div>

        <form wire:submit="submit">
            <div style="margin-bottom:1rem;">
                <input
                    type="text"
                    wire:model="token"
                    class="token-input"
                    placeholder="XXXXXX"
                    maxlength="10"
                    autocomplete="off"
                    autofocus
                >
                @if($error)
                <div style="background:#FDEDEC;border:1px solid #F1948A;color:#C0392B;padding:0.65rem 1rem;border-radius:0.5rem;font-size:0.825rem;font-weight:500;margin-top:0.75rem;display:flex;align-items:center;gap:0.5rem;">
                    <span>⚠️</span> {{ $error }}
                </div>
                @endif
            </div>

            <button type="submit" class="btn-digi-primary" style="width:100%;justify-content:center;padding:0.9rem;font-size:0.95rem;" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="margin-right:0.4rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    Mulai Ujian
                </span>
                <span wire:loading>⏳ Memverifikasi...</span>
            </button>
        </form>

        <div style="margin-top:1rem;text-align:center;">
            <a href="{{ route('student.dashboard') }}" wire:navigate style="color:#9CA3AF;font-size:0.8rem;text-decoration:none;">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Instructions --}}
    <div style="background:#FFFBEB;border:1px solid #FCD34D;border-radius:0.75rem;padding:1rem 1.25rem;margin-top:1rem;">
        <p style="margin:0 0 0.5rem;font-size:0.8rem;font-weight:700;color:#92400E;">📌 Perhatian Sebelum Ujian:</p>
        <ul style="margin:0;padding-left:1.25rem;font-size:0.78rem;color:#78350F;line-height:1.8;">
            <li>Pastikan koneksi internet stabil</li>
            <li>Jangan berpindah tab selama ujian berlangsung</li>
            <li>Jawaban tersimpan otomatis setiap 30 detik</li>
            <li>Pelanggaran 3x = ujian otomatis dikumpulkan</li>
        </ul>
    </div>
</div>

</x-layouts.digitest>
