<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            // Добавляем новое поле 'type' со значением по умолчанию 'text'
            $table->enum('type', ['text', 'handwriting', 'voice'])->default('text')->after('content');
        });

        // Опционально: Обновляем существующие рукописные заметки,
        // если они уже есть в базе данных (используя старую логику)
        // Это важно для миграции старых данных.
        \DB::table('notes')->where('content', 'like', '{"objects":%')
            ->update(['type' => 'handwriting']);
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
