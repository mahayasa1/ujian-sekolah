{{-- resources/views/livewire/admin/classes.blade.php --}}
<div>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
    <div>
        <h1 style="margin:0 0 4px;font-size:16px;font-weight:700;color:#1F2937;">🏫 Manajemen Kelas</h1>
        <p style="margin:0;font-size:12px;color:#6B7280;">Kelola kelas dan pengelompokan siswa</p>
    </div>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">+ Tambah Kelas</button>
</div>

{{-- Modal --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #F3F4F6;">
            <h3 style="margin:0;font-size:14px;font-weight:700;color:#1F2937;">{{ $editId ? '✏️ Edit Kelas' : '➕ Tambah Kelas Baru' }}</h3>
            <button wire:click="$set('showForm', false)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:18px;line-height:1;">✕</button>
        </div>
        <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Nama Kelas <span style="color:#C0392B;">*</span></label>
                <input type="text" wire:model="name" placeholder="mis. VII A, VIII B, IX C"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('name') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Tingkat <span style="color:#C0392B;">*</span></label>
                <select wire:model="grade"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;background:white;font-family:inherit;outline:none;">
                    <option value="">— Pilih Tingkat —</option>
                    <option value="7">Kelas 7 (SMP)</option>
                    <option value="8">Kelas 8 (SMP)</option>
                    <option value="9">Kelas 9 (SMP)</option>
                </select>
                @error('grade') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;padding:14px 20px;border-top:1px solid #F3F4F6;">
            <button wire:click="$set('showForm', false)" class="btn-digi-outline">Batal</button>
            <button wire:click="save" class="btn-digi-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>💾 Simpan Kelas</span>
                <span wire:loading>⏳ Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- Classes Grid by Grade --}}
@php $classesByGrade = $this->classes->groupBy('grade')->sortKeys(); @endphp

@if($this->classes->isEmpty())
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);padding:40px 16px;text-align:center;color:#9CA3AF;">
    <div style="font-size:36px;margin-bottom:10px;">🏫</div>
    <p style="margin:0 0 14px;font-weight:500;font-size:14px;color:#6B7280;">Belum ada kelas terdaftar</p>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">+ Tambah Kelas Pertama</button>
</div>
@else

@foreach($classesByGrade as $grade => $classes)
<div style="margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
        <div style="width:30px;height:30px;border-radius:8px;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#C0392B;">{{ $grade }}</div>
        <h2 style="margin:0;font-size:14px;font-weight:700;color:#374151;">Tingkat {{ $grade }}</h2>
        <span style="background:#F3F4F6;color:#9CA3AF;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">{{ $classes->count() }} kelas</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
        @foreach($classes as $class)
        <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border-left:4px solid #C0392B;padding:14px;">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:10px;">
                <div>
                    <h3 style="margin:0 0 2px;font-size:15px;font-weight:700;color:#1F2937;">{{ $class->name }}</h3>
                    <p style="margin:0;font-size:11px;color:#9CA3AF;">Kelas {{ $class->grade }} (SMP)</p>
                </div>
                <div style="width:36px;height:36px;border-radius:8px;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:18px;">🏫</div>
            </div>

            <div style="display:flex;align-items:center;gap:6px;margin-bottom:12px;padding:6px 10px;background:#F9FAFB;border-radius:8px;">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span style="font-size:12px;font-weight:600;color:#374151;">{{ $class->students_count }} siswa</span>
            </div>

            <div style="display:flex;gap:6px;">
                <button wire:click="edit({{ $class->id }})"
                    style="flex:1;padding:5px 8px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;text-align:center;">
                    ✏️ Edit
                </button>
                <button wire:click="delete({{ $class->id }})"
                    wire:confirm="Yakin hapus kelas {{ $class->name }}?"
                    style="flex:1;padding:5px 8px;background:#FDEDEC;color:#C0392B;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;text-align:center;">
                    🗑️ Hapus
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

{{-- Summary Table --}}
<div style="margin-top:8px;">
    <h2 style="font-size:14px;font-weight:700;color:#1F2937;margin:0 0 12px;">📋 Ringkasan Semua Kelas</h2>
    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">
        <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
            <table style="width:100%;border-collapse:collapse;min-width:380px;">
                <thead>
                    <tr>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Nama Kelas</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:80px;">Tingkat</th>
                        <th style="text-align:center;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:100px;">Jumlah Siswa</th>
                        <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->classes as $class)
                    <tr style="border-bottom:0.5px solid #F3F4F6;">
                        <td style="padding:10px 14px;font-weight:600;font-size:13px;color:#1F2937;">{{ $class->name }}</td>
                        <td style="padding:10px 14px;">
                            <span style="background:#FDEDEC;color:#C0392B;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700;">Kelas {{ $class->grade }}</span>
                        </td>
                        <td style="padding:10px 14px;text-align:center;">
                            <strong style="font-size:14px;color:#374151;">{{ $class->students_count }}</strong>
                            <span style="font-size:11px;color:#9CA3AF;"> siswa</span>
                        </td>
                        <td style="padding:10px 14px;">
                            <div style="display:flex;gap:6px;">
                                <button wire:click="edit({{ $class->id }})"
                                    style="padding:4px 10px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $class->id }})"
                                    wire:confirm="Yakin hapus kelas {{ $class->name }}?"
                                    style="padding:4px 10px;background:#FDEDEC;color:#C0392B;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endif

</div>