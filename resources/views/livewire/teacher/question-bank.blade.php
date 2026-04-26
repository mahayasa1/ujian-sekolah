{{-- resources/views/livewire/teacher/question-bank.blade.php --}}
<div>

{{-- ===== TOOLBAR ===== --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:0.75rem;">
    <span style="font-size:0.875rem;color:#6B7280;">
        {{ $this->questions->total() }} set soal terdaftar
    </span>
    <button
        wire:click="$set('showForm', true)"
        style="display:inline-flex;align-items:center;gap:6px;background:#C0392B;color:white;border:none;border-radius:8px;padding:9px 16px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;">
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
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:14px;width:100%;max-width:560px;max-height:92vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.25);">

        {{-- Header modal --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <span style="font-size:1.25rem;">📝</span>
                <h3 style="margin:0;font-size:0.95rem;font-weight:700;color:#1F2937;">
                    {{ $editId ? 'Edit Set Soal' : 'Tambah Set Soal Baru' }}
                </h3>
            </div>
            <button wire:click="resetForm"
                style="background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:1.25rem;line-height:1;">✕</button>
        </div>

        {{-- Body --}}
        <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem;">

            {{-- Judul soal --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    Judul / Nama Set Soal <span style="color:#C0392B;">*</span>
                </label>
                <input type="text" wire:model="title"
                    placeholder="mis. Ulangan Harian Bab 3 — Sistem Tata Surya"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('title')
                    <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Link Google Form siswa --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    🔗 Link Google Form (untuk siswa) <span style="color:#C0392B;">*</span>
                </label>
                <input type="url" wire:model="googleFormUrl"
                    placeholder="https://docs.google.com/forms/d/e/xxxxx/viewform"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                <p style="font-size:0.7rem;color:#9CA3AF;margin-top:0.3rem;line-height:1.5;">
                    💡 Buka Google Form → klik <strong>Kirim</strong> → tab <strong>🔗 Link</strong> → salin URL-nya.
                    Tidak perlu <code>?embedded=true</code>, sistem akan menambahkan otomatis.
                </p>
                @error('googleFormUrl')
                    <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Link Edit Google Form (opsional) --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    ✏️ Link Edit Google Form
                    <span style="color:#9CA3AF;font-weight:400;">(opsional — untuk guru edit soal langsung)</span>
                </label>
                <input type="url" wire:model="googleFormEditUrl"
                    placeholder="https://docs.google.com/forms/d/xxxxx/edit"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                <p style="font-size:0.7rem;color:#9CA3AF;margin-top:0.3rem;">
                    💡 Di Google Form, klik ikon pensil / salin URL dari browser saat kamu membuka form dalam mode edit.
                </p>
                @error('googleFormEditUrl')
                    <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Link Google Sheets (opsional) --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    📊 Link Spreadsheet Jawaban
                    <span style="color:#9CA3AF;font-weight:400;">(opsional — untuk lihat hasil)</span>
                </label>
                <input type="url" wire:model="googleSheetUrl"
                    placeholder="https://docs.google.com/spreadsheets/d/xxxxx"
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                <p style="font-size:0.7rem;color:#9CA3AF;margin-top:0.3rem;">
                    💡 Di Google Form → Respons → ikon Google Sheets → salin URL spreadsheet.
                </p>
                @error('googleSheetUrl')
                    <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Durasi & Status --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                        ⏱ Durasi Default (menit)
                    </label>
                    <input type="number" wire:model="duration" min="5" max="300"
                        style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;"
                        onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                    @error('duration')
                        <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                        Status
                    </label>
                    <select wire:model="isActive"
                        style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;background:white;font-family:inherit;outline:none;">
                        <option value="1">✅ Aktif</option>
                        <option value="0">⏸ Non-aktif</option>
                    </select>
                </div>
            </div>

            {{-- Deskripsi / petunjuk --}}
            <div>
                <label style="font-size:0.78rem;font-weight:700;color:#374151;display:block;margin-bottom:0.4rem;">
                    📋 Deskripsi / Petunjuk Soal
                    <span style="color:#9CA3AF;font-weight:400;">(opsional)</span>
                </label>
                <textarea wire:model="description" rows="3"
                    placeholder="mis. Kerjakan soal berikut dengan teliti. Pilih jawaban yang paling tepat."
                    style="width:100%;padding:0.65rem 0.875rem;border:1.5px solid #E5E7EB;border-radius:8px;font-size:0.875rem;font-family:inherit;outline:none;resize:vertical;line-height:1.5;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'"></textarea>
                @error('description')
                    <p style="color:#C0392B;font-size:0.72rem;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Footer --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding:1rem 1.5rem;border-top:1px solid #F3F4F6;position:sticky;bottom:0;background:white;">
            <button wire:click="resetForm"
                style="padding:9px 18px;background:white;border:1.5px solid #E5E7EB;border-radius:8px;font-size:14px;font-weight:600;color:#374151;cursor:pointer;font-family:inherit;">
                Batal
            </button>
            <button wire:click="save" wire:loading.attr="disabled"
                style="padding:9px 18px;background:#C0392B;border:none;border-radius:8px;font-size:14px;font-weight:700;color:white;cursor:pointer;font-family:inherit;">
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
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:200;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:14px;width:100%;max-width:720px;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.3);overflow:hidden;">

        {{-- Header preview --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid #F3F4F6;flex-shrink:0;">
            <div>
                <div style="font-size:0.75rem;color:#C0392B;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Preview Soal</div>
                <div style="font-size:0.95rem;font-weight:700;color:#1F2937;">{{ $previewQ->title }}</div>
            </div>
            <div style="display:flex;align-items:center;gap:0.5rem;">
                @if($previewQ->google_form_edit_url)
                <a href="{{ $previewQ->google_form_edit_url }}" target="_blank"
                    style="padding:6px 12px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                    ✏️ Edit Form
                </a>
                @endif
                @if($previewQ->google_sheet_url)
                <a href="{{ $previewQ->google_sheet_url }}" target="_blank"
                    style="padding:6px 12px;background:#D5F5E3;color:#1E8449;border:none;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                    📊 Lihat Jawaban
                </a>
                @endif
                <button wire:click="closePreview"
                    style="background:#F3F4F6;border:none;color:#374151;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;">✕</button>
            </div>
        </div>

        {{-- Embed Google Form --}}
        <div style="flex:1;overflow:hidden;">
            <iframe
                src="{{ $previewQ->embed_url }}"
                style="width:100%;height:100%;min-height:500px;border:none;"
                frameborder="0"
                marginheight="0"
                marginwidth="0"
                allow="camera;microphone"
            >
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
<div style="background:white;border:1px solid #E5E7EB;border-radius:12px;padding:3rem;text-align:center;color:#9CA3AF;">
    <div style="font-size:3rem;margin-bottom:0.75rem;">📭</div>
    <p style="margin:0 0 1rem;font-weight:500;color:#6B7280;">Belum ada soal untuk mata pelajaran ini</p>
    <p style="margin:0 0 1.25rem;font-size:0.85rem;color:#9CA3AF;">
        Tambahkan soal menggunakan link Google Form yang sudah kamu buat
    </p>
    <button wire:click="$set('showForm', true)"
        style="display:inline-flex;align-items:center;gap:6px;background:#C0392B;color:white;border:none;border-radius:8px;padding:10px 18px;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;">
        + Tambah Soal Google Form
    </button>
</div>
@else

{{-- Panduan cepat --}}
<div style="background:#FFFBEB;border:0.5px solid #FCD34D;border-radius:10px;padding:12px 16px;margin-bottom:14px;font-size:12px;color:#78350F;line-height:1.7;">
    <strong>📌 Cara menggunakan Bank Soal Google Form:</strong><br>
    1. Buat soal di <a href="https://forms.google.com" target="_blank" style="color:#C0392B;font-weight:600;">Google Forms</a> →
    2. Salin link form →
    3. Klik <strong>+ Tambah Soal Google Form</strong> →
    4. Tempel link dan simpan →
    5. Gunakan soal ini saat membuat <strong>Ujian</strong> di tab Ujian.
</div>

<div style="display:flex;flex-direction:column;gap:0.875rem;">
    @foreach($this->questions as $q)
    <div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border-left:4px solid {{ $q->is_active ? '#27AE60' : '#D1D5DB' }};overflow:hidden;">

        <div style="padding:1rem 1.25rem;">

            {{-- Judul & status --}}
            <div style="display:flex;align-items:start;justify-content:space-between;gap:0.75rem;margin-bottom:0.75rem;">
                <div style="flex:1;min-width:0;">
                    <h3 style="margin:0 0 0.2rem;font-size:0.95rem;font-weight:700;color:#1F2937;line-height:1.3;">
                        {{ $q->title }}
                    </h3>
                    @if($q->description)
                    <p style="margin:0;font-size:0.8rem;color:#6B7280;line-height:1.5;">
                        {{ Str::limit($q->description, 120) }}
                    </p>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:0.4rem;flex-shrink:0;">
                    <span style="background:{{ $q->is_active ? '#D5F5E3' : '#F3F4F6' }};color:{{ $q->is_active ? '#155724' : '#9CA3AF' }};padding:0.2rem 0.625rem;border-radius:999px;font-size:0.72rem;font-weight:700;">
                        {{ $q->is_active ? '● Aktif' : '○ Non-aktif' }}
                    </span>
                </div>
            </div>

            {{-- Info chips --}}
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.875rem;">
                <span style="display:inline-flex;align-items:center;gap:4px;background:#F3F4F6;color:#374151;padding:4px 10px;border-radius:999px;font-size:0.72rem;font-weight:600;">
                    ⏱ {{ $q->duration }} menit
                </span>
                <span style="display:inline-flex;align-items:center;gap:4px;background:#EBF5FB;color:#1A5276;padding:4px 10px;border-radius:999px;font-size:0.72rem;font-weight:600;">
                    📋 Google Form
                </span>
                @if($q->google_sheet_url)
                <span style="display:inline-flex;align-items:center;gap:4px;background:#D5F5E3;color:#155724;padding:4px 10px;border-radius:999px;font-size:0.72rem;font-weight:600;">
                    📊 Ada Spreadsheet
                </span>
                @endif
                <span style="display:inline-flex;align-items:center;gap:4px;background:#F3F4F6;color:#9CA3AF;padding:4px 10px;border-radius:999px;font-size:0.72rem;font-weight:500;">
                    📅 {{ $q->created_at->format('d M Y') }}
                </span>
            </div>

            {{-- URL preview (terpotong) --}}
            <div style="background:#F9FAFB;border:0.5px solid #E5E7EB;border-radius:7px;padding:8px 12px;margin-bottom:0.875rem;display:flex;align-items:center;gap:8px;">
                <span style="font-size:0.8rem;">🔗</span>
                <span style="font-size:0.75rem;color:#6B7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;">
                    {{ $q->google_form_url }}
                </span>
                <a href="{{ $q->google_form_url }}" target="_blank"
                    style="font-size:0.72rem;color:#C0392B;font-weight:600;text-decoration:none;flex-shrink:0;">
                    Buka ↗
                </a>
            </div>

            {{-- Action buttons --}}
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">

                {{-- Preview embed --}}
                <button wire:click="openPreview({{ $q->id }})"
                    style="display:inline-flex;align-items:center;gap:5px;padding:7px 12px;background:#FDEDEC;color:#C0392B;border:none;border-radius:7px;cursor:pointer;font-size:0.78rem;font-weight:600;font-family:inherit;">
                    👁️ Preview
                </button>

                {{-- Edit form di Google --}}
                @if($q->google_form_edit_url)
                <a href="{{ $q->google_form_edit_url }}" target="_blank"
                    style="display:inline-flex;align-items:center;gap:5px;padding:7px 12px;background:#EBF5FB;color:#1A5276;border:none;border-radius:7px;cursor:pointer;font-size:0.78rem;font-weight:600;text-decoration:none;">
                    ✏️ Edit di Google
                </a>
                @endif

                {{-- Lihat jawaban --}}
                @if($q->google_sheet_url)
                <a href="{{ $q->google_sheet_url }}" target="_blank"
                    style="display:inline-flex;align-items:center;gap:5px;padding:7px 12px;background:#D5F5E3;color:#155724;border:none;border-radius:7px;cursor:pointer;font-size:0.78rem;font-weight:600;text-decoration:none;">
                    📊 Lihat Jawaban
                </a>
                @endif

                {{-- Toggle aktif --}}
                <button wire:click="toggleActive({{ $q->id }})"
                    style="display:inline-flex;align-items:center;gap:5px;padding:7px 12px;background:#F3F4F6;color:#374151;border:none;border-radius:7px;cursor:pointer;font-size:0.78rem;font-weight:600;font-family:inherit;">
                    {{ $q->is_active ? '⏸ Nonaktifkan' : '▶ Aktifkan' }}
                </button>

                {{-- Edit data --}}
                <button wire:click="edit({{ $q->id }})"
                    style="display:inline-flex;align-items:center;gap:5px;padding:7px 12px;background:#F3F4F6;color:#374151;border:none;border-radius:7px;cursor:pointer;font-size:0.78rem;font-weight:600;font-family:inherit;">
                    🖊 Edit Data
                </button>

                {{-- Hapus --}}
                <button wire:click="delete({{ $q->id }})"
                    wire:confirm="Yakin hapus soal '{{ $q->title }}'? Tindakan ini tidak dapat dibatalkan."
                    style="display:inline-flex;align-items:center;gap:5px;padding:7px 12px;background:#FDEDEC;color:#C0392B;border:none;border-radius:7px;cursor:pointer;font-size:0.78rem;font-weight:600;font-family:inherit;">
                    🗑️ Hapus
                </button>

            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination --}}
@if($this->questions->hasPages())
<div style="margin-top:1rem;">
    {{ $this->questions->links() }}
</div>
@endif

@endif

</div>