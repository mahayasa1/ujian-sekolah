{{-- resources/views/livewire/teacher/subject.blade.php --}}
{{-- Sesuai screenshot: judul "NAMA MAPEL", field "Soal", tombol Simpan + Batal --}}
{{-- Dan halaman DATA MAPEL: grid 3x3 kartu kosong --}}
<x-layouts.digitest :title="$subject->name">

@php $activeTab = request()->query('tab', 'soal'); @endphp

{{-- ======================== TAB: BANK SOAL ======================== --}}
@if($activeTab === 'soal')

{{-- Judul halaman (seperti screenshot "NAMA MAPEL") --}}
<div style="font-size:13px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">
    {{ strtoupper($subject->name) }}
</div>

{{-- Livewire question bank --}}
<livewire:teacher.question-bank :subjectId="$subject->id" />

@endif

{{-- ======================== TAB: UJIAN ======================== --}}
@if($activeTab === 'ujian')

<div style="font-size:13px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;">
    DAFTAR UJIAN — {{ strtoupper($subject->name) }}
</div>

<livewire:teacher.exam-manager :subjectId="$subject->id" />

@endif

{{-- Tab switcher di bawah --}}
<div style="display:flex;background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;margin-top:20px;">
    <a href="?tab=soal" style="flex:1;padding:12px;text-align:center;font-size:14px;font-weight:600;text-decoration:none;{{ $activeTab==='soal' ? 'color:#C0392B;background:#FDEDEC;' : 'color:#8E8E93;' }}">
        📝 Bank Soal
    </a>
    <a href="?tab=ujian" style="flex:1;padding:12px;text-align:center;font-size:14px;font-weight:600;text-decoration:none;{{ $activeTab==='ujian' ? 'color:#C0392B;background:#FDEDEC;' : 'color:#8E8E93;' }}">
        🎯 Ujian
    </a>
</div>

</x-layouts.digitest>