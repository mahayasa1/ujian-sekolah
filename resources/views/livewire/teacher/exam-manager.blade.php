{{-- resources/views/livewire/teacher/exam-manager.blade.php --}}
<div>

{{-- Toolbar --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:0.75rem;">
    <span style="font-size:0.875rem;color:#6B7280;">{{ $this->exams->count() }} ujian terdaftar</span>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">
        + Buat Ujian Baru
    </button>
</div>

{{-- ============================================================
     MODAL BUAT/EDIT UJIAN
     ============================================================ --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:1rem;width:100%;max-width:600px;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.2);">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <span>🎯</span>
                <h3 style="margin:0;font-size:0.95rem;font-weight:700;color:#1F2937;">{{ $editId ? 'Edit Ujian' : 'Buat Ujian Baru' }}</h3>
            </div>
            <button wire:click="$set('showForm', false)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:1.25rem;">✕</button>
        </div>

        <div style="padding:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

            {{-- Judul (full width) --}}
            <div style="grid-column:1/-1;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    Judul Ujian <span style="color:#C0392B;">*</span>
                </label>
                <input type="text" wire:model="title" placeholder="mis. Ujian Tengah Semester Ganjil 2025"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('title') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Link Google Form (full width) --}}
            <div style="grid-column:1/-1;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    🔗 Link Google Form <span style="color:#9CA3AF;font-weight:400;">(embed soal dari Google Form)</span>
                </label>
                <input type="url" wire:model="googleFormUrl"
                    placeholder="https://docs.google.com/forms/d/e/xxxxx/viewform?embedded=true"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('googleFormUrl') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
                <p style="font-size:0.72rem;color:#9CA3AF;margin-top:0.25rem;">
                    💡 Di Google Form → Kirim → Tab Embed → salin URL dari atribut <code>src</code>.
                    Pastikan URL diakhiri <code>?embedded=true</code>
                </p>
            </div>

            {{-- Kelas --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Kelas</label>
                <select wire:model="classRoomId"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
                    <option value="0">— Semua Kelas —</option>
                    @foreach($classRooms as $cls)
                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Durasi --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Durasi (menit)</label>
                <input type="number" wire:model="duration" min="5" max="300"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('duration') <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Mulai pada --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    Mulai Pada <span style="color:#9CA3AF;font-weight:400;">(opsional)</span>
                </label>
                <input type="datetime-local" wire:model="startAt"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>

            {{-- Berakhir pada --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    Berakhir Pada <span style="color:#9CA3AF;font-weight:400;">(opsional)</span>
                </label>
                <input type="datetime-local" wire:model="endAt"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>

        </div>

        {{-- Footer --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding:1rem 1.5rem;border-top:1px solid #F3F4F6;">
            <button wire:click="$set('showForm', false)" class="btn-digi-outline">Batal</button>
            <button wire:click="save" class="btn-digi-primary" wire:loading.attr="disabled">
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
<div style="background:white;border:1px solid #E5E7EB;border-radius:0.75rem;padding:3rem;text-align:center;color:#9CA3AF;">
    <div style="font-size:2.5rem;margin-bottom:0.75rem;">📭</div>
    <p style="margin:0 0 1rem;font-weight:500;">Belum ada ujian dibuat untuk mata pelajaran ini</p>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">+ Buat Ujian Pertama</button>
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
    <div class="digi-card" style="border-left:4px solid {{ $statusColor }};">

        {{-- Header kartu --}}
        <div style="display:flex;align-items:start;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;margin-bottom:0.875rem;">
            <div style="flex:1;min-width:0;">
                <h3 style="margin:0 0 0.25rem;font-size:0.95rem;font-weight:700;color:#1F2937;">{{ $exam->title }}</h3>
                <div style="font-size:0.78rem;color:#9CA3AF;display:flex;flex-wrap:wrap;gap:0.5rem;">
                    <span>⏱ {{ $exam->duration }} menit</span>
                    @if($exam->classRoom)<span>🏫 {{ $exam->classRoom->name }}</span>@endif
                    @if($exam->start_at)<span>📅 {{ $exam->start_at->format('d M Y H:i') }}</span>@endif
                    @if($exam->google_form_url)
                    <span style="background:#EBF5FB;color:#1A5276;padding:1px 7px;border-radius:999px;font-size:0.7rem;font-weight:700;">📋 Google Form</span>
                    @endif
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                {{-- Token badge --}}
                <div style="background:#FDEDEC;border:1px solid #F1948A;padding:0.3rem 0.75rem;border-radius:0.5rem;text-align:center;">
                    <div style="font-size:0.6rem;color:#C0392B;font-weight:700;text-transform:uppercase;">Token</div>
                    <div style="font-size:1rem;font-weight:800;color:#C0392B;letter-spacing:0.1em;font-family:monospace;">{{ $exam->token }}</div>
                </div>
                {{-- Status badge --}}
                @if($exam->status === 'aktif')
                    <span class="badge-aktif">● Aktif</span>
                @elseif($exam->status === 'selesai')
                    <span class="badge-selesai">✓ Selesai</span>
                @else
                    <span class="badge-draft">Draft</span>
                @endif
            </div>
        </div>

        {{-- Google Form URL preview --}}
        @if($exam->google_form_url)
        <div style="background:#F0F9FF;border:0.5px solid #BAE6FD;border-radius:0.5rem;padding:0.6rem 0.875rem;margin-bottom:0.875rem;font-size:0.78rem;color:#0C4A6E;display:flex;align-items:center;gap:0.5rem;">
            <span>🔗</span>
            <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $exam->google_form_url }}</span>
            <a href="{{ $exam->google_form_url }}" target="_blank"
               style="color:#0C4A6E;font-weight:600;text-decoration:none;flex-shrink:0;">Buka ↗</a>
        </div>
        @endif

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;margin-bottom:0.875rem;padding:0.75rem;background:#F9FAFB;border-radius:0.5rem;">
            <div style="text-align:center;">
                <div style="font-size:1.1rem;font-weight:700;color:#E67E22;">{{ $ongoingCount }}</div>
                <div style="font-size:0.65rem;color:#9CA3AF;">Mengerjakan</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.1rem;font-weight:700;color:#27AE60;">{{ $finishedCount }}</div>
                <div style="font-size:0.65rem;color:#9CA3AF;">Selesai</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.1rem;font-weight:700;color:#C0392B;">{{ $avgScore ? round($avgScore) : '—' }}</div>
                <div style="font-size:0.65rem;color:#9CA3AF;">Rata-rata</div>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <button wire:click="toggleStatus({{ $exam->id }})"
                class="{{ $exam->status === 'aktif' ? 'btn-digi-outline' : 'btn-digi-success' }}"
                style="font-size:0.8rem;">
                {{ $exam->status === 'aktif' ? '⏸ Nonaktifkan' : '▶ Aktifkan' }}
            </button>
            <button wire:click="edit({{ $exam->id }})"
                class="btn-digi-outline" style="font-size:0.8rem;">
                ✏️ Edit
            </button>
            <a href="{{ route('teacher.monitor', $exam->id) }}" wire:navigate
                class="btn-digi-outline" style="font-size:0.8rem;display:inline-flex;align-items:center;gap:0.35rem;text-decoration:none;">
                👁️ Monitor
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif

</div>