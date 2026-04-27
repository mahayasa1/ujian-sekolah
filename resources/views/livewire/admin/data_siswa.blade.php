{{-- resources/views/livewire/admin/data_siswa.blade.php --}}
<x-layouts.digitest :title="'Data Siswa'">

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
    <div>
        <div style="font-size:13px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:0.5px;">DATA SISWA</div>
        <div style="font-size:12px;color:#AEAEB2;margin-top:2px;">Total {{ $students->total() }} siswa terdaftar</div>
    </div>
    <a href="{{ route('admin.users') }}" style="display:inline-flex;align-items:center;gap:6px;background:#C0392B;color:white;border:none;border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;text-decoration:none;">
        + Tambah Siswa
    </a>
</div>

{{-- Search --}}
<div style="position:relative;margin-bottom:14px;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
    <input
        type="text"
        id="search-siswa"
        placeholder="Cari nama, NIS, atau kelas..."
        oninput="filterSiswa(this.value)"
        style="width:100%;padding:10px 12px 10px 36px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:14px;outline:none;font-family:inherit;box-sizing:border-box;"
        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'"
    >
</div>

{{-- Table --}}
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table style="width:100%;border-collapse:collapse;min-width:480px;" id="tabel-siswa">
            <thead>
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Nama Siswa</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:100px;">NIS</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:90px;">Kelas</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr class="siswa-row" data-name="{{ strtolower($student->user->name) }}" data-nis="{{ strtolower($student->nis ?? '') }}" data-kelas="{{ strtolower($student->classRoom?->name ?? '') }}"
                    style="border-bottom:0.5px solid #F3F4F6;">
                    <td style="padding:10px 14px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#C0392B;flex-shrink:0;">
                                {{ strtoupper(substr($student->user->name, 0, 1)) }}
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:#1F2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $student->user->name }}</div>
                                <div style="font-size:11px;color:#9CA3AF;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $student->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:10px 14px;">
                        <span style="font-size:12px;color:#374151;font-weight:500;">{{ $student->nis ?? '—' }}</span>
                    </td>
                    <td style="padding:10px 14px;">
                        @if($student->classRoom)
                        <span style="background:#FDEDEC;color:#C0392B;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap;">
                            {{ $student->classRoom->name }}
                        </span>
                        @else
                        <span style="color:#D1D5DB;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;">
                        <a href="{{ route('admin.users') }}?edit={{ $student->user_id }}"
                           style="display:inline-flex;align-items:center;padding:4px 10px;background:#EBF5FB;color:#1A5276;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:40px 16px;text-align:center;color:#9CA3AF;">
                        <div style="font-size:32px;margin-bottom:8px;">🎓</div>
                        <div style="font-size:14px;font-weight:500;">Belum ada data siswa</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #F3F4F6;">
        {{ $students->links() }}
    </div>
    @endif
</div>

<script>
function filterSiswa(val) {
    var keyword = val.toLowerCase();
    document.querySelectorAll('.siswa-row').forEach(function(row) {
        var match = row.dataset.name.includes(keyword)
            || row.dataset.nis.includes(keyword)
            || row.dataset.kelas.includes(keyword);
        row.style.display = match ? '' : 'none';
    });
}
</script>

</x-layouts.digitest>