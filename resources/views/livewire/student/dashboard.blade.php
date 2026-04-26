<div class="">

@php
    $student = auth()->user()->student;
@endphp
 
{{-- Kartu Selamat Datang --}}
<div style="background:white;border-radius:12px;padding:20px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:20px;">
    <div style="font-size:18px;font-weight:600;color:#1C1C1E;margin-bottom:16px;">Selamat Datang !</div>
 
    {{-- Nama --}}
    <div style="margin-bottom:10px;">
        <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:6px;">Nama</div>
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px 12px;font-size:15px;color:#1C1C1E;min-height:40px;">
            {{ auth()->user()->name }}
        </div>
    </div>
 
    {{-- Kelas --}}
    <div style="margin-bottom:10px;">
        <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:6px;">Kelas</div>
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px 12px;font-size:15px;color:#1C1C1E;min-height:40px;">
            {{ $student?->classRoom?->name ?? '-' }}
        </div>
    </div>
 
    {{-- NISN --}}
    <div style="margin-bottom:10px;">
        <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:6px;">NISN</div>
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px 12px;font-size:15px;color:#1C1C1E;min-height:40px;">
            {{ $student?->nis ?? '-' }}
        </div>
    </div>
 
    {{-- NIS --}}
    <div>
        <div style="font-size:13px;font-weight:500;color:#1C1C1E;margin-bottom:6px;">NIS</div>
        <div style="background:#F2F2F7;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px 12px;font-size:15px;color:#1C1C1E;min-height:40px;">
            {{ $student?->nis ?? '-' }}
        </div>
    </div>
</div>
 
{{-- Label Mata Pelajaran --}}
<div style="font-size:17px;font-weight:600;color:#1C1C1E;margin-bottom:12px;">Mata Pelajaran</div>
 
{{-- Grid 3 kolom --}}
@if($exams->isEmpty())
<div style="background:white;border-radius:12px;padding:40px 16px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
    <div style="font-size:32px;margin-bottom:10px;">📭</div>
    <div style="font-size:14px;color:#8E8E93;font-weight:500;">Belum ada ujian aktif untuk kelas Anda</div>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
    @php
        $icons = ['Matematika'=>'➕','IPA'=>'🔬','IPS'=>'🌍','B. Indonesia'=>'📝','B. Inggris'=>'🇬🇧','PPKn'=>'🏛️','Agama'=>'🕌','Seni'=>'🎨','Olahraga'=>'⚽'];
    @endphp
    @foreach($exams as $exam)
    @php
        $session = $exam->session;
        $isDone = $session && $session->status === 'selesai';
        $isOngoing = $session && $session->status === 'aktif';
        $icon = $icons[$exam->subject->name] ?? '📘';
    @endphp
    <a href="{{ $isDone ? route('student.result', $session->id) : ($isOngoing ? route('student.exam', $session->id) : route('student.token', $exam->id)) }}"
       style="background:white;border-radius:12px;aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);cursor:pointer;padding:12px 8px;text-align:center;text-decoration:none;gap:6px;position:relative;overflow:hidden;">
        @if($isDone)
        <div style="position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:#34C759;"></div>
        @elseif($isOngoing)
        <div style="position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:#FF9500;"></div>
        @endif
        <div style="font-size:28px;line-height:1;">{{ $icon }}</div>
        <div style="font-size:11px;font-weight:600;color:#1C1C1E;line-height:1.2;">{{ Str::limit($exam->subject->name, 10) }}</div>
        @if($isDone)
        <div style="font-size:10px;color:#34C759;font-weight:700;">{{ $session->score }}</div>
        @endif
    </a>
    @endforeach
 
    {{-- Isi sisa grid dengan kartu kosong jika kurang dari 9 --}}
    @for($i = $exams->count(); $i < 9; $i++)
    <div style="background:white;border-radius:12px;aspect-ratio:1;box-shadow:0 1px 3px rgba(0,0,0,0.08);"></div>
    @endfor
</div>
@endif
 
{{-- Riwayat ujian (jika ada) --}}
@if($completedSessions->isNotEmpty())
<div style="margin-top:24px;">
    <div style="font-size:17px;font-weight:600;color:#1C1C1E;margin-bottom:12px;">Riwayat Ujian</div>
    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">
        @foreach($completedSessions as $ses)
        <a href="{{ route('student.result', $ses->id) }}" style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:0.5px solid #E5E5EA;text-decoration:none;color:inherit;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:600;color:#1C1C1E;margin-bottom:2px;">{{ $ses->exam->subject->name }}</div>
                <div style="font-size:12px;color:#8E8E93;">{{ $ses->submitted_at?->format('d M Y') }}</div>
            </div>
            <div style="font-size:20px;font-weight:700;color:{{ ($ses->score ?? 0) >= 75 ? '#34C759' : '#C0392B' }};margin-left:12px;">
                {{ $ses->score ?? '-' }}
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
</div>