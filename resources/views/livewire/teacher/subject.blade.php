{{-- resources/views/livewire/teacher/subject.blade.php --}}
<x-layouts.digitest :title="$subject->name">

@php $activeTab = request()->query('tab', 'ujian'); @endphp

{{-- Back button --}}
<a href="{{ route('teacher.dashboard') }}" wire:navigate
   style="display:inline-flex;align-items:center;gap:6px;color:#C0392B;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:14px;">
    ← Kembali ke Dashboard
</a>

{{-- Judul halaman --}}
<div style="font-size:12px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">
    {{ strtoupper($subject->name) }}
</div>

{{-- ======================== TAB: BANK SOAL ======================== --}}
@if($activeTab === 'soal')
<livewire:teacher.question-bank :subjectId="$subject->id" />
@endif

{{-- ======================== TAB: UJIAN ======================== --}}
@if($activeTab === 'ujian')
<div style="font-size:12px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">
    DAFTAR UJIAN — {{ strtoupper($subject->name) }}
</div>
<livewire:teacher.exam-manager :subjectId="$subject->id" />
@endif

{{-- Tab switcher --}}
<div style="display:flex;background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;margin-top:20px;">
    {{-- <a href="?tab=soal" style="flex:1;padding:12px;text-align:center;font-size:13px;font-weight:600;text-decoration:none;{{ $activeTab==='soal' ? 'color:#C0392B;background:#FDEDEC;' : 'color:#8E8E93;' }}">
        📝 Bank Soal
    </a> --}}
    {{-- <a href="?tab=ujian" style="flex:1;padding:12px;text-align:center;font-size:13px;font-weight:600;text-decoration:none;{{ $activeTab==='ujian' ? 'color:#C0392B;background:#FDEDEC;' : 'color:#8E8E93;' }}">
        🎯 Ujian
    </a> --}}
</div>

</x-layouts.digitest>