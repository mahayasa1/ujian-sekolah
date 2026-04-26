{{-- resources/views/livewire/admin/users.blade.php --}}
<div>

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
    <div>
        <h1 style="margin:0 0 0.25rem;font-size:1.15rem;font-weight:700;color:#1F2937;">👥 Manajemen Pengguna</h1>
        <p style="margin:0;font-size:0.85rem;color:#6B7280;">Kelola akun admin, guru, dan siswa</p>
    </div>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">
        + Tambah Pengguna
    </button>
</div>

{{-- Filter & Search --}}
<div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.25rem;">
    <div style="position:relative;flex:1;min-width:200px;">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari nama atau email..."
            style="width:100%;padding:0.6rem 0.75rem 0.6rem 2.25rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;outline:none;font-family:inherit;"
            onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
    </div>
    <select wire:model.live="roleFilter"
        style="padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
        <option value="">Semua Role</option>
        <option value="admin">Admin</option>
        <option value="guru">Guru</option>
        <option value="siswa">Siswa</option>
    </select>
</div>

{{-- Modal Form --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:1rem;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <span>{{ $editId ? '✏️' : '➕' }}</span>
                <h3 style="margin:0;font-size:0.95rem;font-weight:700;color:#1F2937;">
                    {{ $editId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
                </h3>
            </div>
            <button wire:click="$set('showForm', false)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:1.25rem;">✕</button>
        </div>

        <div style="padding:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

            <div style="grid-column:1/-1;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Nama Lengkap <span style="color:#C0392B;">*</span></label>
                <input type="text" wire:model="name" placeholder="Nama lengkap pengguna"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('name') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div style="grid-column:1/-1;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Email <span style="color:#C0392B;">*</span></label>
                <input type="email" wire:model="email" placeholder="email@sekolah.sch.id"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('email') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Password {{ $editId ? '(kosongkan jika tidak diubah)' : '*' }}</label>
                <input type="password" wire:model="password" placeholder="••••••••"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('password') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Role <span style="color:#C0392B;">*</span></label>
                <select wire:model.live="role"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
                    <option value="siswa">Siswa</option>
                    <option value="guru">Guru</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            {{-- Guru: NIP --}}
            @if($role === 'guru')
            <div style="grid-column:1/-1;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">NIP <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="text" wire:model="nip" placeholder="Nomor Induk Pegawai"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>
            @endif

            {{-- Siswa: NIS & Kelas --}}
            @if($role === 'siswa')
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">NIS <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="text" wire:model="nis" placeholder="Nomor Induk Siswa"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Kelas</label>
                <select wire:model="classRoomId"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
                    <option value="0">— Pilih Kelas —</option>
                    @foreach($classRooms as $cls)
                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding:1rem 1.5rem;border-top:1px solid #F3F4F6;">
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
<div class="digi-card" style="padding:0;overflow:hidden;">
    <div style="padding:0.875rem 1.25rem;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:0.875rem;font-weight:600;color:#374151;">
            {{ $this->users->total() }} pengguna ditemukan
        </span>
    </div>

    <table class="digi-table">
        <thead>
            <tr>
                <th>Pengguna</th>
                <th style="width:80px;">Role</th>
                <th style="width:120px;">Info Tambahan</th>
                <th style="width:90px;">Bergabung</th>
                <th style="width:100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($this->users as $user)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:0.625rem;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-weight:700;color:#C0392B;font-size:0.8rem;flex-shrink:0;">
                            {{ $user->initials() }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:0.875rem;color:#1F2937;">{{ $user->name }}</div>
                            <div style="font-size:0.75rem;color:#9CA3AF;">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @if($user->role === 'admin')
                        <span style="background:#FDEDEC;color:#C0392B;padding:0.2rem 0.6rem;border-radius:999px;font-size:0.72rem;font-weight:700;">Admin</span>
                    @elseif($user->role === 'guru')
                        <span style="background:#EBF5FB;color:#1A5276;padding:0.2rem 0.6rem;border-radius:999px;font-size:0.72rem;font-weight:700;">Guru</span>
                    @else
                        <span style="background:#D5F5E3;color:#1E8449;padding:0.2rem 0.6rem;border-radius:999px;font-size:0.72rem;font-weight:700;">Siswa</span>
                    @endif
                </td>
                <td style="font-size:0.78rem;color:#6B7280;">
                    @if($user->role === 'guru' && $user->teacher)
                        NIP: {{ $user->teacher->nip ?? '-' }}
                    @elseif($user->role === 'siswa' && $user->student)
                        NIS: {{ $user->student->nis ?? '-' }}<br>
                        <span style="font-weight:600;color:#374151;">{{ $user->student->classRoom?->name ?? '-' }}</span>
                    @else
                        —
                    @endif
                </td>
                <td style="font-size:0.78rem;color:#9CA3AF;">{{ $user->created_at->format('d/m/Y') }}</td>
                <td>
                    <div style="display:flex;gap:0.4rem;">
                        <button wire:click="edit({{ $user->id }})"
                            style="padding:0.3rem 0.6rem;background:#EBF5FB;color:#1A5276;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;">
                            Edit
                        </button>
                        @if($user->id !== auth()->id())
                        <button wire:click="delete({{ $user->id }})"
                            wire:confirm="Yakin hapus pengguna '{{ $user->name }}'?"
                            style="padding:0.3rem 0.6rem;background:#FDEDEC;color:#C0392B;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;">
                            Hapus
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#9CA3AF;padding:3rem;font-size:0.9rem;">
                    <div style="font-size:2.5rem;margin-bottom:0.5rem;">📭</div>
                    Tidak ada pengguna ditemukan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($this->users->hasPages())
    <div style="padding:0.875rem 1.25rem;border-top:1px solid #F3F4F6;">
        {{ $this->users->links() }}
    </div>
    @endif
</div>

</div>
