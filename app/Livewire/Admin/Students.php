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
    public string $sortField     = 'id';
    public string $sortDirection = 'asc';

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

    // --- Import batch progress state ---
    // The 168-student import is split into small batches, and each batch
    // is processed in its OWN Livewire (HTTP) request — triggered by the
    // browser via the `import-batch-done` event below — instead of a
    // single request hashing all 168 passwords, which used to blow past
    // `max_execution_time` inside BcryptHasher.
    public int    $importIndex     = 0;
    public int    $importTotal     = 0;
    public int    $importBatchSize = 10;
    public int    $importInserted  = 0;
    public int    $importUpdated   = 0;
    public bool   $importing       = false;
    public bool   $importCompleted = false;

    // ----------------------------------------------------------------
    // Paginated student list
    // ----------------------------------------------------------------
    public function getStudentsProperty()
    {
        $query = Student::query()
            ->select('students.*')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->leftJoin('class_rooms', 'class_rooms.id', '=', 'students.class_room_id')
            ->with(['user', 'classRoom'])
            ->when($this->search, fn($q) =>
                $q->where(function ($query) {
                    $query->where('users.name', 'like', "%{$this->search}%")
                        ->orWhere('users.email', 'like', "%{$this->search}%")
                        ->orWhere('students.nis', 'like', "%{$this->search}%")
                        ->orWhere('students.nisn', 'like', "%{$this->search}%");
                })
            )
            ->when($this->classFilter, fn($q) =>
                $q->where('students.class_room_id', $this->classFilter)
            );

        match ($this->sortField) {
            'name'  => $query->orderBy('users.name', $this->sortDirection),
            'email' => $query->orderBy('users.email', $this->sortDirection),
            'nis'   => $query->orderBy('students.nis', $this->sortDirection),
            'nisn'  => $query->orderBy('students.nisn', $this->sortDirection),
            'kelas' => $query->orderBy('class_rooms.name', $this->sortDirection),
            default => $query->orderBy('students.id', $this->sortDirection),
        };

        return $query->paginate(15);
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

        $preview = [];

        /*
        |--------------------------------------------------------------------------
        | XLSX
        |--------------------------------------------------------------------------
        */
        if ($ext === 'xlsx') {

            $rows = $this->readXlsx($path);

            foreach ($rows as $r) {

                $name = trim(
                    $r['nama']
                    ?? $r['name']
                    ?? ''
                );

                $email = strtolower(
                    trim($r['email'] ?? '')
                );

                // Baris kosong / tidak lengkap langsung dilewati
                if ($name === '' || $email === '') {
                    continue;
                }

                $preview[] = [
                    'name'     => $name,
                    'email'    => $email,
                    'password' => trim($r['password'] ?? 'password'),
                    'nis'      => trim($r['nis'] ?? ''),
                    'nisn'     => trim($r['nisn'] ?? ''),
                    'kelas'    => trim(
                        $r['kelas']
                        ?? $r['class']
                        ?? $r['classroom']
                        ?? ''
                    ),
                ];
            }

        /*
        |--------------------------------------------------------------------------
        | CSV / TXT
        |--------------------------------------------------------------------------
        */
        } else {

            if (($handle = fopen($path, 'r')) !== false) {

                $header = null;

                while (($line = fgetcsv($handle, 1000, ',')) !== false) {

                    // Skip baris kosong
                    if (count(array_filter($line, fn ($v) => trim((string) $v) !== '')) === 0) {
                        continue;
                    }

                    // Header
                    if ($header === null) {
                        $header = array_map(
                            'strtolower',
                            array_map('trim', $line)
                        );

                        continue;
                    }

                    if (count($line) < 2) {
                        continue;
                    }

                    $line = array_pad(
                        $line,
                        count($header),
                        ''
                    );

                    $row = array_combine($header, $line);

                    if (!$row) {
                        continue;
                    }

                    $name = trim(
                        $row['nama']
                        ?? $row['name']
                        ?? ''
                    );

                    $email = strtolower(
                        trim($row['email'] ?? '')
                    );

                    if ($name === '' || $email === '') {
                        continue;
                    }

                    $preview[] = [
                        'name'     => $name,
                        'email'    => $email,
                        'password' => trim($row['password'] ?? 'password'),
                        'nis'      => trim($row['nis'] ?? ''),
                        'nisn'     => trim($row['nisn'] ?? ''),
                        'kelas'    => trim(
                            $row['kelas']
                            ?? $row['class']
                            ?? $row['classroom']
                            ?? ''
                        ),
                    ];
                }

                fclose($handle);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi hasil
        |--------------------------------------------------------------------------
        */

        if (empty($preview)) {
            $this->importError = true;
            $this->importMsg   =
                'Tidak ada baris valid ditemukan. Pastikan kolom nama/name dan email tersedia.';

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Cek email yang sudah ada — 1 query untuk semua data
        |--------------------------------------------------------------------------
        */

        $emails = collect($preview)
            ->pluck('email')
            ->filter()
            ->map(fn ($email) => strtolower(trim($email)))
            ->unique()
            ->values();

        $existingEmails = User::whereIn('email', $emails)
            ->pluck('email')
            ->map(fn ($email) => strtolower($email))
            ->flip()
            ->all();

        foreach ($preview as &$row) {
            $row['existing'] = isset($existingEmails[$row['email']]);
        }

        unset($row);


        /*
        |--------------------------------------------------------------------------
        | Simpan preview
        |--------------------------------------------------------------------------
        */
        $this->importPreview = array_values($preview);

        $count = count($this->importPreview);

        $this->importMsg =
            "{$count} baris siap diimpor. Cek data lalu klik Konfirmasi Import.";
    }

    /**
     * Kick off the batched import. Called once when the user clicks
     * "Konfirmasi Import" in the modal. Sets up the counters, then runs
     * the FIRST batch synchronously; every subsequent batch is triggered
     * by the browser via the `import-batch-done` event (see the Alpine
     * x-init listener in students.blade.php), so each batch runs as its
     * own fresh HTTP request and bcrypt never has to hash more than
     * $importBatchSize passwords per request.
     */
    public function confirmImport()
    {
        if (empty($this->importPreview)) {
            return;
        }

        session()->put('student_import_data', $this->importPreview);

        $this->importing = true;
        $this->importCompleted = false;
        $this->importIndex = 0;
        $this->importInserted = 0;
        $this->importUpdated = 0;
        $this->importTotal = count($this->importPreview);

        // Tidak perlu lagi membawa 168 row
        // di state Livewire setiap request.
        $this->importPreview = [];

        $this->processImportBatch();
    }

    /**
     * Process ONE batch (default 10 rows) of $importPreview, starting at
     * $importIndex. This runs as an independent HTTP request every time
     * it's called, so bcrypt only ever hashes up to $importBatchSize new
     * passwords per request — well within max_execution_time.
     */
    public function processImportBatch()
{
    $requestId = uniqid('IMPORT-', true);

    Log::info('====================================================');
    Log::info("{$requestId} PROCESS IMPORT BATCH START");

    Log::info("{$requestId} INITIAL STATE", [
        'importing' => $this->importing,
        'importCompleted' => $this->importCompleted,
        'importIndex' => $this->importIndex,
        'importTotal' => $this->importTotal,
        'importBatchSize' => $this->importBatchSize,

        'preview_count' => is_array($this->importPreview)
            ? count($this->importPreview)
            : 0,

        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    /*
    |--------------------------------------------------------------------------
    | CEK APAKAH SUDAH SELESAI
    |--------------------------------------------------------------------------
    */

    if (
        empty($this->importPreview) ||
        $this->importIndex >= $this->importTotal
    ) {
        Log::info("{$requestId} IMPORT ALREADY FINISHED", [
            'importIndex' => $this->importIndex,
            'importTotal' => $this->importTotal,
            'preview_empty' => empty($this->importPreview),
        ]);

        $this->importing       = false;
        $this->importCompleted = true;

        Log::info("{$requestId} PROCESS IMPORT BATCH END - ALREADY FINISHED");

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL BATCH
    |--------------------------------------------------------------------------
    */

    $batch = array_slice(
        $this->importPreview,
        $this->importIndex,
        $this->importBatchSize
    );

    Log::info("{$requestId} BATCH CREATED", [
        'start_index' => $this->importIndex,
        'batch_size' => count($batch),
        'configured_batch_size' => $this->importBatchSize,
        'total' => $this->importTotal,

        'first_email' => $batch[0]['email'] ?? null,
        'last_email' => $batch[count($batch) - 1]['email'] ?? null,

        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    /*
    |--------------------------------------------------------------------------
    | LOAD CLASSROOM SEKALI
    |--------------------------------------------------------------------------
    */

    Log::info("{$requestId} BEFORE CLASSROOM QUERY");

    $classRooms = ClassRoom::pluck('id', 'name');

    Log::info("{$requestId} AFTER CLASSROOM QUERY", [
        'classroom_count' => $classRooms->count(),
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION
    |--------------------------------------------------------------------------
    */

    try {

        DB::transaction(function () use ($batch, $requestId, $classRooms) {

            Log::info("{$requestId} TRANSACTION START", [
                'batch_count' => count($batch),
            ]);

            foreach ($batch as $batchKey => $row) {

                $number = $batchKey + 1;

                Log::info("{$requestId} ROW START", [
                    'batch_row' => $number,
                    'name' => $row['name'] ?? null,
                    'email' => $row['email'] ?? null,
                    'nis' => $row['nis'] ?? null,
                    'nisn' => $row['nisn'] ?? null,
                    'kelas' => $row['kelas'] ?? null,

                    'memory_mb' => round(
                        memory_get_usage(true) / 1024 / 1024,
                        2
                    ),
                ]);

                /*
                |--------------------------------------------------------------------------
                | VALIDASI DATA
                |--------------------------------------------------------------------------
                */

                if (
                    empty($row['name']) ||
                    empty($row['email'])
                ) {
                    Log::warning("{$requestId} ROW SKIPPED", [
                        'batch_row' => $number,
                        'reason' => 'name atau email kosong',
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | CLASSROOM
                |--------------------------------------------------------------------------
                */

                $classRoomId = null;

                if (!empty($row['kelas'])) {

                    $classRoomId = $classRooms[$row['kelas']] ?? null;

                    Log::info("{$requestId} CLASSROOM CHECK", [
                        'batch_row' => $number,
                        'kelas' => $row['kelas'],
                        'class_room_id' => $classRoomId,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | CARI USER
                |--------------------------------------------------------------------------
                */

                Log::info("{$requestId} BEFORE USER QUERY", [
                    'batch_row' => $number,
                    'email' => $row['email'],
                ]);

                $user = User::where(
                    'email',
                    $row['email']
                )->first();

                Log::info("{$requestId} AFTER USER QUERY", [
                    'batch_row' => $number,
                    'user_found' => (bool) $user,
                    'user_id' => $user?->id,
                ]);

                /*
                |--------------------------------------------------------------------------
                | UPDATE USER
                |--------------------------------------------------------------------------
                */

                if ($user) {

                    Log::info("{$requestId} BEFORE USER UPDATE", [
                        'batch_row' => $number,
                        'user_id' => $user->id,
                    ]);

                    $user->update([
                        'name' => $row['name'],
                        'role' => 'siswa',
                    ]);

                    $this->importUpdated++;

                    Log::info("{$requestId} AFTER USER UPDATE", [
                        'batch_row' => $number,
                        'user_id' => $user->id,
                        'importUpdated' => $this->importUpdated,
                    ]);

                /*
                |--------------------------------------------------------------------------
                | CREATE USER
                |--------------------------------------------------------------------------
                */

                } else {

                    $password = $row['password'] ?: 'password';

                    Log::info("{$requestId} BEFORE PASSWORD HASH", [
                        'batch_row' => $number,
                        'email' => $row['email'],

                        // JANGAN LOG PASSWORD ASLI
                        'password_length' => strlen($password),

                        'memory_mb' => round(
                            memory_get_usage(true) / 1024 / 1024,
                            2
                        ),
                    ]);

                    $hashStart = microtime(true);

                    $hashedPassword = Hash::make($password);

                    $hashTime = round(
                        microtime(true) - $hashStart,
                        3
                    );

                    Log::info("{$requestId} AFTER PASSWORD HASH", [
                        'batch_row' => $number,
                        'hash_time_seconds' => $hashTime,

                        'memory_mb' => round(
                            memory_get_usage(true) / 1024 / 1024,
                            2
                        ),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE USER
                    |--------------------------------------------------------------------------
                    */

                    Log::info("{$requestId} BEFORE USER CREATE", [
                        'batch_row' => $number,
                        'email' => $row['email'],
                    ]);

                    $user = User::create([
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'password' => $hashedPassword,
                        'role' => 'siswa',
                    ]);

                    $this->importInserted++;

                    Log::info("{$requestId} AFTER USER CREATE", [
                        'batch_row' => $number,
                        'user_id' => $user->id,
                        'importInserted' => $this->importInserted,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | STUDENT
                |--------------------------------------------------------------------------
                */

                Log::info("{$requestId} BEFORE STUDENT UPDATE OR CREATE", [
                    'batch_row' => $number,
                    'user_id' => $user->id,
                    'class_room_id' => $classRoomId,
                ]);

                Student::updateOrCreate(
                    [
                        'user_id' => $user->id,
                    ],
                    [
                        'nis' => $row['nis'] ?: null,
                        'nisn' => $row['nisn'] ?: null,
                        'class_room_id' => $classRoomId,
                    ]
                );

                Log::info("{$requestId} AFTER STUDENT UPDATE OR CREATE", [
                    'batch_row' => $number,
                    'user_id' => $user->id,
                ]);

                Log::info("{$requestId} ROW FINISHED", [
                    'batch_row' => $number,
                    'email' => $row['email'],
                    'memory_mb' => round(
                        memory_get_usage(true) / 1024 / 1024,
                        2
                    ),
                    'peak_memory_mb' => round(
                        memory_get_peak_usage(true) / 1024 / 1024,
                        2
                    ),
                ]);
            }

            Log::info("{$requestId} TRANSACTION CALLBACK FINISHED");
        });

        Log::info("{$requestId} TRANSACTION COMMITTED");

    } catch (\Throwable $e) {

        Log::error("{$requestId} IMPORT BATCH FAILED", [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),

            'importIndex' => $this->importIndex,
            'importTotal' => $this->importTotal,

            'memory_mb' => round(
                memory_get_usage(true) / 1024 / 1024,
                2
            ),

            'peak_memory_mb' => round(
                memory_get_peak_usage(true) / 1024 / 1024,
                2
            ),
        ]);

        $this->importing = false;
        $this->importError = true;

        $this->importMsg =
            'Import gagal pada batch ' .
            ($this->importIndex + 1) .
            '. Silakan cek log server.';

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE INDEX
    |--------------------------------------------------------------------------
    */

    $oldIndex = $this->importIndex;

    $this->importIndex += count($batch);

    Log::info("{$requestId} INDEX UPDATED", [
        'old_index' => $oldIndex,
        'new_index' => $this->importIndex,
        'total' => $this->importTotal,
        'remaining' => max(
            0,
            $this->importTotal - $this->importIndex
        ),

        'importInserted' => $this->importInserted,
        'importUpdated' => $this->importUpdated,
    ]);

    /*
    |--------------------------------------------------------------------------
    | CEK SELESAI
    |--------------------------------------------------------------------------
    */

    if ($this->importIndex >= $this->importTotal) {

        Log::info("{$requestId} IMPORT COMPLETED", [
            'total' => $this->importTotal,
            'inserted' => $this->importInserted,
            'updated' => $this->importUpdated,
        ]);

        $this->importing = false;
        $this->importCompleted = true;
        $this->importPreview = [];
        $this->importFile = null;

        session()->flash(
            'success',
            "Import selesai: {$this->importInserted} siswa baru ditambahkan, {$this->importUpdated} siswa diperbarui."
        );

        Log::info("{$requestId} PROCESS IMPORT BATCH END - COMPLETED");

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | MASIH ADA DATA
    |--------------------------------------------------------------------------
    */

    Log::info("{$requestId} DISPATCHING NEXT BATCH", [
        'current_index' => $this->importIndex,
        'total' => $this->importTotal,
        'next_batch_start' => $this->importIndex,
        'remaining' => $this->importTotal - $this->importIndex,
    ]);

    $this->dispatch('import-batch-done');

    Log::info("{$requestId} PROCESS IMPORT BATCH END - NEXT BATCH DISPATCHED");

    Log::info('====================================================');
}

    private function finishImport()
{
    session()->forget('student_import_data');

    $this->importing = false;
    $this->importCompleted = true;
    $this->importPreview = [];
    $this->importFile = null;

    session()->flash(
        'success',
        "Import selesai: {$this->importInserted} siswa baru ditambahkan, {$this->importUpdated} siswa diperbarui."
    );
}

    public function cancelImport()
    {
        $this->reset([
            'showImport', 'importFile', 'importPreview', 'importMsg', 'importError',
            'importIndex', 'importTotal', 'importInserted', 'importUpdated',
            'importing', 'importCompleted',
        ]);
    }

    // ----------------------------------------------------------------
    // Simple XLSX reader (no external library needed)
    // Reads ALL sheets in the workbook and merges their rows together,
    // so a file with one sheet per class (e.g. "VII A", "VII B", ...)
    // gets imported in one go instead of only the first sheet.
    // ----------------------------------------------------------------
    private function readXlsx(string $path): array
    {
        $rows = [];
        try {
            $zip = new \ZipArchive();
            if ($zip->open($path) !== true) return [];

            // Read shared strings (shared across all sheets)
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

            // Resolve every sheet's actual XML file, in workbook order,
            // via workbook.xml (sheet -> r:id) and workbook.xml.rels (r:id -> target file).
            $sheetTargets = [];
            $workbookXml = $zip->getFromName('xl/workbook.xml');
            $relsXml     = $zip->getFromName('xl/_rels/workbook.xml.rels');

            if ($workbookXml && $relsXml) {
                $wb   = simplexml_load_string($workbookXml);
                $rels = simplexml_load_string($relsXml);

                $ridToTarget = [];
                foreach ($rels->Relationship as $rel) {
                    $ridToTarget[(string) $rel['Id']] = (string) $rel['Target'];
                }

                $wb->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
                foreach ($wb->sheets->sheet as $sheet) {
                    $rid = (string) $sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
                    if (isset($ridToTarget[$rid])) {
                        $target = $ridToTarget[$rid];
                        // Targets are relative to xl/, e.g. "worksheets/sheet1.xml"
                        $sheetTargets[] = 'xl/' . ltrim($target, '/');
                    }
                }
            }

            // Fallback: no workbook/rels info available — assume single sheet1.xml
            if (empty($sheetTargets)) {
                $sheetTargets = ['xl/worksheets/sheet1.xml'];
            }

            foreach ($sheetTargets as $target) {
                $sheetXml = $zip->getFromName($target);
                if (! $sheetXml) continue;

                $sheetRows = $this->parseSheetRows($sheetXml, $strings);
                if (empty($sheetRows)) continue;

                // Each sheet has its own header row (first row of that sheet).
                $header = array_map('strtolower', array_map('trim', $sheetRows[0]));
                for ($i = 1; $i < count($sheetRows); $i++) {
                    if (count($sheetRows[$i]) < 2) continue;
                    $rows[] = array_combine($header, array_pad($sheetRows[$i], count($header), ''));
                }
            }

            $zip->close();
        } catch (\Throwable $e) {
            // fallback — return empty
        }

        return $rows;
    }

    /**
     * Parse a single worksheet XML string into an array of row-arrays
     * (each row is a plain numeric-indexed array of cell values, gaps
     * from skipped blank cells filled with empty strings).
     */
    private function parseSheetRows(string $sheetXml, array $strings): array
    {
        $xml = simplexml_load_string($sheetXml);

        if ($xml === false || !isset($xml->sheetData->row)) {
            return [];
        }

        $data = [];

        foreach ($xml->sheetData->row as $row) {

            $rowData = [];
            $hasValue = false;

            foreach ($row->c as $cell) {

                $ref = (string) ($cell['r'] ?? '');

                if ($ref === '') {
                    continue;
                }

                /*
                 * Ambil index kolom.
                 * Contoh:
                 * A1 -> 0
                 * B1 -> 1
                 * G1 -> 6
                 */
                if (!preg_match('/^([A-Z]+)/', $ref, $matches)) {
                    continue;
                }

                $colIndex = $this->columnLetterToIndex($matches[1]);

                // Kita hanya membutuhkan 7 kolom:
                // A = No
                // B = Nama
                // C = Email
                // D = Password
                // E = NIS
                // F = NISN
                // G = Kelas
                if ($colIndex > 6) {
                    continue;
                }

                $type  = (string) ($cell['t'] ?? '');
                $value = (string) ($cell->v ?? '');

                // Shared string
                if ($type === 's') {
                    $value = $strings[(int) $value] ?? '';
                }

                // Inline string
                elseif ($type === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                }

                $value = trim($value);

                if ($value !== '') {
                    $hasValue = true;
                }

                $rowData[$colIndex] = $value;
            }

            /*
             * Row benar-benar kosong:
             * jangan dimasukkan ke array.
             */
            if (!$hasValue) {
                continue;
            }

            /*
             * Pastikan selalu mempunyai 7 kolom.
             */
            $filled = [];

            for ($i = 0; $i < 7; $i++) {
                $filled[$i] = $rowData[$i] ?? '';
            }

            $data[] = $filled;
        }

        return $data;
    }

    /**
     * Convert an Excel column letter (A, B, ..., Z, AA, AB, ...) to a
     * zero-based numeric index (A=0, B=1, ..., Z=25, AA=26, ...).
     */
    private function columnLetterToIndex(string $letters): int
    {
        $index = 0;
        foreach (str_split(strtoupper($letters)) as $char) {
            $index = $index * 26 + (ord($char) - ord('A') + 1);
        }

        return $index - 1;
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
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