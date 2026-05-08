{{-- resources/views/livewire/admin/teachers.blade.php --}}
<div>

{{-- ============================================================
     HEADER
     ============================================================ --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
    <div>
        <h1 style="margin:0 0 2px;font-size:16px;font-weight:700;color:#1F2937;">👨‍🏫 Manajemen Guru</h1>
        <p style="margin:0;font-size:12px;color:#6B7280;">Kelola data seluruh guru</p>
    </div>
    <button wire:click="$set('showForm', true)"
        style="display:inline-flex;align-items:center;gap:6px;background:#C0392B;color:white;border:none;border-radius:8px;padding:9px 14px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
        ➕ Tambah Guru
    </button>
</div>

{{-- Flash --}}
@if(session('success'))
<div style="background:#D4EDDA;border:0.5px solid #C3E6CB;color:#155724;padding:12px 16px;border-radius:10px;font-size:14px;margin-bottom:12px;font-weight:500;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Search --}}
<div style="position:relative;margin-bottom:14px;">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari nama, email, NIP..."
        style="width:100%;padding:9px 12px 9px 32px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;outline:none;font-family:inherit;box-sizing:border-box;"
        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
</div>

{{-- ============================================================
     MODAL FORM ADD / EDIT
     ============================================================ --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,0.25);">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #F3F4F6;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:18px;">{{ $editId ? '✏️' : '➕' }}</span>
                <h3 style="margin:0;font-size:14px;font-weight:700;color:#1F2937;">
                    {{ $editId ? 'Edit Data Guru' : 'Tambah Guru Baru' }}
                </h3>
            </div>
            <button wire:click="resetForm" style="background:#F3F4F6;border:none;color:#374151;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">

            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Nama Lengkap <span style="color:#C0392B;">*</span></label>
                <input type="text" wire:model="name" placeholder="Nama lengkap guru"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('name') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Email <span style="color:#C0392B;">*</span></label>
                <input type="email" wire:model="email" placeholder="email@sekolah.sch.id"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('email') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                    Password 
                    @if($editId)
                        <span style="color:#9CA3AF;font-weight:400;">(kosongkan jika tidak diubah)</span>
                    @else
                        <span style="color:#C0392B;">*</span>
                    @endif
                </label>
                <input type="password" wire:model="password" placeholder="••••••••"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('password') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">NIP <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="text" wire:model="nip" placeholder="Nomor Induk Pegawai"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>

        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;padding:14px 20px;border-top:1px solid #F3F4F6;">
            <button wire:click="resetForm"
                style="padding:9px 16px;background:white;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;font-family:inherit;">
                Batal
            </button>
            <button wire:click="save" wire:loading.attr="disabled"
                style="padding:9px 16px;background:#C0392B;border:none;border-radius:8px;font-size:13px;font-weight:700;color:white;cursor:pointer;font-family:inherit;">
                <span wire:loading.remove wire:target="save">💾 Simpan</span>
                <span wire:loading wire:target="save">⏳ Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ============================================================
     TEACHERS TABLE
     ============================================================ --}}
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">

    <div style="padding:12px 16px;border-bottom:1px solid #F3F4F6;">
        <span style="font-size:13px;font-weight:600;color:#374151;">Total: {{ $this->teachers->total() }} guru</span>
    </div>

    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table style="width:100%;border-collapse:collapse;min-width:560px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Guru</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:160px;">NIP</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Mata Pelajaran</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->teachers as $teacher)
                <tr style="border-bottom:0.5px solid #F3F4F6;">
                    <td style="padding:10px 14px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#EBF5FB;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#1A5276;flex-shrink:0;">
                                {{ strtoupper(substr($teacher->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:#1F2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $teacher->user->name }}</div>
                                <div style="font-size:11px;color:#9CA3AF;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $teacher->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:10px 14px;font-size:12px;color:#374151;">{{ $teacher->nip ?? '—' }}</td>
                    <td style="padding:10px 14px;">
                        @if($teacher->subjects->isNotEmpty())
                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                            @foreach($teacher->subjects as $subject)
                            <span style="background:#EBF5FB;color:#1A5276;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;">{{ $subject->name }}</span>
                            @endforeach
                        </div>
                        @else
                        <span style="color:#D1D5DB;font-size:12px;">Belum ditugaskan</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;">
                        <div style="display:flex;gap:6px;">
                            <button wire:click="edit({{ $teacher->user_id }})"
                                style="padding:4px 10px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;">
                                ✏️
                            </button>
                            @if($teacher->user_id !== auth()->id())
                            <button wire:click="delete({{ $teacher->user_id }})"
                                wire:confirm="Yakin hapus guru '{{ $teacher->user->name }}'?"
                                style="padding:4px 10px;background:#FDEDEC;color:#C0392B;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;">
                                🗑️
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:40px 16px;text-align:center;color:#9CA3AF;">
                        <div style="font-size:32px;margin-bottom:8px;">👨‍🏫</div>
                        <div style="font-size:14px;font-weight:500;">Belum ada data guru</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->teachers->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #F3F4F6;">
        {{ $this->teachers->links() }}
    </div>
    @endif
</div>

</div>