{{-- resources/views/livewire/admin/students.blade.php --}}
<div>

{{-- ============================================================
     HEADER
     ============================================================ --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
    <div>
        <h1 style="margin:0 0 2px;font-size:16px;font-weight:700;color:#1F2937;">🎓 Manajemen Siswa</h1>
        <p style="margin:0;font-size:12px;color:#6B7280;">Kelola data seluruh siswa</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <button wire:click="$set('showImport', true)"
            style="display:inline-flex;align-items:center;gap:6px;background:#27AE60;color:white;border:none;border-radius:8px;padding:9px 14px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
            📊 Import Excel/CSV
        </button>
        <button wire:click="$set('showForm', true)"
            style="display:inline-flex;align-items:center;gap:6px;background:#C0392B;color:white;border:none;border-radius:8px;padding:9px 14px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
            ➕ Tambah Siswa
        </button>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
<div style="background:#D4EDDA;border:0.5px solid #C3E6CB;color:#155724;padding:12px 16px;border-radius:10px;font-size:14px;margin-bottom:12px;font-weight:500;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- ============================================================
     SEARCH & FILTER
     ============================================================ --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px;">
    <div style="position:relative;flex:1;min-width:200px;">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#9CA3AF" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Cari nama, email, NIS, NISN..."
            style="width:100%;padding:9px 12px 9px 32px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;outline:none;font-family:inherit;box-sizing:border-box;"
            onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
    </div>
    <select wire:model.live="classFilter"
        style="padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;background:white;font-family:inherit;outline:none;">
        <option value="">Semua Kelas</option>
        @foreach($classRooms as $cls)
        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
        @endforeach
    </select>
</div>

{{-- ============================================================
     MODAL FORM MANUAL (ADD / EDIT)
     ============================================================ --}}
@if($showForm)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.25);">

        {{-- Modal Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:18px;">{{ $editId ? '✏️' : '➕' }}</span>
                <h3 style="margin:0;font-size:14px;font-weight:700;color:#1F2937;">
                    {{ $editId ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}
                </h3>
            </div>
            <button wire:click="resetForm" style="background:#F3F4F6;border:none;color:#374151;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        {{-- Modal Body --}}
        <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:14px;">

            <div style="grid-column:1/-1;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">Nama Lengkap <span style="color:#C0392B;">*</span></label>
                <input type="text" wire:model="name" placeholder="Nama lengkap siswa"
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

            <div style="grid-column:1/-1;">
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                    Password 
                    @if($editId)
                        <span style="color:#9CA3AF;font-weight:400;">(kosongkan jika tidak diubah)</span>
                    @else
                        <span style="color:#C0392B;">*</span>
                    @endif
                </label>
                <input type="password" wire:model="password" placeholder="••••••••"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
                @error('password') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror
            </div>

            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">NIS</label>
                <input type="text" wire:model="nis" placeholder="Nomor Induk Siswa"
                    style="width:100%;padding:9px 12px;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#C0392B'" onblur="this.style.borderColor='#E5E7EB'">
            </div>

            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:6px;">NISN</label>
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
        </div>

        {{-- Modal Footer --}}
        <div style="display:flex;gap:10px;justify-content:flex-end;padding:14px 20px;border-top:1px solid #F3F4F6;">
            <button wire:click="resetForm"
                style="padding:9px 16px;background:white;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;font-family:inherit;">
                Batal
            </button>
            <button wire:click="save" wire:loading.attr="disabled"
                style="padding:9px 16px;background:#C0392B;border:none;border-radius:8px;font-size:13px;font-weight:700;color:white;cursor:pointer;font-family:inherit;">
                <span wire:loading.remove wire:target="save">💾 Simpan</span>
                <span wire:loading wire:target="save">⏳ Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ============================================================
     MODAL IMPORT EXCEL / CSV
     ============================================================ --}}
@if($showImport)
<div style="position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:200;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.25);">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #F3F4F6;position:sticky;top:0;background:white;z-index:1;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:18px;">📊</span>
                <h3 style="margin:0;font-size:14px;font-weight:700;color:#1F2937;">Import Siswa via Excel / CSV</h3>
            </div>
            <button wire:click="cancelImport" style="background:#F3F4F6;border:none;color:#374151;width:28px;height:28px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        <div style="padding:20px;">

            {{-- Format guide --}}
            <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:14px;margin-bottom:16px;">
                <div style="font-size:13px;font-weight:700;color:#1E40AF;margin-bottom:8px;">📋 Format Kolom yang Diperlukan:</div>
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:12px;">
                        <thead>
                            <tr style="background:#DBEAFE;">
                                <th style="padding:6px 10px;text-align:left;color:#1E40AF;border:1px solid #BFDBFE;">nama / name</th>
                                <th style="padding:6px 10px;text-align:left;color:#1E40AF;border:1px solid #BFDBFE;">email</th>
                                <th style="padding:6px 10px;text-align:left;color:#9CA3AF;border:1px solid #BFDBFE;">password <em>(opsional)</em></th>
                                <th style="padding:6px 10px;text-align:left;color:#9CA3AF;border:1px solid #BFDBFE;">nis <em>(opsional)</em></th>
                                <th style="padding:6px 10px;text-align:left;color:#9CA3AF;border:1px solid #BFDBFE;">nisn <em>(opsional)</em></th>
                                <th style="padding:6px 10px;text-align:left;color:#9CA3AF;border:1px solid #BFDBFE;">kelas <em>(opsional)</em></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background:white;">
                                <td style="padding:6px 10px;border:1px solid #BFDBFE;color:#374151;">Budi Santoso</td>
                                <td style="padding:6px 10px;border:1px solid #BFDBFE;color:#374151;">budi@gmail.com</td>
                                <td style="padding:6px 10px;border:1px solid #BFDBFE;color:#9CA3AF;">password123</td>
                                <td style="padding:6px 10px;border:1px solid #BFDBFE;color:#9CA3AF;">2024001</td>
                                <td style="padding:6px 10px;border:1px solid #BFDBFE;color:#9CA3AF;">002024001</td>
                                <td style="padding:6px 10px;border:1px solid #BFDBFE;color:#9CA3AF;">IX A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="font-size:11px;color:#3B82F6;margin-top:8px;line-height:1.7;">
                    ⚠️ <strong>Jika email sudah ada di database</strong>, data siswa akan diperbarui (nama, NIS, NISN, kelas).<br>
                    📁 Format yang didukung: <strong>.csv</strong> atau <strong>.xlsx</strong><br>
                    🔑 Password default <strong>"password"</strong> jika kolom password dikosongkan.
                </div>
            </div>

            {{-- Upload area --}}
            @if(empty($importPreview))
            <div>
                <label style="font-size:12px;font-weight:700;color:#374151;display:block;margin-bottom:8px;">Pilih File:</label>
                <input type="file" wire:model="importFile" accept=".csv,.xlsx,.txt"
                    style="width:100%;padding:10px;border:2px dashed #E5E7EB;border-radius:8px;font-size:13px;font-family:inherit;background:#F9FAFB;cursor:pointer;box-sizing:border-box;">

                @error('importFile') <p style="color:#C0392B;font-size:11px;margin-top:4px;">{{ $message }}</p> @enderror

                @if($importError)
                <div style="background:#FDEDEC;border:0.5px solid #F1948A;color:#C0392B;padding:10px 12px;border-radius:8px;font-size:13px;margin-top:10px;">
                    ⚠️ {{ $importMsg }}
                </div>
                @endif

                <button wire:click="previewImport" wire:loading.attr="disabled"
                    style="margin-top:12px;width:100%;padding:10px;background:#1E40AF;border:none;border-radius:8px;font-size:13px;font-weight:700;color:white;cursor:pointer;font-family:inherit;">
                    <span wire:loading.remove wire:target="previewImport">🔍 Preview Data</span>
                    <span wire:loading wire:target="previewImport">⏳ Memproses...</span>
                </button>
            </div>
            @endif

            {{-- Preview table --}}
            @if(!empty($importPreview))
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <div style="font-size:13px;font-weight:700;color:#374151;">Preview Data ({{ count($importPreview) }} baris)</div>
                    <button wire:click="$set('importPreview', [])"
                        style="font-size:12px;color:#6B7280;background:none;border:none;cursor:pointer;text-decoration:underline;">
                        Ganti file
                    </button>
                </div>

                @if($importMsg && !$importError)
                <div style="background:#D4EDDA;border:0.5px solid #C3E6CB;color:#155724;padding:10px 12px;border-radius:8px;font-size:13px;margin-bottom:10px;">
                    ✅ {{ $importMsg }}
                </div>
                @endif

                <div style="overflow-x:auto;border:1px solid #E5E7EB;border-radius:8px;max-height:300px;overflow-y:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:12px;min-width:520px;">
                        <thead style="position:sticky;top:0;">
                            <tr style="background:#F9FAFB;">
                                <th style="padding:8px 10px;text-align:left;color:#6B7280;border-bottom:1px solid #E5E7EB;font-size:11px;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">#</th>
                                <th style="padding:8px 10px;text-align:left;color:#6B7280;border-bottom:1px solid #E5E7EB;font-size:11px;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Nama</th>
                                <th style="padding:8px 10px;text-align:left;color:#6B7280;border-bottom:1px solid #E5E7EB;font-size:11px;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Email</th>
                                <th style="padding:8px 10px;text-align:left;color:#6B7280;border-bottom:1px solid #E5E7EB;font-size:11px;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">NIS</th>
                                <th style="padding:8px 10px;text-align:left;color:#6B7280;border-bottom:1px solid #E5E7EB;font-size:11px;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">NISN</th>
                                <th style="padding:8px 10px;text-align:left;color:#6B7280;border-bottom:1px solid #E5E7EB;font-size:11px;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Kelas</th>
                                <th style="padding:8px 10px;text-align:left;color:#6B7280;border-bottom:1px solid #E5E7EB;font-size:11px;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($importPreview as $i => $row)
                            @php
                                $existingUser = \App\Models\User::where('email', $row['email'])->exists();
                            @endphp
                            <tr style="border-bottom:0.5px solid #F3F4F6;{{ $existingUser ? 'background:#FFFBEB;' : '' }}">
                                <td style="padding:7px 10px;color:#9CA3AF;">{{ $i + 1 }}</td>
                                <td style="padding:7px 10px;font-weight:600;color:#1F2937;">{{ $row['name'] }}</td>
                                <td style="padding:7px 10px;color:#6B7280;">{{ $row['email'] }}</td>
                                <td style="padding:7px 10px;color:#374151;">{{ $row['nis'] ?: '—' }}</td>
                                <td style="padding:7px 10px;color:#374151;">{{ $row['nisn'] ?: '—' }}</td>
                                <td style="padding:7px 10px;color:#374151;">{{ $row['kelas'] ?: '—' }}</td>
                                <td style="padding:7px 10px;">
                                    @if($existingUser)
                                    <span style="background:#FEF3C7;color:#92400E;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:700;white-space:nowrap;">🔄 Update</span>
                                    @else
                                    <span style="background:#D5F5E3;color:#155724;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:700;white-space:nowrap;">✨ Baru</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:6px;font-size:11px;color:#9CA3AF;">
                    🟡 Baris kuning = email sudah ada → data akan diperbarui
                </div>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div style="display:flex;gap:10px;justify-content:flex-end;padding:14px 20px;border-top:1px solid #F3F4F6;">
            <button wire:click="cancelImport"
                style="padding:9px 16px;background:white;border:1.5px solid #E5E7EB;border-radius:8px;font-size:13px;font-weight:600;color:#374151;cursor:pointer;font-family:inherit;">
                Batal
            </button>
            @if(!empty($importPreview))
            <button wire:click="confirmImport" wire:loading.attr="disabled"
                style="padding:9px 16px;background:#27AE60;border:none;border-radius:8px;font-size:13px;font-weight:700;color:white;cursor:pointer;font-family:inherit;">
                <span wire:loading.remove wire:target="confirmImport">✅ Konfirmasi Import</span>
                <span wire:loading wire:target="confirmImport">⏳ Mengimpor...</span>
            </button>
            @endif
        </div>
    </div>
</div>
@endif

{{-- ============================================================
     STUDENTS TABLE
     ============================================================ --}}
<div style="background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);overflow:hidden;">

    <div style="padding:12px 16px;border-bottom:1px solid #F3F4F6;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:13px;font-weight:600;color:#374151;">
            Total: {{ $this->students->total() }} siswa
        </span>
    </div>

    <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
        <table style="width:100%;border-collapse:collapse;min-width:600px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;">Siswa</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:100px;">NIS</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:110px;">NISN</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:90px;">Kelas</th>
                    <th style="text-align:left;padding:10px 14px;font-size:11px;font-weight:700;color:#6B7280;background:#F9FAFB;border-bottom:1px solid #F3F4F6;text-transform:uppercase;letter-spacing:0.4px;white-space:nowrap;width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->students as $student)
                <tr style="border-bottom:0.5px solid #F3F4F6;">
                    <td style="padding:10px 14px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#FDEDEC;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#C0392B;flex-shrink:0;">
                                {{ strtoupper(substr($student->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div style="min-width:0;">
                                <div style="font-size:13px;font-weight:600;color:#1F2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $student->user->name }}</div>
                                <div style="font-size:11px;color:#9CA3AF;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $student->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:10px 14px;font-size:13px;color:#374151;">{{ $student->nis ?? '—' }}</td>
                    <td style="padding:10px 14px;font-size:13px;color:#374151;">{{ $student->nisn ?? '—' }}</td>
                    <td style="padding:10px 14px;">
                        @if($student->classRoom)
                        <span style="background:#FDEDEC;color:#C0392B;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700;white-space:nowrap;">
                            {{ $student->classRoom->name }}
                        </span>
                        @else
                        <span style="color:#D1D5DB;font-size:12px;">—</span>
                        @endif
                    </td>
                    <td style="padding:10px 14px;">
                        <div style="display:flex;gap:6px;">
                            <button wire:click="edit({{ $student->user_id }})"
                                style="padding:4px 10px;background:#EBF5FB;color:#1A5276;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;">
                                ✏️
                            </button>
                            <button wire:click="delete({{ $student->user_id }})"
                                wire:confirm="Yakin hapus siswa '{{ $student->user->name }}'?"
                                style="padding:4px 10px;background:#FDEDEC;color:#C0392B;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;">
                                🗑️
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding:40px 16px;text-align:center;color:#9CA3AF;">
                        <div style="font-size:32px;margin-bottom:8px;">🎓</div>
                        <div style="font-size:14px;font-weight:500;">Belum ada data siswa</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($this->students->hasPages())
    <div style="padding:12px 16px;border-top:1px solid #F3F4F6;">
        {{ $this->students->links() }}
    </div>
    @endif
</div>

</div>