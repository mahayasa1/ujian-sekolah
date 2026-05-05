{{-- resources/views/livewire/teacher/exam-manager.blade.php --}}
<div>

{{-- Toolbar --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:0.75rem;">
    <span style="font-size:0.8rem;color:#6B7280;">{{ $this->exams->count() }} ujian</span>
    <button class="btn-digi-primary" wire:click="create" style="font-size:13px;padding:8px 14px;">
        + Buat Ujian Baru
    </button>
</div>

{{-- ============================================================
     MODAL BUAT/EDIT UJIAN - Bottom sheet on mobile
     ============================================================ --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:flex-end;justify-content:center;">
    <div style="background:white;border-radius:16px 16px 0 0;width:100%;max-width:600px;max-height:92vh;overflow-y:auto;box-shadow:0 -4px 30px rgba(0,0,0,0.2);">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.25rem 0.75rem;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <span>🎯</span>
                <h3 style="margin:0;font-size:0.9rem;font-weight:700;color:#1F2937;">{{ $editId ? 'Edit Ujian' : 'Buat Ujian Baru' }}</h3>
            </div>
            <button wire:click="$set('showForm', false)" style="background:#F3F4F6;border:none;color:#374151;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        <div style="padding:1.25rem;display:flex;flex-direction:column;gap:0.875rem;">

            {{-- Judul --}}
            <div>
                <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                    Judul Ujian <span style="color:#C0392B;">*</span>
                </label>
                <input type="text" wire:model="title" placeholder="mis. Ujian Tengah Semester Ganjil 2025"
                    style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('title') <p style="color:#C0392B;font-size:0.7rem;margin-top:0.2rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Link Google Form --}}
            <div>
                <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                    🔗 Link Google Form <span style="color:#9CA3AF;font-weight:400;">(opsional)</span>
                </label>
                <input type="url" wire:model="googleFormUrl"
                    placeholder="https://docs.google.com/forms/d/e/xxxxx/viewform?embedded=true"
                    style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('googleFormUrl') <p style="color:#C0392B;font-size:0.7rem;margin-top:0.2rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Kelas & Durasi --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.875rem;">
                <div>
                    <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">Kelas</label>
                    <select wire:model="classRoomId"
                        style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
                        <option value="0">— Semua —</option>
                        @foreach($classRooms as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">Durasi (menit)</label>
                    <input type="number" wire:model="duration" min="5" max="300"
                        style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;"
                        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                    @error('duration') <p style="color:#C0392B;font-size:0.7rem;margin-top:0.2rem;">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Mulai & Berakhir --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.875rem;">
                <div>
                    <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                        Mulai <span style="color:#9CA3AF;font-weight:400;">(opsional)</span>
                    </label>
                    <input type="datetime-local" wire:model="startAt"
                        style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;"
                        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                </div>
                <div>
                    <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                        Berakhir <span style="color:#9CA3AF;font-weight:400;">(opsional)</span>
                    </label>
                    <input type="datetime-local" wire:model="endAt"
                        style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;"
                        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding:1rem 1.25rem;border-top:1px solid #F3F4F6;position:sticky;bottom:0;background:white;">
            <button wire:click="$set('showForm', false)" class="btn-digi-outline" style="font-size:13px;padding:8px 14px;">Batal</button>
            <button wire:click="save" class="btn-digi-primary" style="font-size:13px;padding:8px 14px;" wire:loading.attr="disabled">
                <span wire:loading.remove>💾 Simpan Ujian</span>
                <span wire:loading>⏳ Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ============================================================
     DAFTAR UJIAN
     ============================================================ --}}
@if($this->exams->isEmpty())
<div style="background:white;border:1px solid #E5E7EB;border-radius:12px;padding:2.5rem 1rem;text-align:center;color:#9CA3AF;">
    <div style="font-size:2.5rem;margin-bottom:0.75rem;">📭</div>
    <p style="margin:0 0 0.75rem;font-weight:500;color:#6B7280;">Belum ada ujian dibuat</p>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)" style="font-size:13px;">+ Buat Ujian Pertama</button>
</div>
@else
<div style="display:flex;flex-direction:column;gap:0.875rem;">
    @foreach($this->exams as $exam)
    @php
        $ongoingCount  = $exam->sessions->where('status','aktif')->count();
        $finishedCount = $exam->sessions->where('status','selesai')->count();
        $avgScore      = $exam->sessions->where('status','selesai')->avg('score');
        $statusColor   = $exam->status === 'aktif' ? '#27AE60' : ($exam->status === 'selesai' ? '#7D3C98' : '#9CA3AF');
    @endphp
    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border-left:4px solid {{ $statusColor }};padding:14px;">

        {{-- Header kartu --}}
        <div style="display:flex;align-items:start;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;margin-bottom:0.875rem;">
            <div style="flex:1;min-width:0;">
                <h3 style="margin:0 0 0.25rem;font-size:0.875rem;font-weight:700;color:#1F2937;">{{ $exam->title }}</h3>
                <div style="font-size:0.72rem;color:#9CA3AF;display:flex;flex-wrap:wrap;gap:0.4rem;">
                    <span>⏱ {{ $exam->duration }} mnt</span>
                    @if($exam->classRoom)<span>🏫 {{ $exam->classRoom->name }}</span>@endif
                    @if($exam->start_at)<span>📅 {{ $exam->start_at->format('d M H:i') }}</span>@endif
                    @if($exam->google_form_url)
                    <span style="background:#EBF5FB;color:#1A5276;padding:1px 6px;border-radius:999px;font-size:0.65rem;font-weight:700;">📋 Form</span>
                    @endif
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:0.4rem;flex-wrap:wrap;flex-shrink:0;">
                <div style="background:#FDEDEC;border:1px solid #F1948A;padding:4px 10px;border-radius:8px;text-align:center;">
                    <div style="font-size:9px;color:#C0392B;font-weight:700;text-transform:uppercase;">Token</div>
                    <div style="font-size:14px;font-weight:800;color:#C0392B;letter-spacing:0.1em;font-family:monospace;">{{ $exam->token }}</div>
                </div>
                @if($exam->status === 'aktif')
                    <span class="badge-aktif">● Aktif</span>
                @elseif($exam->status === 'selesai')
                    <span class="badge-selesai">✓ Selesai</span>
                @else
                    <span class="badge-draft">Draft</span>
                @endif
            </div>
        </div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;margin-bottom:0.875rem;padding:0.6rem;background:#F9FAFB;border-radius:0.5rem;">
            <div style="text-align:center;">
                <div style="font-size:1rem;font-weight:700;color:#E67E22;">{{ $ongoingCount }}</div>
                <div style="font-size:0.6rem;color:#9CA3AF;">Aktif</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1rem;font-weight:700;color:#27AE60;">{{ $finishedCount }}</div>
                <div style="font-size:0.6rem;color:#9CA3AF;">Selesai</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1rem;font-weight:700;color:#C0392B;">{{ $avgScore ? round($avgScore) : '—' }}</div>
                <div style="font-size:0.6rem;color:#9CA3AF;">Rata-rata</div>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
            <button wire:click="toggleStatus({{ $exam->id }})"
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:{{ $exam->status === 'aktif' ? '#F3F4F6' : '#D5F5E3' }};color:{{ $exam->status === 'aktif' ? '#374151' : '#1E8449' }};border:none;border-radius:6px;cursor:pointer;font-size:0.72rem;font-weight:600;font-family:inherit;">
                {{ $exam->status === 'aktif' ? '⏸ Nonaktif' : '▶ Aktifkan' }}
            </button>
            <button wire:click="edit({{ $exam->id }})"
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#F3F4F6;color:#374151;border:none;border-radius:6px;cursor:pointer;font-size:0.72rem;font-weight:600;font-family:inherit;">
                ✏️ Edit
            </button>
            <a href="{{ route('teacher.monitor', $exam->id) }}" wire:navigate
                style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;cursor:pointer;font-size:0.72rem;font-weight:600;text-decoration:none;">
                👁️ Monitor
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif

</div>