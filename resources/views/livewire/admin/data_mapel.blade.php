{{-- resources/views/livewire/admin/data-mapel.blade.php --}}
{{-- Sesuai screenshot: title "DATA MAPEL" + grid 3x3 kartu mata pelajaran --}}
<x-layouts.digitest :title="'Data Mapel'">

{{-- Title --}}
<div style="font-size:13px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;">DATA MAPEL</div>

{{-- Grid 3x3 mapel --}}
@php
    $subjects = \App\Models\Subject::with('teacher')->get();
    $icons = ['Matematika'=>'➕','IPA'=>'🔬','IPS'=>'🌍','B. Indonesia'=>'📝','B. Inggris'=>'🇬🇧','PPKn'=>'🏛️','Agama'=>'🕌','Seni'=>'🎨','Olahraga'=>'⚽'];
@endphp

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
    @foreach($subjects as $subject)
    <a href="#" style="background:white;border-radius:12px;aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);cursor:pointer;padding:12px 8px;text-align:center;text-decoration:none;gap:6px;">
        <div style="font-size:28px;line-height:1;">{{ $icons[$subject->name] ?? '📘' }}</div>
        <div style="font-size:11px;font-weight:600;color:#1C1C1E;line-height:1.2;">{{ Str::limit($subject->name, 10) }}</div>
    </a>
    @endforeach

    {{-- Isi sisa ke 9 --}}
    @for($i = $subjects->count(); $i < 9; $i++)
    <div style="background:white;border-radius:12px;aspect-ratio:1;box-shadow:0 1px 3px rgba(0,0,0,0.08);"></div>
    @endfor
</div>

</x-layouts.digitest>