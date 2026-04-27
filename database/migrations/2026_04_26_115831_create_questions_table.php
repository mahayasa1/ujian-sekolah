<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('title');                        // Judul soal / nama set soal
            $table->string('google_form_url');              // Link Google Form (untuk siswa)
            $table->string('google_form_edit_url')->nullable(); // Link edit Google Form (untuk guru)
            $table->string('google_sheet_url')->nullable(); // Link spreadsheet hasil
            $table->text('description')->nullable();        // Deskripsi / petunjuk soal
            $table->integer('duration')->default(60);       // Durasi default (menit)
            $table->date('exam_date')->nullable();          // Tanggal ujian (opsional)
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};