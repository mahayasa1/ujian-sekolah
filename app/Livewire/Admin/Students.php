<?php
// app/Livewire/Admin/Students.php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class Students extends Component
{
    use WithPagination, WithFileUploads;

    // --- Search & Filter ---
    public string $search        = '';
    public string $classFilter   = '';

    // --- Form state ---
    public bool   $showForm      = false;
    public ?int   $editId        = null;

    // --- Form fields ---
    public string $name          = '';
    public string $email         = '';
    public string $password      = '';
    public string $nis           = '';
    public string $nisn          = '';
    public int    $classRoomId   = 0;

    // --- Import state ---
    public bool   $showImport    = false;
    public        $importFile    = null;
    public array  $importPreview = [];
    public string $importMsg     = '';
    public bool   $importError   = false;

    // ----------------------------------------------------------------
    // Paginated student list
    // ----------------------------------------------------------------
    public function getStudentsProperty()
    {
        return Student::with(['user', 'classRoom'])
            ->when($this->search, fn($q) =>
                $q->whereHas('user', fn($u) =>
                    $u->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                )->orWhere('nis', 'like', "%{$this->search}%")
                 ->orWhere('nisn', 'like', "%{$this->search}%")
            )
            ->when($this->classFilter, fn($q) =>
                $q->where('class_room_id', $this->classFilter)
            )
            ->latest()
            ->paginate(15);
    }

    // ----------------------------------------------------------------
    // Manual CRUD
    // ----------------------------------------------------------------
    public function save()
    {
        $rules = [
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email' . ($this->editId ? ",{$this->editId}" : ''),
            'nis'         => 'nullable|string|max:20',
            'nisn'        => 'nullable|string|max:20',
            'classRoomId' => 'nullable|integer',
        ];

        if (! $this->editId) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        DB::transaction(function () {
            $data = [
                'name'  => $this->name,
                'email' => strtolower($this->email),
                'role'  => 'siswa',
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            if ($this->editId) {
                $user = User::findOrFail($this->editId);
                $user->update($data);
                $student = $user->student;
            } else {
                $user    = User::create($data);
                $student = null;
            }

            $studentData = [
                'user_id'      => $user->id,
                'nis'          => $this->nis  ?: null,
                'nisn'         => $this->nisn ?: null,
                'class_room_id'=> $this->classRoomId ?: null,
            ];

            if ($student) {
                $student->update($studentData);
            } else {
                Student::create($studentData);
            }
        });

        $this->resetForm();
        session()->flash('success', 'Data siswa berhasil disimpan.');
    }

    public function edit(int $userId)
    {
        $user = User::with('student')->findOrFail($userId);

        $this->editId      = $userId;
        $this->name        = $user->name;
        $this->email       = $user->email;
        $this->password    = '';
        $this->nis         = $user->student?->nis  ?? '';
        $this->nisn        = $user->student?->nisn ?? '';
        $this->classRoomId = $user->student?->class_room_id ?? 0;
        $this->showForm    = true;
    }

    public function delete(int $userId)
    {
        $user = User::find($userId);
        if ($user && $user->id !== auth()->id()) {
            $user->delete();
            session()->flash('success', 'Siswa berhasil dihapus.');
        }
    }

    public function resetForm()
    {
        $this->reset([
            'showForm', 'editId', 'name', 'email',
            'password', 'nis', 'nisn', 'classRoomId',
        ]);
    }

    // ----------------------------------------------------------------
    // Import via Excel / Spreadsheet
    // ----------------------------------------------------------------

    /**
     * Parse uploaded file and show preview before committing.
     * Supported: .csv, .xlsx (via manual CSV parse — no PhpSpreadsheet needed for CSV).
     * For xlsx, we ask the user to export as CSV first, OR we handle both.
     */
    public function previewImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt,xlsx|max:2048',
        ]);

        $this->importMsg     = '';
        $this->importError   = false;
        $this->importPreview = [];

        $path = $this->importFile->getRealPath();
        $ext  = strtolower($this->importFile->getClientOriginalExtension());

        $rows = [];

        if ($ext === 'xlsx') {
            // Read xlsx without PhpSpreadsheet using a simple ZIP extraction of the sheet XML
            $rows = $this->readXlsx($path);
        } else {
            // CSV / TXT
            if (($handle = fopen($path, 'r')) !== false) {
                $header = null;
                while (($line = fgetcsv($handle, 1000, ',')) !== false) {
                    if (! $header) {
                        $header = array_map('strtolower', array_map('trim', $line));
                        continue;
                    }
                    if (count($line) < 2) continue;
                    $rows[] = array_combine($header, array_pad($line, count($header), ''));
                }
                fclose($handle);
            }
        }

        if (empty($rows)) {
            $this->importError = true;
            $this->importMsg   = 'File kosong atau format tidak dikenali. Pastikan header kolom benar.';
            return;
        }

        // Normalize rows → pick known columns
        $preview = [];
        foreach ($rows as $r) {
            $preview[] = [
                'name'     => trim($r['nama']         ?? $r['name']         ?? ''),
                'email'    => strtolower(trim($r['email'] ?? '')),
                'password' => trim($r['password']     ?? 'password'),
                'nis'      => trim($r['nis']           ?? ''),
                'nisn'     => trim($r['nisn']          ?? ''),
                'kelas'    => trim($r['kelas']         ?? $r['class']        ?? $r['classroom'] ?? ''),
            ];
        }

        // Filter rows that have at least name + email
        $preview = array_filter($preview, fn($r) => $r['name'] !== '' && $r['email'] !== '');
        $preview = array_values($preview);

        if (empty($preview)) {
            $this->importError = true;
            $this->importMsg   = 'Tidak ada baris valid ditemukan. Pastikan kolom: nama/name, email ada.';
            return;
        }

        $this->importPreview = $preview;
        $this->importMsg     = count($preview) . ' baris siap diimpor. Cek data lalu klik Konfirmasi Import.';
    }

    public function confirmImport()
    {
        if (empty($this->importPreview)) {
            $this->importError = true;
            $this->importMsg   = 'Tidak ada data untuk diimpor.';
            return;
        }

        $inserted = 0;
        $updated  = 0;

        DB::transaction(function () use (&$inserted, &$updated) {
            foreach ($this->importPreview as $row) {
                if (empty($row['name']) || empty($row['email'])) continue;

                // Resolve class_room_id from kelas name
                $classRoomId = null;
                if ($row['kelas'] !== '') {
                    $class = ClassRoom::where('name', $row['kelas'])->first();
                    if ($class) $classRoomId = $class->id;
                }

                // Upsert User by email
                $user = User::where('email', $row['email'])->first();

                if ($user) {
                    // Update existing user
                    $user->update([
                        'name' => $row['name'],
                        'role' => 'siswa',
                    ]);
                    $updated++;
                } else {
                    // Create new user
                    $user = User::create([
                        'name'     => $row['name'],
                        'email'    => $row['email'],
                        'password' => Hash::make($row['password'] ?: 'password'),
                        'role'     => 'siswa',
                    ]);
                    $inserted++;
                }

                // Upsert Student profile
                Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nis'           => $row['nis']  ?: null,
                        'nisn'          => $row['nisn'] ?: null,
                        'class_room_id' => $classRoomId,
                    ]
                );
            }
        });

        $this->importPreview = [];
        $this->importFile    = null;
        $this->showImport    = false;
        $this->importError   = false;
        $this->importMsg     = '';

        session()->flash('success', "Import selesai: {$inserted} siswa baru ditambahkan, {$updated} siswa diperbarui.");
    }

    public function cancelImport()
    {
        $this->reset(['showImport', 'importFile', 'importPreview', 'importMsg', 'importError']);
    }

    // ----------------------------------------------------------------
    // Simple XLSX reader (no external library needed)
    // ----------------------------------------------------------------
    private function readXlsx(string $path): array
    {
        $rows = [];
        try {
            $zip = new \ZipArchive();
            if ($zip->open($path) !== true) return [];

            // Read shared strings
            $strings = [];
            $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
            if ($sharedXml) {
                $xml = simplexml_load_string($sharedXml);
                foreach ($xml->si as $si) {
                    $val = '';
                    if (isset($si->t)) {
                        $val = (string) $si->t;
                    } elseif (isset($si->r)) {
                        foreach ($si->r as $r) {
                            $val .= (string) $r->t;
                        }
                    }
                    $strings[] = $val;
                }
            }

            // Read first sheet
            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            $zip->close();

            if (! $sheetXml) return [];

            $xml  = simplexml_load_string($sheetXml);
            $data = [];

            foreach ($xml->sheetData->row as $row) {
                $rowData = [];
                foreach ($row->c as $cell) {
                    $t   = (string) ($cell['t'] ?? '');
                    $val = (string) ($cell->v ?? '');
                    if ($t === 's') {
                        $val = $strings[(int) $val] ?? '';
                    }
                    $rowData[] = $val;
                }
                $data[] = $rowData;
            }

            if (empty($data)) return [];

            $header = array_map('strtolower', array_map('trim', $data[0]));
            for ($i = 1; $i < count($data); $i++) {
                if (count($data[$i]) < 2) continue;
                $rows[] = array_combine($header, array_pad($data[$i], count($header), ''));
            }
        } catch (\Throwable $e) {
            // fallback — return empty
        }

        return $rows;
    }

    // ----------------------------------------------------------------
    // Render
    // ----------------------------------------------------------------
    public function render()
    {
        $classRooms = ClassRoom::orderBy('grade')->orderBy('name')->get();

        return view('livewire.admin.students', compact('classRooms'))
            ->layout('components.layouts.digitest', ['title' => 'Manajemen Siswa']);
    }
}