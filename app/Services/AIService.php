<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl = 'https://openrouter.ai/api/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->model  = config('services.openrouter.model', 'meta-llama/llama-3.3-70b-instruct:free');
    }

    // ─────────────────────────────────────────────
    // Общий метод отправки запроса к OpenRouter
    // ─────────────────────────────────────────────
    protected function sendRequest(string $prompt): array
    {
        set_time_limit(120);
        try {
            Log::info('Sending request to OpenRouter', ['model' => $this->model]);

            $response = Http::timeout(90)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                    'HTTP-Referer'  => config('app.url', 'http://localhost'),
                    'X-Title'       => config('app.name', 'Laravel App'),
                ])
                ->post("{$this->baseUrl}/chat/completions", [
                    'model'    => $this->model,
                    'messages' => [
                        [
                            'role'    => 'user',
                            'content' => $prompt,
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens'  => 4096,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                $generatedText = $data['choices'][0]['message']['content'] ?? '';

                if (empty($generatedText)) {
                    Log::error('OpenRouter returned empty text', ['data' => $data]);
                    return ['success' => false, 'error' => 'API вернул пустой ответ.'];
                }

                Log::info('OpenRouter response received', ['text_length' => strlen($generatedText)]);
                return ['success' => true, 'text' => $generatedText];
            }

            Log::error('OpenRouter API failed', [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
            return ['success' => false, 'error' => 'API request failed: ' . $response->body()];

        } catch (\Exception $e) {
            Log::error('OpenRouter API Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // 1. Генерация Q&A упражнения
    // ─────────────────────────────────────────────
    public function generateExerciseFromNote(string $noteContent): array
    {
        $prompt = $this->buildQAPrompt($noteContent);
        $result = $this->sendRequest($prompt);

        if (!$result['success']) {
            return $result;
        }

        return [
            'success'  => true,
            'exercise' => $this->parseJSON($result['text']),
            'raw'      => $result['text'],
        ];
    }

    // ─────────────────────────────────────────────
    // 2. Генерация одной полноценной задачи
    // ─────────────────────────────────────────────
    public function generateTaskFromNote(string $noteContent): array
    {
        $prompt = $this->buildTaskPrompt($noteContent);
        $result = $this->sendRequest($prompt);

        if (!$result['success']) {
            return $result;
        }

        $parsed = $this->parseJSON($result['text']);

        if (!$parsed || !isset($parsed['task'])) {
            Log::error('Task parse failed', ['raw' => $result['text']]);
            return ['success' => false, 'error' => 'Не удалось разобрать ответ AI.'];
        }

        return [
            'success' => true,
            'task'    => $parsed,
            'raw'     => $result['text'],
        ];
    }

    // ─────────────────────────────────────────────
    // 3. Проверка решения пользователя
    // ─────────────────────────────────────────────
    public function checkSolution(string $taskTitle, string $taskDescription, string $userSolution): array
    {
        $prompt = $this->buildCheckPrompt($taskTitle, $taskDescription, $userSolution);
        $result = $this->sendRequest($prompt);

        if (!$result['success']) {
            return $result;
        }

        $parsed = $this->parseJSON($result['text']);

        if (!$parsed || !isset($parsed['score'])) {
            Log::error('Check solution parse failed', ['raw' => $result['text']]);
            return ['success' => false, 'error' => 'Не удалось разобрать ответ AI.'];
        }

        return [
            'success'  => true,
            'feedback' => $parsed,
            'raw'      => $result['text'],
        ];
    }

    // ─────────────────────────────────────────────
    // Промпты
    // ─────────────────────────────────────────────

    protected function buildQAPrompt(string $noteContent): string
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
- НЕ добавляй markdown разметку (\`\`\`json)
- Верни ТОЛЬКО JSON объект

Заметка:
{$noteContent}";
    }

    protected function buildTaskPrompt(string $noteContent): string
    {
        return "На основе следующей заметки придумай одну полноценную практическую задачу для закрепления темы заметки. Сама задача может стилистически быть связана с другими темами, но не должна требовать от решающего знаний в областях, не упомянутых в заметке.

Формат ответа должен быть СТРОГО JSON без markdown разметки:
{
  \"title\": \"Название задачи\",
  \"task\": \"Полное условие задачи. Должно быть подробным: объясни контекст, что нужно сделать, какие шаги предпринять. Минимум 3-5 предложений.\",
  \"hints\": [
    \"Подсказка 1 (необязательная помощь для решающего)\",
    \"Подсказка 2\"
  ],
  \"expected_aspects\": [
    \"Ключевой аспект 1, который должен быть в правильном решении\",
    \"Ключевой аспект 2\"
  ]
}

Требования:
- Задача должна проверять ПОНИМАНИЕ темы, а не просто воспроизведение фактов
- Условие задачи должно быть чётким и однозначным
- expected_aspects — это внутренние критерии для AI-проверки (не показываются пользователю)
- НЕ добавляй markdown разметку
- Верни ТОЛЬКО JSON объект

Заметка:
{$noteContent}";
    }

    protected function buildCheckPrompt(string $taskTitle, string $taskDescription, string $userSolution): string
    {
        return "Ты — строгий, но справедливый преподаватель. Проверь решение студента.

Задача: {$taskTitle}
Условие: {$taskDescription}

Решение студента:
{$userSolution}

Оцени решение и верни СТРОГО JSON без markdown разметки:
{
  \"score\": 7,
  \"verdict\": \"Хорошо\",
  \"strengths\": [
    \"Что студент сделал правильно (конкретно)\"
  ],
  \"mistakes\": [
    \"Конкретная ошибка или упущение\"
  ],
  \"advice\": \"Краткий совет: что улучшить или изучить дополнительно\"
}

Требования:
- score: целое число от 0 до 10
- verdict: одно слово или короткая фраза (Отлично / Хорошо / Удовлетворительно / Слабо / Не зачтено)
- strengths: массив строк, минимум 1 пункт (если совсем нет плюсов — напиши \"Попытка засчитана\")
- mistakes: массив строк, пустой массив [] если ошибок нет
- advice: одно предложение
- НЕ добавляй markdown разметку
- Верни ТОЛЬКО JSON объект";
    }

    // ─────────────────────────────────────────────
    // Парсинг JSON из ответа
    // ─────────────────────────────────────────────
    protected function parseJSON(string $text): ?array
    {
        $text = preg_replace('/```json\s*/i', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $text = $matches[0];
        }

        // Убираем управляющие символы кроме \n, \r, \t
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);

        try {
            $parsed = json_decode($text, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON parse error', [
                    'error' => json_last_error_msg(),
                    'text'  => substr($text, 0, 500),
                ]);
                return null;
            }

            return $parsed;
        } catch (\Exception $e) {
            Log::error('Failed to parse JSON: ' . $e->getMessage());
            return null;
        }
    }
}
