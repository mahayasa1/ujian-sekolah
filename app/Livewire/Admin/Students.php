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
    /*
    |--------------------------------------------------------------------------
    | TEST LOGGING - START
    |--------------------------------------------------------------------------
    */
    \Log::info('================ IMPORT PREVIEW START ================');

    \Log::info('IMPORT PREVIEW - INITIAL STATE', [
        'memory' => memory_get_usage(true),
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory' => memory_get_peak_usage(true),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        'importPreview_count_before' => count($this->importPreview ?? []),
    ]);

    /*
    |--------------------------------------------------------------------------
    | VALIDASI FILE
    |--------------------------------------------------------------------------
    */
    $this->validate([
        'importFile' => 'required|file|mimes:csv,txt,xlsx|max:2048',
    ]);

    \Log::info('IMPORT PREVIEW - VALIDATION PASSED', [
        'memory' => memory_get_usage(true),
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    /*
    |--------------------------------------------------------------------------
    | RESET STATE
    |--------------------------------------------------------------------------
    */
    $this->importMsg     = '';
    $this->importError   = false;
    $this->importPreview = [];

    /*
    |--------------------------------------------------------------------------
    | FILE INFORMATION
    |--------------------------------------------------------------------------
    */
    $path = $this->importFile->getRealPath();
    $ext  = strtolower($this->importFile->getClientOriginalExtension());

    \Log::info('IMPORT PREVIEW - FILE INFO', [
        'path_exists' => $path ? file_exists($path) : false,
        'file_size_bytes' => $path && file_exists($path)
            ? filesize($path)
            : null,
        'file_size_kb' => $path && file_exists($path)
            ? round(filesize($path) / 1024, 2)
            : null,
        'file_size_mb' => $path && file_exists($path)
            ? round(filesize($path) / 1024 / 1024, 2)
            : null,
        'extension' => $ext,
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    $preview = [];

    /*
    |--------------------------------------------------------------------------
    | XLSX
    |--------------------------------------------------------------------------
    */
    if ($ext === 'xlsx') {

        \Log::info('IMPORT PREVIEW - BEFORE readXlsx()', [
            'memory' => memory_get_usage(true),
            'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_memory' => memory_get_peak_usage(true),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ]);

        /*
        |--------------------------------------------------------------------------
        | READ XLSX
        |--------------------------------------------------------------------------
        */
        $rows = $this->readXlsx($path);

        \Log::info('IMPORT PREVIEW - AFTER readXlsx()', [
            'rows_count' => is_countable($rows)
                ? count($rows)
                : null,
            'rows_type' => get_debug_type($rows),
            'memory' => memory_get_usage(true),
            'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_memory' => memory_get_peak_usage(true),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ]);

        /*
        |--------------------------------------------------------------------------
        | PROCESS XLSX ROWS
        |--------------------------------------------------------------------------
        */
        $rowNumber = 0;

        foreach ($rows as $r) {

            $rowNumber++;

            $name = trim(
                $r['nama']
                ?? $r['name']
                ?? ''
            );

            $email = strtolower(
                trim($r['email'] ?? '')
            );

            /*
            |--------------------------------------------------------------------------
            | Skip empty / incomplete row
            |--------------------------------------------------------------------------
            */
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

        \Log::info('IMPORT PREVIEW - AFTER XLSX LOOP', [
            'source_rows' => $rowNumber,
            'preview_rows' => count($preview),
            'memory' => memory_get_usage(true),
            'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_memory' => memory_get_peak_usage(true),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ]);

    /*
    |--------------------------------------------------------------------------
    | CSV / TXT
    |--------------------------------------------------------------------------
    */
    } else {

        \Log::info('IMPORT PREVIEW - CSV/TXT MODE', [
            'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
        ]);

        if (($handle = fopen($path, 'r')) !== false) {

            $header = null;
            $csvRowNumber = 0;

            while (($line = fgetcsv($handle, 1000, ',')) !== false) {

                $csvRowNumber++;

                /*
                |--------------------------------------------------------------------------
                | Skip empty row
                |--------------------------------------------------------------------------
                */
                if (
                    count(
                        array_filter(
                            $line,
                            fn ($v) => trim((string) $v) !== ''
                        )
                    ) === 0
                ) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Header
                |--------------------------------------------------------------------------
                */
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

            \Log::info('IMPORT PREVIEW - AFTER CSV LOOP', [
                'source_rows' => $csvRowNumber,
                'preview_rows' => count($preview),
                'memory' => memory_get_usage(true),
                'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                'peak_memory' => memory_get_peak_usage(true),
                'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HASIL PREVIEW
    |--------------------------------------------------------------------------
    */
    \Log::info('IMPORT PREVIEW - BEFORE EMPTY CHECK', [
        'preview_rows' => count($preview),
        'memory' => memory_get_usage(true),
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory' => memory_get_peak_usage(true),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    /*
    |--------------------------------------------------------------------------
    | EMPTY RESULT
    |--------------------------------------------------------------------------
    */
    if (empty($preview)) {

        \Log::warning('IMPORT PREVIEW - EMPTY RESULT');

        $this->importError = true;

        $this->importMsg =
            'Tidak ada baris valid ditemukan. Pastikan kolom nama/name dan email tersedia.';

        \Log::info('================ IMPORT PREVIEW END - EMPTY ================');

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | LOG PREVIEW SIZE BEFORE LIVEWIRE STATE
    |--------------------------------------------------------------------------
    */
    $previewSerialized = serialize($preview);

    \Log::info('IMPORT PREVIEW - SERIALIZED PREVIEW SIZE', [
        'rows' => count($preview),
        'bytes' => strlen($previewSerialized),
        'kb' => round(strlen($previewSerialized) / 1024, 2),
        'mb' => round(strlen($previewSerialized) / 1024 / 1024, 4),
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    /*
    |--------------------------------------------------------------------------
    | ASSIGN TO LIVEWIRE PROPERTY
    |--------------------------------------------------------------------------
    */
    \Log::info('IMPORT PREVIEW - BEFORE STATE ASSIGNMENT', [
        'preview_rows' => count($preview),
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    $this->importPreview = array_values($preview);

    /*
    |--------------------------------------------------------------------------
    | AFTER LIVEWIRE STATE ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    \Log::info('IMPORT PREVIEW - AFTER STATE ASSIGNMENT', [
        'importPreview_rows' => count($this->importPreview),
        'memory' => memory_get_usage(true),
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory' => memory_get_peak_usage(true),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    /*
    |--------------------------------------------------------------------------
    | SERIALIZE LIVEWIRE PROPERTY
    |--------------------------------------------------------------------------
    */
    $livewireStateSerialized = serialize($this->importPreview);

    \Log::info('IMPORT PREVIEW - LIVEWIRE STATE SIZE', [
        'rows' => count($this->importPreview),
        'bytes' => strlen($livewireStateSerialized),
        'kb' => round(strlen($livewireStateSerialized) / 1024, 2),
        'mb' => round(strlen($livewireStateSerialized) / 1024 / 1024, 4),
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    unset($previewSerialized, $livewireStateSerialized);

    /*
    |--------------------------------------------------------------------------
    | FINAL MESSAGE
    |--------------------------------------------------------------------------
    */
    $count = count($this->importPreview);

    $this->importMsg =
        "{$count} baris siap diimpor. Cek data lalu klik Konfirmasi Import.";

    /*
    |--------------------------------------------------------------------------
    | FINAL LOG
    |--------------------------------------------------------------------------
    */
    \Log::info('IMPORT PREVIEW - METHOD FINISHED', [
        'count' => $count,
        'message' => $this->importMsg,
        'memory' => memory_get_usage(true),
        'memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
        'peak_memory' => memory_get_peak_usage(true),
        'peak_memory_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    ]);

    \Log::info('================ IMPORT PREVIEW END ================');
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
    $importData = session('student_import_data', []);

    if (
        empty($importData) ||
        $this->importIndex >= $this->importTotal
    ) {
        $this->finishImport();

        return;
    }

    $batch = array_slice(
        $importData,
        $this->importIndex,
        $this->importBatchSize
    );

    DB::transaction(function () use ($batch) {

        $classRooms = ClassRoom::pluck('id', 'name');

        $emails = collect($batch)
            ->pluck('email')
            ->filter()
            ->map(fn ($email) => strtolower(trim($email)))
            ->unique()
            ->values();

        $existingUsers = User::whereIn('email', $emails)
            ->get()
            ->keyBy(fn ($user) => strtolower($user->email));

        foreach ($batch as $row) {

            if (
                empty($row['name']) ||
                empty($row['email'])
            ) {
                continue;
            }

            $email = strtolower(trim($row['email']));

            $classRoomId = null;

            if (!empty($row['kelas'])) {
                $classRoomId = $classRooms[$row['kelas']] ?? null;
            }

            $user = $existingUsers->get($email);

            if ($user) {

                $user->update([
                    'name' => $row['name'],
                    'role' => 'siswa',
                ]);

                $this->importUpdated++;

            } else {

                $password = $row['password'] ?: 'password';

                $user = User::create([
                    'name'     => $row['name'],
                    'email'    => $email,
                    'password' => Hash::make($password),
                    'role'     => 'siswa',
                ]);

                $this->importInserted++;
            }

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nis'           => $row['nis'] ?: null,
                    'nisn'          => $row['nisn'] ?: null,
                    'class_room_id' => $classRoomId,
                ]
            );
        }
    });

    $this->importIndex += count($batch);

    if ($this->importIndex >= $this->importTotal) {

        $this->finishImport();

    } else {

        $this->dispatch('import-batch-done');
    }
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