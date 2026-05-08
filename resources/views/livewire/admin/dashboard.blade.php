{{-- resources/views/livewire/admin/dashboard.blade.php --}}
<div>

{{-- Greeting --}}
<div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:16px;">
    <div style="font-size:12px;color:#8E8E93;font-weight:500;margin-bottom:4px;">Selamat Datang !</div>
    <div style="font-size:15px;font-weight:600;color:#1C1C1E;margin-bottom:2px;">{{ auth()->user()->name }}</div>
    <div style="font-size:13px;color:#8E8E93;">Administrator</div>
</div>

{{-- Label --}}
<div style="font-size:17px;font-weight:600;color:#1C1C1E;margin-bottom:12px;">Dashboard</div>

{{-- 2-column grid --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">

    {{-- Data Guru --}}
    <a href="{{ route('admin.teachers') }}"
       style="background:white;border-radius:12px;padding:24px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);text-align:center;text-decoration:none;color:inherit;display:block;border-bottom:3px solid #1A5276;">
        <div style="font-size:28px;margin-bottom:6px;">👨‍🏫</div>
        <div style="font-size:14px;font-weight:600;color:#1C1C1E;">Data Guru</div>
        <div style="font-size:11px;color:#8E8E93;margin-top:2px;">{{ $stats['total_guru'] }} guru</div>
    </a>

    {{-- Data Siswa --}}
    <a href="{{ route('admin.students') }}"
       style="background:white;border-radius:12px;padding:24px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);text-align:center;text-decoration:none;color:inherit;display:block;border-bottom:3px solid #C0392B;">
        <div style="font-size:28px;margin-bottom:6px;">🎓</div>
        <div style="font-size:14px;font-weight:600;color:#1C1C1E;">Data Siswa</div>
        <div style="font-size:11px;color:#8E8E93;margin-top:2px;">{{ $stats['total_siswa'] }} siswa</div>
    </a>
</div>

{{-- Full-width cards --}}
<a href="{{ route('admin.subjects') }}"
   style="background:white;border-radius:12px;padding:20px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);text-align:center;text-decoration:none;color:inherit;display:block;margin-bottom:10px;border-bottom:3px solid #27AE60;">
    <div style="font-size:28px;margin-bottom:6px;">📚</div>
    <div style="font-size:14px;font-weight:700;color:#1C1C1E;text-transform:uppercase;letter-spacing:0.3px;">DATA MAPEL</div>
    <div style="font-size:11px;color:#8E8E93;margin-top:2px;">{{ $stats['total_mapel'] }} mata pelajaran</div>
</a>

{{-- <a href="{{ route('admin.classes') }}"
   style="background:white;border-radius:12px;padding:20px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);text-align:center;text-decoration:none;color:inherit;display:block;margin-bottom:10px;border-bottom:3px solid #7D3C98;">
    <div style="font-size:28px;margin-bottom:6px;">🏫</div>
    <div style="font-size:14px;font-weight:700;color:#1C1C1E;text-transform:uppercase;letter-spacing:0.3px;">DATA KELAS</div>
    <div style="font-size:11px;color:#8E8E93;margin-top:2px;">{{ $stats['total_kelas'] }} kelas</div>
</a> --}}

</div>