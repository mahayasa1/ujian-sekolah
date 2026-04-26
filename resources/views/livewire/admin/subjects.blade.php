{{-- resources/views/livewire/admin/subjects.blade.php --}}
<div>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
    <div>
        <h1 style="margin:0 0 0.25rem;font-size:1.15rem;font-weight:700;color:#1F2937;">📚 Manajemen Mata Pelajaran</h1>
        <p style="margin:0;font-size:0.85rem;color:#6B7280;">Kelola mata pelajaran dan penugasan guru</p>
    </div>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">+ Tambah Mapel</button>
</div>

<div style="position:relative;margin-bottom:1.25rem;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari mata pelajaran atau kode..."
        style="width:100%;padding:0.6rem 0.75rem 0.6rem 2.25rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;outline:none;font-family:inherit;"
        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
</div>

{{-- Modal --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:1rem;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #F3F4F6;">
            <h3 style="margin:0;font-size:0.95rem;font-weight:700;color:#1F2937;">{{ $editId ? '✏️ Edit Mata Pelajaran' : '➕ Tambah Mata Pelajaran' }}</h3>
            <button wire:click="$set('showForm', false)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:1.25rem;">✕</button>
        </div>
        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Nama Mata Pelajaran <span style="color:#C0392B;">*</span></label>
                <input type="text" wire:model="name" placeholder="mis. Matematika, IPA, B. Indonesia"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('name') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Kode Mapel <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="text" wire:model="code" placeholder="mis. MTK-01, IPA-01"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('code') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Guru Pengampu</label>
                <select wire:model="teacherId"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
                    <option value="0">— Belum ditugaskan —</option>
                    @foreach($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                    @endforeach
                </select>
            </div>
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

{{-- Table --}}
<div class="digi-card" style="padding:0;overflow:hidden;">
    <table class="digi-table">
        <thead>
            <tr>
                <th>Mata Pelajaran</th>
                <th style="width:100px;">Kode</th>
                <th>Guru Pengampu</th>
                <th style="width:80px;">Soal</th>
                <th style="width:80px;">Ujian</th>
                <th style="width:100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($this->subjects as $subject)
            @php
                $icons = ['Matematika'=>'➕','IPA'=>'🔬','IPS'=>'🌍','B. Indonesia'=>'📝','B. Inggris'=>'🇬🇧','PPKn'=>'🏛️','Agama'=>'🕌','Seni'=>'🎨','Olahraga'=>'⚽'];
                $icon  = $icons[$subject->name] ?? '📘';
            @endphp
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:0.5rem;">
                        <span style="font-size:1.1rem;">{{ $icon }}</span>
                        <div>
                            <div style="font-weight:600;font-size:0.875rem;color:#1F2937;">{{ $subject->name }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    @if($subject->code)
                    <code style="background:#F3F4F6;color:#374151;padding:0.15rem 0.4rem;border-radius:0.3rem;font-size:0.78rem;">{{ $subject->code }}</code>
                    @else
                    <span style="color:#D1D5DB;">—</span>
                    @endif
                </td>
                <td>
                    @if($subject->teacher)
                    <div style="display:flex;align-items:center;gap:0.4rem;">
                        <div style="width:24px;height:24px;border-radius:50%;background:#EBF5FB;display:flex;align-items:center;justify-content:center;font-size:0.65rem;font-weight:700;color:#1A5276;">
                            {{ substr($subject->teacher->user->name, 0, 1) }}
                        </div>
                        <span style="font-size:0.82rem;color:#374151;">{{ $subject->teacher->user->name }}</span>
                    </div>
                    @else
                    <span style="font-size:0.8rem;color:#D1D5DB;font-style:italic;">Belum ditugaskan</span>
                    @endif
                </td>
                <td style="text-align:center;font-weight:600;color:#374151;">{{ $subject->questions()->count() }}</td>
                <td style="text-align:center;font-weight:600;color:#374151;">{{ $subject->exams()->count() }}</td>
                <td>
                    <div style="display:flex;gap:0.4rem;">
                        <button wire:click="edit({{ $subject->id }})"
                            style="padding:0.3rem 0.6rem;background:#EBF5FB;color:#1A5276;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;">
                            Edit
                        </button>
                        <button wire:click="delete({{ $subject->id }})"
                            wire:confirm="Yakin hapus mata pelajaran ini? Semua soal dan ujian akan ikut terhapus!"
                            style="padding:0.3rem 0.6rem;background:#FDEDEC;color:#C0392B;border:none;border-radius:0.4rem;cursor:pointer;font-size:0.75rem;font-weight:600;">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;color:#9CA3AF;padding:3rem;font-size:0.9rem;">
                    <div style="font-size:2.5rem;margin-bottom:0.5rem;">📭</div>
                    Belum ada mata pelajaran
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($this->subjects->hasPages())
    <div style="padding:0.875rem 1.25rem;border-top:1px solid #F3F4F6;">
        {{ $this->subjects->links() }}
    </div>
    @endif
</div>

</div>
