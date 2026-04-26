<div>
 
@php $teacher = auth()->user()->teacher; @endphp
 
{{-- Kartu Selamat Datang --}}
<div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:16px;">
    <div style="font-size:12px;color:#8E8E93;font-weight:500;margin-bottom:4px;">Selamat Datang !</div>
    <div style="font-size:15px;font-weight:600;color:#1C1C1E;margin-bottom:2px;">{{ auth()->user()->name }}</div>
    <div style="font-size:13px;color:#8E8E93;">Guru {{ $subjects->first()?->name ?? '' }}</div>
</div>
 
{{-- Label Dashboard --}}
<div style="font-size:17px;font-weight:600;color:#1C1C1E;margin-bottom:12px;">Dashboard</div>
 
{{-- Kartu setiap mata pelajaran --}}
@forelse($subjects as $subject)
<a href="{{ route('teacher.subject', $subject->id) }}" style="background:white;border-radius:12px;padding:18px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);display:block;text-decoration:none;color:inherit;margin-bottom:10px;">
    <div style="font-size:14px;font-weight:600;color:#1C1C1E;margin-bottom:2px;">Mapel</div>
    <div style="font-size:15px;color:#8E8E93;">{{ $subject->name }}</div>
</a>
@empty
<div style="background:white;border-radius:12px;padding:40px 16px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
    <div style="font-size:32px;margin-bottom:10px;">📚</div>
    <div style="font-size:14px;color:#8E8E93;font-weight:500;">Belum ada mata pelajaran. Hubungi admin.</div>
</div>
@endforelse
 
{{-- Ujian aktif (jika ada) --}}
@if($activeExams->isNotEmpty())
<div style="margin-top:8px;">
    <div style="font-size:17px;font-weight:600;color:#1C1C1E;margin-bottom:12px;">Ujian Aktif</div>
    @foreach($activeExams as $exam)
    @php
        $ongoingCount = $exam->sessions->where('status','aktif')->count();
        $finishedCount = $exam->sessions->where('status','selesai')->count();
    @endphp
    <div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:10px;">
        <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:12px;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:14px;font-weight:600;color:#1C1C1E;margin-bottom:2px;">{{ Str::limit($exam->title, 40) }}</div>
                <div style="font-size:12px;color:#8E8E93;">{{ $exam->subject->name }}</div>
            </div>
            <div style="background:#FDEDEC;border:1px solid #F1948A;padding:4px 10px;border-radius:8px;text-align:center;margin-left:12px;flex-shrink:0;">
                <div style="font-size:10px;color:#C0392B;font-weight:700;text-transform:uppercase;">Token</div>
                <div style="font-size:14px;font-weight:800;color:#C0392B;font-family:monospace;letter-spacing:1px;">{{ $exam->token }}</div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
            <div style="background:#F2F2F7;border-radius:8px;padding:8px;text-align:center;">
                <div style="font-size:18px;font-weight:700;color:#FF9500;">{{ $ongoingCount }}</div>
                <div style="font-size:10px;color:#8E8E93;">Mengerjakan</div>
            </div>
            <div style="background:#F2F2F7;border-radius:8px;padding:8px;text-align:center;">
                <div style="font-size:18px;font-weight:700;color:#34C759;">{{ $finishedCount }}</div>
                <div style="font-size:10px;color:#8E8E93;">Selesai</div>
            </div>
        </div>
        <a href="{{ route('teacher.monitor', $exam->id) }}" style="display:block;background:#F2F2F7;color:#1C1C1E;border:0.5px solid #E5E5EA;border-radius:8px;padding:10px;font-size:14px;font-weight:600;text-align:center;text-decoration:none;">
            👁️ Monitor
        </a>
    </div>
    @endforeach
</div>
@endif
 
</div>