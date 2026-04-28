{{-- resources/views/livewire/admin/users.blade.php --}}
<div>

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
    <div>
        <h1 style="margin:0 0 4px;font-size:16px;font-weight:700;color:#1F2937;">👥 Manajemen Pengguna</h1>
        <p style="margin:0;font-size:12px;color:#6B7280;">Kelola akun admin, guru, dan siswa</p>
    </div>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">+ Tambah Pengguna</button>
</div>

{{-- Filter & Search --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
    <div style="position:relative;flex:1;min-width:200px;">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari nama atau email..."
            style="width:100%;padding:9px 12px 9px 32px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;outline:none;font-family:inherit;box-sizing:border-box;"
            onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
    </div>
    <select wire:model.live="roleFilter"
        style="padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;background:white;font-family:inherit;outline:none;">
        <option value="">Semua Role</option>
        <option value="admin">Admin</option>
        <option value="guru">Guru</option>
        <option value="siswa">Siswa</option>
    </select>
</div>

{{-- Modal Form --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span>{{ $editId ? '✏️' : '➕' }}</span>
                <h3 style="margin:0;font-size:14px;font-weight:700;color:#1F2937;">
                    {{ $editId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                </h3>
            </div>
            <button wire:click="$set('showForm', false)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:18px;line-height:1;">✕</button>
        </div>

        <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:14px;">

            <div style="grid-column:1/-1;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Nama Lengkap <span style="color:#C0392B;">*</span></label>
                <input type="text" wire:model="name" placeholder="Nama lengkap pengguna"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('name') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div style="grid-column:1/-1;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Email <span style="color:#C0392B;">*</span></label>
                <input type="email" wire:model="email" placeholder="email@sekolah.sch.id"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('email') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Password {{ $editId ? '(kosongkan jika tidak diubah)' : '*' }}</label>
                <input type="password" wire:model="password" placeholder="••••••••"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('password') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Role <span style="color:#C0392B;">*</span></label>
                <select wire:model.live="role"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;background:white;font-family:inherit;outline:none;">
                    <option value="siswa">Siswa</option>
                    <option value="guru">Guru</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            @if($role === 'guru')
            <div style="grid-column:1/-1;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">NIP <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="text" wire:model="nip" placeholder="Nomor Induk Pegawai"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>
            @endif

            @if($role === 'siswa')
            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">NIS <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="text" wire:model="nis" placeholder="Nomor Induk Siswa"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">NISN <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="text" wire:model="nisn" placeholder="Nomor Induk Siswa Nasional"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>
            <div style="grid-column:1/-1;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Kelas</label>
                <select wire:model="classRoomId"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;background:white;font-family:inherit;outline:none;">
                    <option value="0">— Pilih Kelas —</option>
                    @foreach($classRooms as $cls)
                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;padding:14px 20px;border-top:1px solid #F3F4F6;">
            <button wire:click="$set('showForm', false)" class="btn-digi-outline">Batal</button>
            <button wire:click="save" class="btn-digi-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>💾 Simpan</span>
                <span wire:loading>⏳ Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- Users Table --}}
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">
    <div style="padding:12px 16px;border-bottom:1px solid #F3F4F6;">
        <span style="font-size:13px;font-weight:600;color:#374151;">{{ $this->users->total() }} pengguna ditemukan</span>
    </div>

    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table style="width:100%;border-collapse:collapse;min-width:600px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Pengguna</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:70px;">Role</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:160px;">Info Tambahan</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:80px;">Bergabung</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->users as $user)
                <tr style="border-bottom:0.5px solid #F3F4F6;">
                    <td style="padding:10px 14px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-weight:700;color:#C0392B;font-size:12px;flex-shrink:0;">
                                {{ $user->initials() }}
                            </div>
                            <div style="min-width:0;">
                                <div style="font-weight:600;font-size:13px;color:#1F2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $user->name }}</div>
                                <div style="font-size:11px;color:#9CA3AF;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:10px 14px;">
                        @if($user->role === 'admin')
                            <span style="background:#FDEDEC;color:#C0392B;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap;">Admin</span>
                        @elseif($user->role === 'guru')
                            <span style="background:#EBF5FB;color:#1A5276;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap;">Guru</span>
                        @else
                            <span style="background:#D5F5E3;color:#1E8449;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap;">Siswa</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;font-size:12px;color:#6B7280;line-height:1.6;">
                        @if($user->role === 'guru' && $user->teacher)
                            <div>NIP: {{ $user->teacher->nip ?? '-' }}</div>
                        @elseif($user->role === 'siswa' && $user->student)
                            <div>NIS: {{ $user->student->nis ?? '-' }}</div>
                            <div>NISN: {{ $user->student->nisn ?? '-' }}</div>
                            <div><span style="font-weight:600;color:#374151;">{{ $user->student->classRoom?->name ?? '-' }}</span></div>
                        @else
                            —
                        @endif
                    </td>
                    <td style="padding:10px 14px;font-size:12px;color:#9CA3AF;white-space:nowrap;">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td style="padding:10px 14px;">
                        <div style="display:flex;gap:6px;">
                            <button wire:click="edit({{ $user->id }})"
                                style="padding:4px 10px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;">
                                Edit
                            </button>
                            @if($user->id !== auth()->id())
                            <button wire:click="delete({{ $user->id }})"
                                wire:confirm="Yakin hapus pengguna '{{ $user->name }}'?"
                                style="padding:4px 10px;background:#FDEDEC;color:#C0392B;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;">
                                Hapus
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#9CA3AF;padding:40px 16px;font-size:13px;">
                        <div style="font-size:32px;margin-bottom:8px;">📭</div>
                        Tidak ada pengguna ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->users->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #F3F4F6;">
        {{ $this->users->links() }}
    </div>
    @endif
</div>

</div>