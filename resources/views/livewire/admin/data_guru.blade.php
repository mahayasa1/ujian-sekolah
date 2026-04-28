{{-- resources/views/livewire/admin/data_guru.blade.php --}}
<x-layouts.digitest :title="'Data Guru'">

@php
    $teachers = \App\Models\Teacher::with('user')->latest()->get();
@endphp

<div>

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
    <div>
        <div style="font-size:13px;font-weight:700;color:#8E8E93;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:2px;">DATA GURU</div>
        <div style="font-size:12px;color:#AEAEB2;">Total {{ $teachers->count() }} guru terdaftar</div>
    </div>
    <a href="{{ route('admin.users') }}?roleFilter=guru"
       style="display:inline-flex;align-items:center;gap:6px;background:#C0392B;color:white;border:none;border-radius:8px;padding:9px 14px;font-size:13px;font-weight:600;text-decoration:none;">
        ➕ Tambah Guru
    </a>
</div>

{{-- List Guru --}}
@if($teachers->isEmpty())
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);padding:60px 24px;text-align:center;color:#AEAEB2;">
    <div style="font-size:36px;margin-bottom:12px;">👨‍🏫</div>
    <div style="font-size:14px;font-weight:500;color:#8E8E93;margin-bottom:14px;">Belum ada data guru</div>
    <a href="{{ route('admin.users') }}" style="display:inline-flex;align-items:center;gap:6px;background:#C0392B;color:white;border:none;border-radius:8px;padding:9px 14px;font-size:13px;font-weight:600;text-decoration:none;">
        + Tambah Guru
    </a>
</div>
@else

<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table style="width:100%;border-collapse:collapse;min-width:480px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Nama Guru</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:160px;">NIP</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Email</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Mata Pelajaran</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $teacher)
                <tr style="border-bottom:0.5px solid #F3F4F6;">
                    <td style="padding:10px 14px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#C0392B;flex-shrink:0;">
                                {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:600;color:#1F2937;">{{ $teacher->user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:10px 14px;font-size:12px;color:#374151;">{{ $teacher->nip ?? '—' }}</td>
                    <td style="padding:10px 14px;font-size:12px;color:#6B7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;">{{ $teacher->user->email }}</td>
                    <td style="padding:10px 14px;">
                        @php $subjects = \App\Models\Subject::where('teacher_id', $teacher->id)->get(); @endphp
                        @if($subjects->isNotEmpty())
                            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                @foreach($subjects as $subject)
                                <span style="background:#EBF5FB;color:#1A5276;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">{{ $subject->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <span style="color:#D1D5DB;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;">
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.users') }}"
                               style="padding:4px 10px;background:#EBF5FB;color:#1A5276;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif

</div>

</x-layouts.digitest>