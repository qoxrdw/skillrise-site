<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Добавить этот импорт, если его нет

return new class extends Migration
{
    public function up(): void
    {
        // Используем DB::statement, чтобы обойти ограничения Schema Builder на изменение ENUM
        DB::statement("ALTER TABLE notes CHANGE COLUMN type type ENUM('text', 'handwriting', 'voice') NOT NULL DEFAULT 'text'");
    }

    public function down(): void
    {
        // Откат: удаляем 'voice'
        DB::statement("ALTER TABLE notes CHANGE COLUMN type type ENUM('text', 'handwriting') NOT NULL DEFAULT 'text'");
    }
};
