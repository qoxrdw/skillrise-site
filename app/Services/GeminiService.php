<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Генерирует упражнение на основе заметки
     */
    public function generateExerciseFromNote(string $noteContent): array
    {
        $prompt = $this->buildPrompt($noteContent);
        $modelName = 'gemini-2.5-flash';

        try {
            $response = Http::timeout(60)
                // Используем переменную $modelName
                ->post("{$this->baseUrl}/models/{$modelName}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();

                // Проверка на наличие кандидатов (важно для фильтров безопасности)
                if (!isset($data['candidates']) || empty($data['candidates'])) {
                    Log::error('Gemini successful response but no candidates returned.', ['data' => $data]);
                    return [
                        'success' => false,
                        'error' => 'API вернул успешный ответ, но без сгенерированного контента (возможна блокировка по безопасности).'
                    ];
                }

                $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                Log::info('Gemini response', ['text' => $generatedText]);

                return [
                    'success' => true,
                    'exercise' => $this->parseExercise($generatedText),
                    'raw' => $generatedText
                ];
            }

            Log::error('Gemini API failed', ['response' => $response->body()]);

            return [
                'success' => false,
                'error' => 'API request failed: ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }


    /**
     * Формирует промпт для генерации упражнения
     */
    protected function buildPrompt(string $noteContent): string
    {
        return "На основе следующей заметки создай упражнение из 5-8 вопросов с ответами для самопроверки.

Формат ответа должен быть СТРОГО JSON без markdown разметки:
{
  \"title\": \"Название упражнения\",
  \"questions\": [
    {
      \"question\": \"Текст вопроса\",
      \"answer\": \"Правильный ответ\"
    }
  ]
}

Требования:
- Вопросы должны быть разнообразными и охватывать ключевые моменты заметки
- Ответы должны быть краткими и точными
- НЕ добавляй markdown разметку (```json)
- Верни ТОЛЬКО JSON объект

Заметка:
{$noteContent}";
    }

    /**
     * Парсит ответ от API
     */
    protected function parseExercise(string $text): ?array
    {
        // Убираем markdown форматирование если есть
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        try {
            $parsed = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON parse error', ['error' => json_last_error_msg(), 'text' => $text]);
                return null;
            }

            // Проверяем структуру
            if (!isset($parsed['questions']) || !is_array($parsed['questions'])) {
                Log::error('Invalid structure', ['parsed' => $parsed]);
                return null;
            }

            return $parsed;
        } catch (\Exception $e) {
            Log::error('Failed to parse exercise: ' . $e->getMessage());
            return null;
        }
    }
}
