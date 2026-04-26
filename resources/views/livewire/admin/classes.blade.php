{{-- resources/views/livewire/admin/classes.blade.php --}}
<div>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
    <div>
        <h1 style="margin:0 0 0.25rem;font-size:1.15rem;font-weight:700;color:#1F2937;">🏫 Manajemen Kelas</h1>
        <p style="margin:0;font-size:0.85rem;color:#6B7280;">Kelola kelas dan pengelompokan siswa</p>
    </div>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">+ Tambah Kelas</button>
</div>

{{-- Modal --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:1rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #F3F4F6;">
            <h3 style="margin:0;font-size:0.95rem;font-weight:700;color:#1F2937;">{{ $editId ? '✏️ Edit Kelas' : '➕ Tambah Kelas Baru' }}</h3>
            <button wire:click="$set('showForm', false)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:1.25rem;">✕</button>
        </div>
        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Nama Kelas <span style="color:#C0392B;">*</span></label>
                <input type="text" wire:model="name" placeholder="mis. VII A, VIII B, IX C"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('name') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Tingkat <span style="color:#C0392B;">*</span></label>
                <select wire:model="grade"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
                    <option value="">— Pilih Tingkat —</option>
                    <option value="7">Kelas 7 (SMP)</option>
                    <option value="8">Kelas 8 (SMP)</option>
                    <option value="9">Kelas 9 (SMP)</option>
                </select>
                @error('grade') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>
        </div>
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding:1rem 1.5rem;border-top:1px solid #F3F4F6;">
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
<div style="background:white;border:1px solid #E5E7EB;border-radius:0.75rem;padding:3rem;text-align:center;color:#9CA3AF;">
    <div style="font-size:3rem;margin-bottom:0.75rem;">🏫</div>
    <p style="margin:0 0 1rem;font-weight:500;">Belum ada kelas terdaftar</p>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">+ Tambah Kelas Pertama</button>
</div>
@else

@foreach($classesByGrade as $grade => $classes)
<div style="margin-bottom:1.5rem;">
    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.75rem;">
        <div style="width:32px;height:32px;border-radius:0.5rem;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:0.85rem;font-weight:700;color:#C0392B;">
            {{ $grade }}
        </div>
        <h2 style="margin:0;font-size:0.9rem;font-weight:700;color:#374151;">Tingkat {{ $grade }}</h2>
        <span style="background:#F3F4F6;color:#9CA3AF;padding:0.15rem 0.5rem;border-radius:999px;font-size:0.72rem;font-weight:600;">{{ $classes->count() }} kelas</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0.875rem;">
        @foreach($classes as $class)
        <div class="digi-card" style="border-left:4px solid #C0392B;padding:1rem;">
            <div style="display:flex;align-items:start;justify-content:space-between;margin-bottom:0.75rem;">
                <div>
                    <h3 style="margin:0 0 0.15rem;font-size:1rem;font-weight:700;color:#1F2937;">{{ $class->name }}</h3>
                    <p style="margin:0;font-size:0.75rem;color:#9CA3AF;">Kelas {{ $class->grade }} (SMP)</p>
                </div>
                <div style="width:40px;height:40px;border-radius:0.5rem;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:1.25rem;">🏫</div>
            </div>

            <div style="display:flex;align-items:center;gap:0.4rem;margin-bottom:1rem;padding:0.5rem 0.75rem;background:#F9FAFB;border-radius:0.5rem;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span style="font-size:0.8rem;font-weight:600;color:#374151;">{{ $class->students_count }} siswa</span>
            </div>

            <div style="display:flex;gap:0.4rem;">
                <button wire:click="edit({{ $class->id }})"
                    style="flex:1;padding:0.4rem 0.5rem;background:#EBF5FB;color:#1A5276;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;text-align:center;">
                    ✏️ Edit
                </button>
                <button wire:click="delete({{ $class->id }})"
                    wire:confirm="Yakin hapus kelas {{ $class->name }}? Siswa di kelas ini akan kehilangan pengelompokan."
                    style="flex:1;padding:0.4rem 0.5rem;background:#FDEDEC;color:#C0392B;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;text-align:center;">
                    🗑️ Hapus
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

{{-- Summary Table --}}
<div style="margin-top:1.5rem;">
    <h2 style="font-size:1rem;font-weight:700;color:#1F2937;margin:0 0 1rem;">📋 Ringkasan Semua Kelas</h2>
    <div class="digi-card" style="padding:0;overflow:hidden;">
        <table class="digi-table">
            <thead>
                <tr>
                    <th>Nama Kelas</th>
                    <th style="width:80px;">Tingkat</th>
                    <th style="width:100px;text-align:center;">Jumlah Siswa</th>
                    <th style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->classes as $class)
                <tr>
                    <td style="font-weight:600;font-size:0.875rem;">{{ $class->name }}</td>
                    <td>
                        <span style="background:#FDEDEC;color:#C0392B;padding:0.2rem 0.5rem;border-radius:999px;font-size:0.75rem;font-weight:700;">Kelas {{ $class->grade }}</span>
                    </td>
                    <td style="text-align:center;">
                        <strong style="font-size:1rem;color:#374151;">{{ $class->students_count }}</strong>
                        <span style="font-size:0.72rem;color:#9CA3AF;"> siswa</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:0.4rem;">
                            <button wire:click="edit({{ $class->id }})"
                                style="padding:0.3rem 0.6rem;background:#EBF5FB;color:#1A5276;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;">
                                Edit
                            </button>
                            <button wire:click="delete({{ $class->id }})"
                                wire:confirm="Yakin hapus kelas {{ $class->name }}?"
                                style="padding:0.3rem 0.6rem;background:#FDEDEC;color:#C0392B;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;">
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

@endif

</div>
