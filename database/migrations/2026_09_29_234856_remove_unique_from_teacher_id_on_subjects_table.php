<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['teacher_id']);

            // Hapus unique index
            $table->dropUnique('subjects_teacher_id_unique');

            // Buat kembali foreign key tanpa unique
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Hapus foreign key
            $table->dropForeign(['teacher_id']);

            // Kembalikan unique index
            $table->unique('teacher_id');

            // Buat kembali foreign key
            $table->foreign('teacher_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }
};