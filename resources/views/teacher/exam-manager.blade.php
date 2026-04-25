{{-- resources/views/livewire/teacher/exam-manager.blade.php --}}
<div>

{{-- Toolbar --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:0.75rem;">
    <span style="font-size:0.875rem;color:#6B7280;">
        {{ $this->exams->count() }} ujian terdaftar
    </span>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Buat Ujian Baru
    </button>
</div>

{{-- Create/Edit Exam Modal --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:100;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:1rem;width:100%;max-width:680px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.2);">

        {{-- Modal header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <span>🎯</span>
                <h3 style="margin:0;font-size:0.95rem;font-weight:700;color:#1F2937;">
                    {{ $editId ? 'Edit Ujian' : 'Buat Ujian Baru' }}
                </h3>
            </div>
            <button wire:click="$set('showForm', false)" style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:1.25rem;">✕</button>
        </div>

        <div style="padding:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

            {{-- Title (full width) --}}
            <div style="grid-column:1/-1;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Judul Ujian <span style="color:#C0392B;">*</span></label>
                <input type="text" wire:model="title" placeholder="mis. Ujian Tengah Semester Ganjil 2024"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>

            {{-- Class --}}
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

            {{-- Duration --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Durasi (menit)</label>
                <input type="number" wire:model="duration" min="10" max="300"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>

            {{-- Start at --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Mulai Pada <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="datetime-local" wire:model="startAt"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>

            {{-- End at --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">Berakhir Pada <span style="color:#9CA3AF;font-weight:400;">(opsional)</span></label>
                <input type="datetime-local" wire:model="endAt"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:0.5rem;font-size:0.875rem;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>

            {{-- Random --}}
            <div style="grid-column:1/-1;display:flex;align-items:center;gap:0.5rem;">
                <input type="checkbox" wire:model="randomQuestion" id="rq" style="accent-color:#C0392B;width:16px;height:16px;">
                <label for="rq" style="font-size:0.875rem;color:#374151;cursor:pointer;font-weight:500;">
                    🔀 Acak urutan soal untuk setiap siswa
                </label>
            </div>

            {{-- Question selection (full width) --}}
            <div style="grid-column:1/-1;">
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    Pilih Soal <span style="color:#C0392B;">*</span>
                    <span style="font-weight:400;color:#9CA3AF;">({{ count($selectedQuestions) }} dipilih)</span>
                </label>

                <div style="border:1.5px solid #E5E7EB;border-radius:0.5rem;max-height:240px;overflow-y:auto;">
                    @if($questions->isEmpty())
                    <div style="padding:1.5rem;text-align:center;color:#9CA3AF;font-size:0.85rem;">
                        Belum ada soal di bank soal. Tambahkan soal terlebih dahulu.
                    </div>
                    @else
                    @foreach($questions as $q)
                    <label style="display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1rem;border-bottom:1px solid #F9FAFB;cursor:pointer;transition:background 0.1s;"
                        onmouseover="this.style.background='#FDEDEC'" onmouseout="this.style.background='white'">
                        <input type="checkbox" value="{{ $q->id }}" wire:model="selectedQuestions"
                            style="accent-color:#C0392B;width:15px;height:15px;flex-shrink:0;">
                        <div style="flex:1;min-width:0;">
                            <span style="font-size:0.82rem;color:#1F2937;line-height:1.4;">{{ Str::limit($q->question, 90) }}</span>
                        </div>
                        <div style="display:flex;gap:0.35rem;flex-shrink:0;">
                            <span style="background:{{ $q->type==='pg' ? '#EBF5FB' : '#F9EBEA' }};color:{{ $q->type==='pg' ? '#1A5276' : '#922B21' }};padding:0.15rem 0.45rem;border-radius:999px;font-size:0.68rem;font-weight:700;">
                                {{ strtoupper($q->type) }}
                            </span>
                        </div>
                    </label>
                    @endforeach
                    @endif
                </div>

                {{-- Select all / Clear --}}
                <div style="display:flex;gap:0.5rem;margin-top:0.4rem;">
                    <button type="button" wire:click="$set('selectedQuestions', {{ json_encode($questions->pluck('id')->toArray()) }})"
                        style="font-size:0.75rem;color:#C0392B;background:none;border:none;cursor:pointer;padding:0;font-weight:600;text-decoration:underline;">
                        Pilih Semua
                    </button>
                    <span style="color:#D1D5DB;">·</span>
                    <button type="button" wire:click="$set('selectedQuestions', [])"
                        style="font-size:0.75rem;color:#6B7280;background:none;border:none;cursor:pointer;padding:0;text-decoration:underline;">
                        Hapus Pilihan
                    </button>
                </div>
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

{{-- Exams List --}}
@if($this->exams->isEmpty())
<div style="background:white;border:1px solid #E5E7EB;border-radius:0.75rem;padding:3rem;text-align:center;color:#9CA3AF;">
    <div style="font-size:2.5rem;margin-bottom:0.75rem;">📭</div>
    <p style="margin:0 0 1rem;font-weight:500;">Belum ada ujian dibuat untuk mata pelajaran ini</p>
    <button class="btn-digi-primary" wire:click="$set('showForm', true)">
        + Buat Ujian Pertama
    </button>
</div>
@else
<div style="display:flex;flex-direction:column;gap:0.875rem;">
    @foreach($this->exams as $exam)
    @php
        $ongoingCount  = $exam->sessions->where('status','aktif')->count();
        $finishedCount = $exam->sessions->where('status','selesai')->count();
        $avgScore      = $exam->sessions->where('status','selesai')->avg('score');
    @endphp
    <div class="digi-card" style="border-left:4px solid {{ $exam->status==='aktif' ? '#27AE60' : ($exam->status==='selesai' ? '#7D3C98' : '#E5E7EB') }};">
        <div style="display:flex;align-items:start;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;margin-bottom:1rem;">
            <div>
                <h3 style="margin:0 0 0.25rem;font-size:0.95rem;font-weight:700;color:#1F2937;">{{ $exam->title }}</h3>
                <div style="font-size:0.78rem;color:#9CA3AF;">
                    📅 {{ $exam->start_at?->format('d M Y H:i') ?? 'Kapan saja' }}
                    &nbsp;·&nbsp;
                    ⏱ {{ $exam->duration }} menit
                    &nbsp;·&nbsp;
                    📝 {{ $exam->questions->count() }} soal
                    @if($exam->classRoom)
                    &nbsp;·&nbsp; 🏫 {{ $exam->classRoom->name }}
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

        {{-- Stats row --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.5rem;margin-bottom:1rem;padding:0.75rem;background:#F9FAFB;border-radius:0.5rem;">
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
            <div style="text-align:center;">
                <div style="font-size:1.1rem;font-weight:700;color:#7D3C98;">{{ $exam->random_question ? 'Ya' : 'Tidak' }}</div>
                <div style="font-size:0.65rem;color:#9CA3AF;">Acak Soal</div>
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            {{-- Toggle status --}}
            <button wire:click="toggleStatus({{ $exam->id }})"
                class="{{ $exam->status === 'aktif' ? 'btn-digi-outline' : 'btn-digi-success' }}"
                style="font-size:0.8rem;">
                {{ $exam->status === 'aktif' ? '⏸ Nonaktifkan' : '▶ Aktifkan' }}
            </button>

            {{-- Monitor --}}
            <a href="{{ route('teacher.monitor', $exam->id) }}" wire:navigate
                class="btn-digi-outline" style="font-size:0.8rem;display:inline-flex;align-items:center;gap:0.35rem;">
                👁️ Monitor
            </a>

            {{-- Results --}}
            <a href="{{ route('teacher.exam-results', $exam->id) }}" wire:navigate
                class="btn-digi-outline" style="font-size:0.8rem;display:inline-flex;align-items:center;gap:0.35rem;">
                📊 Nilai
            </a>

            {{-- Edit --}}
            <button
                style="padding:0.45rem 0.875rem;background:#F3F4F6;color:#6B7280;border:none;border-radius:0.5rem;cursor:pointer;font-size:0.8rem;font-weight:600;">
                ✏️ Edit
            </button>
        </div>
    </div>
    @endforeach
</div>
@endif

</div>
