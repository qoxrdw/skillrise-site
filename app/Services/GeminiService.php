<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $proxyUrl;
    protected $useProxy;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->proxyUrl = config('services.gemini.proxy_url');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

        // Определяем, использовать ли прокси (если URL прокси указан)
        $this->useProxy = !empty($this->proxyUrl);
    }

    /**
     * Генерирует упражнение на основе заметки
     */
    public function generateExerciseFromNote(string $noteContent): array
    {
        $prompt = $this->buildPrompt($noteContent);
        $modelName = 'gemini-2.5-flash'; // или 'gemini-2.5-flash' если доступен

        try {
            if ($this->useProxy) {
                // Используем Cloudflare Worker как прокси
                $url = $this->proxyUrl . '?key=' . $this->apiKey . '&model=' . $modelName;

                Log::info('Sending request to Gemini via Cloudflare Worker', [
                    'proxy_url' => $this->proxyUrl,
                    'model' => $modelName
                ]);
            } else {
                // Прямой запрос к Gemini API
                $url = "{$this->baseUrl}/models/{$modelName}:generateContent?key={$this->apiKey}";

                Log::info('Sending direct request to Gemini API', [
                    'model' => $modelName
                ]);
            }

            $response = Http::timeout(60)
                ->post($url, [
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

                if (empty($generatedText)) {
                    Log::error('Gemini returned empty text', ['data' => $data]);
                    return [
                        'success' => false,
                        'error' => 'API вернул пустой ответ.'
                    ];
                }

                Log::info('Gemini response received', ['text_length' => strlen($generatedText)]);

                return [
                    'success' => true,
                    'exercise' => $this->parseExercise($generatedText),
                    'raw' => $generatedText
                ];
            }

            Log::error('Gemini API failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => 'API request failed: ' . $response->body()
            ];

        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

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
