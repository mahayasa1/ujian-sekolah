{{-- resources/views/livewire/admin/data-guru.blade.php --}}
{{-- Sesuai screenshot: title "DATA GURU" + kartu putih kosong besar --}}
<x-layouts.digitest :title="'Data Guru'">

<div style="font-size:13px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:14px;">DATA GURU</div>

<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;min-height:360px;">
    @php
        $teachers = \App\Models\Teacher::with('user')->get();
    @endphp

    @if($teachers->isEmpty())
    <div style="padding:60px 24px;text-align:center;color:#AEAEB2;">
        <div style="font-size:36px;margin-bottom:12px;">👨‍🏫</div>
        <div style="font-size:14px;font-weight:500;color:#8E8E93;">Belum ada data guru</div>
    </div>
    @else
    @foreach($teachers as $teacher)
    <div style="display:flex;align-items:center;padding:14px 16px;border-bottom:0.5px solid #E5E5EA;gap:12px;">
        <div style="width:40px;height:40px;border-radius:50%;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#C0392B;flex-shrink:0;">
            {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:600;color:#1C1C1E;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $teacher->user->name }}</div>
            <div style="font-size:12px;color:#8E8E93;">NIP: {{ $teacher->nip ?? '-' }}</div>
        </div>
    </div>
    @endforeach
    @endif
</div>

</x-layouts.digitest>

---

{{-- resources/views/livewire/admin/data-siswa.blade.php --}}
{{-- Sesuai screenshot: title "DATA SISWA" + kartu putih kosong besar --}}
{{-- [PISAHKAN FILE INI] --}}