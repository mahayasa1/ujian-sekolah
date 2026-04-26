{{-- resources/views/livewire/teacher/question-bank.blade.php --}}
<div>

{{-- ===== TOOLBAR ===== --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:0.75rem;">
    <span style="font-size:0.875rem;color:#6B7280;">
        {{ $this->questions->total() }} set soal terdaftar
    </span>
    <button
        wire:click="$set('showForm', true)"
        style="display:inline-flex;align-items:center;gap:6px;background:#C0392B;color:white;border:none;border-radius:8px;padding:9px 14px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;white-space:nowrap;">
        + Tambah Soal Google Form
    </button>
</div>

{{-- ===== FLASH ===== --}}
@if(session('success'))
<div style="background:#D4EDDA;border:0.5px solid #C3E6CB;color:#155724;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:12px;font-weight:500;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- ============================================================
     MODAL FORM TAMBAH / EDIT SOAL
     ============================================================ --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;display:flex;align-items:flex-end;justify-content:center;padding:0;">
    <div style="background:white;border-radius:16px 16px 0 0;width:100%;max-width:600px;max-height:92vh;overflow-y:auto;box-shadow:0 -4px 30px rgba(0,0,0,0.2);">

        {{-- Header modal --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.25rem 0.75rem;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <span style="font-size:1.1rem;">📝</span>
                <h3 style="margin:0;font-size:0.9rem;font-weight:700;color:#1F2937;">
                    {{ $editId ? 'Edit Set Soal' : 'Tambah Set Soal Baru' }}
                </h3>
            </div>
            <button wire:click="resetForm"
                style="background:#F3F4F6;border:none;color:#374151;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">✕</button>
        </div>

        {{-- Body --}}
        <div style="padding:1.25rem;display:flex;flex-direction:column;gap:0.875rem;">

            {{-- Judul soal --}}
            <div>
                <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                    Judul / Nama Set Soal <span style="color:#C0392B;">*</span>
                </label>
                <input type="text" wire:model="title"
                    placeholder="mis. Ulangan Harian Bab 3"
                    style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('title')
                    <p style="color:#C0392B;font-size:0.7rem;margin-top:0.2rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Link Google Form siswa --}}
            <div>
                <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                    🔗 Link Google Form (untuk siswa) <span style="color:#C0392B;">*</span>
                </label>
                <input type="url" wire:model="googleFormUrl"
                    placeholder="https://docs.google.com/forms/d/e/xxxxx/viewform"
                    style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                <p style="font-size:0.68rem;color:#9CA3AF;margin-top:0.25rem;line-height:1.5;">
                    💡 Buka Google Form → klik <strong>Kirim</strong> → tab 🔗 Link → salin URL-nya.
                </p>
                @error('googleFormUrl')
                    <p style="color:#C0392B;font-size:0.7rem;margin-top:0.2rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Link Edit Google Form --}}
            <div>
                <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                    ✏️ Link Edit Google Form <span style="color:#9CA3AF;font-weight:400;">(opsional)</span>
                </label>
                <input type="url" wire:model="googleFormEditUrl"
                    placeholder="https://docs.google.com/forms/d/xxxxx/edit"
                    style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('googleFormEditUrl')
                    <p style="color:#C0392B;font-size:0.7rem;margin-top:0.2rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Link Google Sheets --}}
            <div>
                <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                    📊 Link Spreadsheet Jawaban <span style="color:#9CA3AF;font-weight:400;">(opsional)</span>
                </label>
                <input type="url" wire:model="googleSheetUrl"
                    placeholder="https://docs.google.com/spreadsheets/d/xxxxx"
                    style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('googleSheetUrl')
                    <p style="color:#C0392B;font-size:0.7rem;margin-top:0.2rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Durasi & Status --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.875rem;">
                <div>
                    <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                        ⏱ Durasi (menit)
                    </label>
                    <input type="number" wire:model="duration" min="5" max="300"
                        style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;"
                        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                    @error('duration')
                        <p style="color:#C0392B;font-size:0.7rem;margin-top:0.2rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">Status</label>
                    <select wire:model="isActive"
                        style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
                        <option value="1">✅ Aktif</option>
                        <option value="0">⏸ Non-aktif</option>
                    </select>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label style="font-size:0.75rem;font-weight:700;color:#374151;display:block;margin-bottom:0.35rem;">
                    📋 Deskripsi <span style="color:#9CA3AF;font-weight:400;">(opsional)</span>
                </label>
                <textarea wire:model="description" rows="3"
                    placeholder="Petunjuk pengerjaan soal..."
                    style="width:100%;padding:0.6rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;resize:vertical;line-height:1.5;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'"></textarea>
                @error('description')
                    <p style="color:#C0392B;font-size:0.7rem;margin-top:0.2rem;">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Footer --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding:1rem 1.25rem;border-top:1px solid #F3F4F6;position:sticky;bottom:0;background:white;">
            <button wire:click="resetForm"
                style="padding:9px 16px;background:white;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;font-family:inherit;">
                Batal
            </button>
            <button wire:click="save" wire:loading.attr="disabled"
                style="padding:9px 16px;background:#C0392B;border:none;border-radius:8px;font-size:13px;font-weight:700;color:white;cursor:pointer;font-family:inherit;">
                <span wire:loading.remove wire:target="save">💾 Simpan Soal</span>
                <span wire:loading wire:target="save">⏳ Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ============================================================
     MODAL PREVIEW GOOGLE FORM
     ============================================================ --}}
@if($showPreview && $previewId)
@php $previewQ = \App\Models\Question::find($previewId); @endphp
@if($previewQ)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:200;display:flex;align-items:center;justify-content:center;padding:0.75rem;">
    <div style="background:white;border-radius:14px;width:100%;max-width:720px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.3);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #F3F4F6;flex-shrink:0;">
            <div>
                <div style="font-size:0.7rem;color:#C0392B;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Preview Soal</div>
                <div style="font-size:0.9rem;font-weight:700;color:#1F2937;">{{ $previewQ->title }}</div>
            </div>
            <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
                @if($previewQ->google_form_edit_url)
                <a href="{{ $previewQ->google_form_edit_url }}" target="_blank"
                    style="padding:5px 10px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">
                    ✏️ Edit
                </a>
                @endif
                @if($previewQ->google_sheet_url)
                <a href="{{ $previewQ->google_sheet_url }}" target="_blank"
                    style="padding:5px 10px;background:#D5F5E3;color:#1E8449;border:none;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">
                    📊 Jawaban
                </a>
                @endif
                <button wire:click="closePreview"
                    style="background:#F3F4F6;border:none;color:#374151;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;">✕</button>
            </div>
        </div>
        <div style="flex:1;overflow:hidden;">
            <iframe src="{{ $previewQ->embed_url }}" style="width:100%;height:100%;min-height:500px;border:none;" frameborder="0" allow="camera;microphone">
                Memuat Google Form...
            </iframe>
        </div>
    </div>
</div>
@endif
@endif

{{-- ============================================================
     LIST SOAL
     ============================================================ --}}
@if($this->questions->isEmpty())
<div style="background:white;border:1px solid #E5E7EB;border-radius:12px;padding:2.5rem 1rem;text-align:center;color:#9CA3AF;">
    <div style="font-size:2.5rem;margin-bottom:0.75rem;">📭</div>
    <p style="margin:0 0 0.75rem;font-weight:500;color:#6B7280;">Belum ada soal untuk mata pelajaran ini</p>
    <p style="margin:0 0 1rem;font-size:0.8rem;color:#9CA3AF;">Tambahkan soal menggunakan link Google Form</p>
    <button wire:click="$set('showForm', true)"
        style="display:inline-flex;align-items:center;gap:6px;background:#C0392B;color:white;border:none;border-radius:8px;padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
        + Tambah Soal Google Form
    </button>
</div>
@else

{{-- Panduan cepat --}}
<div style="background:#FFFBEB;border:0.5px solid #FCD34D;border-radius:10px;padding:10px 14px;margin-bottom:12px;font-size:11px;color:#78350F;line-height:1.7;">
    <strong>📌 Cara:</strong> Buat soal di Google Forms → Salin link → Klik + Tambah → Tempel dan simpan.
</div>

<div style="display:flex;flex-direction:column;gap:0.75rem;">
    @foreach($this->questions as $q)
    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border-left:4px solid {{ $q->is_active ? '#27AE60' : '#D1D5DB' }};overflow:hidden;">
        <div style="padding:1rem;">

            {{-- Judul & status --}}
            <div style="display:flex;align-items:start;justify-content:space-between;gap:0.75rem;margin-bottom:0.6rem;">
                <div style="flex:1;min-width:0;">
                    <h3 style="margin:0 0 0.15rem;font-size:0.875rem;font-weight:700;color:#1F2937;line-height:1.3;">{{ $q->title }}</h3>
                    @if($q->description)
                    <p style="margin:0;font-size:0.75rem;color:#6B7280;line-height:1.4;">{{ Str::limit($q->description, 80) }}</p>
                    @endif
                </div>
                <span style="background:{{ $q->is_active ? '#D5F5E3' : '#F3F4F6' }};color:{{ $q->is_active ? '#155724' : '#9CA3AF' }};padding:2px 8px;border-radius:999px;font-size:0.68rem;font-weight:700;white-space:nowrap;flex-shrink:0;">
                    {{ $q->is_active ? '● Aktif' : '○ Off' }}
                </span>
            </div>

            {{-- Info chips --}}
            <div style="display:flex;flex-wrap:wrap;gap:0.4rem;margin-bottom:0.75rem;">
                <span style="background:#F3F4F6;color:#374151;padding:3px 8px;border-radius:999px;font-size:0.68rem;font-weight:600;">⏱ {{ $q->duration }} mnt</span>
                <span style="background:#EBF5FB;color:#1A5276;padding:3px 8px;border-radius:999px;font-size:0.68rem;font-weight:600;">📋 Google Form</span>
                @if($q->google_sheet_url)
                <span style="background:#D5F5E3;color:#155724;padding:3px 8px;border-radius:999px;font-size:0.68rem;font-weight:600;">📊 Sheet</span>
                @endif
            </div>

            {{-- URL preview --}}
            <div style="background:#F9FAFB;border:0.5px solid #E5E7EB;border-radius:7px;padding:6px 10px;margin-bottom:0.75rem;display:flex;align-items:center;gap:6px;">
                <span style="font-size:0.75rem;">🔗</span>
                <span style="font-size:0.7rem;color:#6B7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">{{ $q->google_form_url }}</span>
                <a href="{{ $q->google_form_url }}" target="_blank"
                    style="font-size:0.7rem;color:#C0392B;font-weight:600;text-decoration:none;flex-shrink:0;">Buka ↗</a>
            </div>

            {{-- Action buttons - scrollable on mobile --}}
            <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                <button wire:click="openPreview({{ $q->id }})"
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#FDEDEC;color:#C0392B;border:none;border-radius:6px;cursor:pointer;font-size:0.72rem;font-weight:600;font-family:inherit;">
                    👁️ Preview
                </button>
                @if($q->google_form_edit_url)
                <a href="{{ $q->google_form_edit_url }}" target="_blank"
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;cursor:pointer;font-size:0.72rem;font-weight:600;text-decoration:none;">
                    ✏️ Edit
                </a>
                @endif
                @if($q->google_sheet_url)
                <a href="{{ $q->google_sheet_url }}" target="_blank"
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#D5F5E3;color:#155724;border:none;border-radius:6px;cursor:pointer;font-size:0.72rem;font-weight:600;text-decoration:none;">
                    📊 Jawaban
                </a>
                @endif
                <button wire:click="toggleActive({{ $q->id }})"
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#F3F4F6;color:#374151;border:none;border-radius:6px;cursor:pointer;font-size:0.72rem;font-weight:600;font-family:inherit;">
                    {{ $q->is_active ? '⏸' : '▶' }}
                </button>
                <button wire:click="edit({{ $q->id }})"
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#F3F4F6;color:#374151;border:none;border-radius:6px;cursor:pointer;font-size:0.72rem;font-weight:600;font-family:inherit;">
                    🖊 Edit
                </button>
                <button wire:click="delete({{ $q->id }})"
                    wire:confirm="Yakin hapus soal '{{ $q->title }}'?"
                    style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;background:#FDEDEC;color:#C0392B;border:none;border-radius:6px;cursor:pointer;font-size:0.72rem;font-weight:600;font-family:inherit;">
                    🗑️
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($this->questions->hasPages())
<div style="margin-top:1rem;">{{ $this->questions->links() }}</div>
@endif

@endif
</div>