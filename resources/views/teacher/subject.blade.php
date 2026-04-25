{{-- resources/views/livewire/teacher/subject.blade.php --}}
{{-- This page acts as the hub for a specific subject: shows tabs for Soal & Ujian --}}
<x-layouts.digitest :title="$subject->name">

@php
    $activeTab = request()->query('tab', 'soal');
    $icons = ['Matematika'=>'➕','IPA'=>'🔬','IPS'=>'🌍','B. Indonesia'=>'📝','B. Inggris'=>'🇬🇧','PPKn'=>'🏛️','Agama'=>'🕌','Seni'=>'🎨','Olahraga'=>'⚽'];
    $icon = $icons[$subject->name] ?? '📘';
@endphp

{{-- Page header --}}
<div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
    <div style="width:52px;height:52px;border-radius:0.875rem;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:1.75rem;flex-shrink:0;">
        {{ $icon }}
    </div>
    <div>
        <div style="font-size:0.75rem;color:#9CA3AF;font-weight:600;">MATA PELAJARAN</div>
        <h1 style="margin:0;font-size:1.2rem;font-weight:800;color:#1F2937;">{{ $subject->name }}</h1>
        <div style="font-size:0.8rem;color:#6B7280;">Kode: {{ $subject->code ?? '-' }}</div>
    </div>
</div>

{{-- Tab Navigation --}}
<div style="display:flex;gap:0.25rem;background:#F3F4F6;border-radius:0.75rem;padding:0.3rem;margin-bottom:1.5rem;width:fit-content;">
    <a href="?tab=soal"
       style="padding:0.5rem 1.25rem;border-radius:0.5rem;font-size:0.875rem;font-weight:600;text-decoration:none;transition:all 0.15s;
              {{ $activeTab === 'soal' ? 'background:white;color:#C0392B;box-shadow:0 1px 4px rgba(0,0,0,0.08);' : 'color:#6B7280;' }}">
        📝 Bank Soal
    </a>
    <a href="?tab=ujian"
       style="padding:0.5rem 1.25rem;border-radius:0.5rem;font-size:0.875rem;font-weight:600;text-decoration:none;transition:all 0.15s;
              {{ $activeTab === 'ujian' ? 'background:white;color:#C0392B;box-shadow:0 1px 4px rgba(0,0,0,0.08);' : 'color:#6B7280;' }}">
        🎯 Daftar Ujian
    </a>
</div>

@if(session('success'))
<div style="background:#D5F5E3;border:1px solid #27AE60;color:#1E8449;padding:0.75rem 1rem;border-radius:0.5rem;margin-bottom:1rem;font-size:0.85rem;font-weight:500;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- TAB: BANK SOAL --}}
@if($activeTab === 'soal')
<livewire:teacher.question-bank :subjectId="$subject->id" />
@endif

{{-- TAB: UJIAN --}}
@if($activeTab === 'ujian')
<livewire:teacher.exam-manager :subjectId="$subject->id" />
@endif

</x-layouts.digitest>
