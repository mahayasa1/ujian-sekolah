{{-- resources/views/livewire/admin/subjects.blade.php --}}
<div>

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
    <div>
        <h1 style="margin:0 0 4px;font-size:16px;font-weight:700;color:#1F2937;">📚 Manajemen Mata Pelajaran</h1>
        <p style="margin:0;font-size:12px;color:#6B7280;">Kelola mata pelajaran dan penugasan guru</p>
    </div>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">+ Tambah Mapel</button>
</div>

<div style="position:relative;margin-bottom:14px;">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari mata pelajaran atau kode..."
        style="width:100%;padding:9px 12px 9px 32px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;outline:none;font-family:inherit;box-sizing:border-box;"
        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
</div>

{{-- Modal --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #F3F4F6;">
            <h3 style="margin:0;font-size:14px;font-weight:700;color:#1F2937;">{{ $editId ? '✏️ Edit Mata Pelajaran' : '➕ Tambah Mata Pelajaran' }}</h3>
            <button wire:click="$set('showForm', false)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:18px;line-height:1;">✕</button>
        </div>
        <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Nama Mata Pelajaran <span style="color:#C0392B;">*</span></label>
                <input type="text" wire:model="name" placeholder="mis. Matematika, IPA, B. Indonesia"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('name') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Kode Mapel <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="text" wire:model="code" placeholder="mis. MTK-01, IPA-01"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('code') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Guru Pengampu</label>
                <select wire:model="teacherId"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;background:white;font-family:inherit;outline:none;">
                    <option value="0">— Belum ditugaskan —</option>
                    @foreach($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->user->name }}</option>
                    @endforeach
                </select>
            </div>
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

{{-- Table --}}
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">
    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table style="width:100%;border-collapse:collapse;min-width:520px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Mata Pelajaran</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:90px;">Kode</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Guru Pengampu</th>
                    <th style="text-align:center;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:60px;">Soal</th>
                    <th style="text-align:center;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:60px;">Ujian</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->subjects as $subject)
                @php
                    $icons = ['Matematika'=>'➕','IPA'=>'🔬','IPS'=>'🌍','B. Indonesia'=>'📝','B. Inggris'=>'🇬🇧','PPKn'=>'🏛️','Agama'=>'🕌','Seni'=>'🎨','Olahraga'=>'⚽'];
                    $icon  = $icons[$subject->name] ?? '📘';
                @endphp
                <tr style="border-bottom:0.5px solid #F3F4F6;">
                    <td style="padding:10px 14px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-size:18px;line-height:1;">{{ $icon }}</span>
                            <span style="font-weight:600;font-size:13px;color:#1F2937;">{{ $subject->name }}</span>
                        </div>
                    </td>
                    <td style="padding:10px 14px;">
                        @if($subject->code)
                        <code style="background:#F3F4F6;color:#374151;padding:2px 6px;border-radius:4px;font-size:11px;">{{ $subject->code }}</code>
                        @else
                        <span style="color:#D1D5DB;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;">
                        @if($subject->teacher)
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="width:22px;height:22px;border-radius:50%;background:#EBF5FB;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#1A5276;flex-shrink:0;">
                                {{ substr($subject->teacher->user->name, 0, 1) }}
                            </div>
                            <span style="font-size:12px;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $subject->teacher->user->name }}</span>
                        </div>
                        @else
                        <span style="font-size:12px;color:#D1D5DB;font-style:italic;">Belum ditugaskan</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;text-align:center;font-weight:600;font-size:13px;color:#374151;">{{ $subject->questions()->count() }}</td>
                    <td style="padding:10px 14px;text-align:center;font-weight:600;font-size:13px;color:#374151;">{{ $subject->exams()->count() }}</td>
                    <td style="padding:10px 14px;">
                        <div style="display:flex;gap:6px;">
                            <button wire:click="edit({{ $subject->id }})"
                                style="padding:4px 10px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;">
                                Edit
                            </button>
                            <button wire:click="delete({{ $subject->id }})"
                                wire:confirm="Yakin hapus mata pelajaran ini? Semua soal dan ujian akan ikut terhapus!"
                                style="padding:4px 10px;background:#FDEDEC;color:#C0392B;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;color:#9CA3AF;padding:40px 16px;font-size:13px;">
                        <div style="font-size:32px;margin-bottom:8px;">📭</div>
                        Belum ada mata pelajaran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($this->subjects->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #F3F4F6;">
        {{ $this->subjects->links() }}
    </div>
    @endif
</div>

</div>