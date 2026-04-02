<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Note extends Model
{
    protected $fillable = [
        'track_id',
        'title',
        'content',
        'type',
        'preview_path'
    ];

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    /**
     * Возвращает первую строку контента для превью.
     * Поддерживает оба формата: старый (plain HTML) и новый (JSON массив страниц).
     */
    public function getFirstLine()
    {
        Log::info('Note content for getFirstLine', ['content' => substr($this->content, 0, 100), 'type' => $this->type]);

        if ($this->type === 'voice') {
            return '🎙️ Голосовая заметка ' . $this->created_at->format('d.m.Y');
        }

        if ($this->type === 'handwriting') {
            return '✍️ Рукописная заметка';
        }

        // Определяем формат контента
        $html = $this->getFirstPageHtml();

        // Извлекаем первый текстовый блок
        preg_match('/<(p|h[1-6])(?:\s+[^>]*)?>(.*?)<\/(p|h[1-6])>/i', $html, $matches);

        if (isset($matches[2])) {
            $firstLine = strip_tags($matches[2]);
        } else {
            $firstLine = 'Без названия';
        }

        return strlen($firstLine) > 100 ? substr($firstLine, 0, 100) . '...' : $firstLine;
    }

    /**
     * Возвращает HTML первой страницы.
     * Если контент — JSON массив страниц, берём первую.
     * Если старый формат (plain HTML) — возвращаем как есть.
     */
    public function getFirstPageHtml(): string
    {
        $content = $this->content ?? '';

        try {
            $parsed = json_decode($content, true);
            if (is_array($parsed) && isset($parsed[0]['html'])) {
                return $parsed[0]['html'];
            }
        } catch (\Exception $e) {
            // не JSON — возвращаем как есть
        }

        return $content;
    }

    /**
     * Возвращает все страницы как массив.
     * Для старых заметок оборачивает контент в массив из одной страницы.
     */
    public function getPages(): array
    {
        $content = $this->content ?? '';

        try {
            $parsed = json_decode($content, true);
            if (is_array($parsed) && isset($parsed[0]['html'])) {
                return $parsed;
            }
        } catch (\Exception $e) {
            // не JSON
        }

        // Старый формат — одна страница
        return [['html' => $content]];
    }

    /**
     * Количество страниц в заметке.
     */
    public function getPagesCount(): int
    {
        return count($this->getPages());
    }
}
