{{-- resources/views/livewire/student/results-list.blade.php --}}
<div>
<div style="margin-bottom:14px;">
    <h2 style="font-size:15px;font-weight:700;color:#1F2937;margin:0 0 4px;">📊 Semua Hasil Ujian</h2>
    <p style="margin:0;font-size:12px;color:#6B7280;">Riwayat seluruh ujian yang telah kamu selesaikan</p>
</div>

@if($sessions->isEmpty())
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);padding:40px 16px;text-align:center;color:#9CA3AF;">
    <div style="font-size:36px;margin-bottom:10px;">📭</div>
    <p style="margin:0 0 14px;font-weight:500;font-size:14px;color:#6B7280;">Kamu belum mengikuti ujian apapun</p>
    <a href="{{ route('student.dashboard') }}" wire:navigate class="btn-digi-primary" style="display:inline-flex;">
        → Lihat Ujian Tersedia
    </a>
</div>
@else
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table style="width:100%;border-collapse:collapse;min-width:480px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Mata Pelajaran</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Judul Ujian</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:90px;">Tanggal</th>
                    <th style="text-align:center;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:60px;">Nilai</th>
                    <th style="text-align:center;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:70px;">Predikat</th>
                    <th style="text-align:center;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:70px;">Status</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:60px;">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $ses)
                @php
                    $score  = $ses->score ?? 0;
                    $grade  = $score >= 90 ? 'A' : ($score >= 80 ? 'B' : ($score >= 75 ? 'C' : ($score >= 60 ? 'D' : 'E')));
                    $passed = $score >= 75;
                    $gc     = $passed ? '#27AE60' : '#C0392B';
                @endphp
                <tr style="border-bottom:0.5px solid #F3F4F6;">
                    <td style="padding:10px 14px;font-weight:600;font-size:13px;color:#1F2937;white-space:nowrap;">{{ $ses->exam->subject->name }}</td>
                    <td style="padding:10px 14px;font-size:12px;color:#374151;">{{ Str::limit($ses->exam->title, 40) }}</td>
                    <td style="padding:10px 14px;font-size:12px;color:#6B7280;white-space:nowrap;">{{ $ses->submitted_at?->format('d/m/Y') }}</td>
                    <td style="padding:10px 14px;text-align:center;">
                        <strong style="font-size:15px;color:{{ $gc }};">{{ $score }}</strong>
                    </td>
                    <td style="padding:10px 14px;text-align:center;">
                        <span style="background:{{ $passed ? '#D5F5E3' : '#FDEDEC' }};color:{{ $gc }};padding:3px 8px;border-radius:999px;font-size:12px;font-weight:700;">{{ $grade }}</span>
                    </td>
                    <td style="padding:10px 14px;text-align:center;">
                        @if($passed)
                            <span style="background:#D5F5E3;color:#1E8449;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:600;white-space:nowrap;">Lulus</span>
                        @else
                            <span style="background:#FDEDEC;color:#C0392B;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:600;white-space:nowrap;">Remidi</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;">
                        <a href="{{ route('student.result', $ses->id) }}" wire:navigate
                           style="color:#C0392B;font-size:12px;font-weight:600;text-decoration:none;white-space:nowrap;">
                            Lihat →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($sessions->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #F3F4F6;">
        {{ $sessions->links() }}
    </div>
    @endif
</div>
@endif
</div>