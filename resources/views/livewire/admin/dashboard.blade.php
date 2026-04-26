{{-- resources/views/livewire/admin/dashboard.blade.php --}}
<div>

{{-- Greeting --}}
<div style="background:white;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);margin-bottom:16px;">
    <div style="font-size:12px;color:#8E8E93;font-weight:500;margin-bottom:4px;">Selamat Datang !</div>
    <div style="font-size:15px;font-weight:600;color:#1C1C1E;margin-bottom:2px;">{{ auth()->user()->name }}</div>
    <div style="font-size:13px;color:#8E8E93;">Admin</div>
</div>
 
{{-- Label Dashboard --}}
<div style="font-size:17px;font-weight:600;color:#1C1C1E;margin-bottom:12px;">Dashboard</div>
 
{{-- Baris 2 kartu: Data Guru | Data Siswa --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
    <a href="{{ route('admin.data_guru') }}" style="background:white;border-radius:12px;padding:24px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);text-align:center;text-decoration:none;color:inherit;display:block;">
        <div style="font-size:14px;font-weight:600;color:#1C1C1E;">Data Guru</div>
    </a>
    <a href="{{ route('admin.users') }}" style="background:white;border-radius:12px;padding:24px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);text-align:center;text-decoration:none;color:inherit;display:block;">
        <div style="font-size:14px;font-weight:600;color:#1C1C1E;">Data Siswa</div>
    </a>
</div>
 
{{-- Kartu full: DATA MAPEL --}}
<a href="{{ route('admin.subjects') }}" style="background:white;border-radius:12px;padding:24px 16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);text-align:center;text-decoration:none;color:inherit;display:block;margin-bottom:10px;">
    <div style="font-size:14px;font-weight:700;color:#1C1C1E;text-transform:uppercase;letter-spacing:0.3px;">DATA MAPEL</div>
</a>

</div>
