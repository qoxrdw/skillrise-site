<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Note extends Model
{
    protected $fillable = ['content', 'track_id', 'type'];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function getFirstLine()
    {
        // Логируем контент для отладки
        Log::info('Note content for getFirstLine', ['content' => $this->content, 'type' => $this->type]);

        // --- НОВОЕ ИЗМЕНЕНИЕ ДЛЯ ГОЛОСОВОЙ ЗАМЕТКИ ---
        if ($this->type === 'voice') {
            // Возвращаем стандартизированное название, включающее дату создания
            return '🎙️ Голосовая заметка ' . $this->created_at->format('d.m.Y');
        }
        // --- КОНЕЦ НОВОГО ИЗМЕНЕНИЯ ---

        // Логика для текстовых и рукописных заметок (остается прежней)

        // Ищем первый блочный тег: <p>, <h1>, <h2>, ..., <h6> с любыми атрибутами
        preg_match('/<(p|h[1-6])(?:\s+[^>]*)?>(.*?)<\/(p|h[1-6])>/i', $this->content, $matches);

        if (isset($matches[2])) {
            $firstLine = strip_tags($matches[2]);
            Log::info('First line extracted', ['firstLine' => $firstLine]);
        } elseif ($this->type === 'handwriting') {
            $firstLine = '✍️ Рукописная заметка';
            Log::info('Handwriting note title');
        } else {
            $firstLine = 'Без названия';
            Log::info('No matching tag found, returning default');
        }

        // Ограничиваем длину строки до 100 символов
        return strlen($firstLine) > 100 ? substr($firstLine, 0, 100) . '...' : $firstLine;
    }
}
