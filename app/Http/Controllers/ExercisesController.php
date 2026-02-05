<?php
namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Track;
use App\Models\Note;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExercisesController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index(Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        $exercises = $track->exercises()->where('user_id', Auth::id())->get();
        return view('exercises.index', compact('exercises', 'track'));
    }

    public function create(Track $track)
    {
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        return view('exercises.create', compact('track'));
    }

    /**
     * Показывает форму выбора заметки для AI генерации
     */
    public function createWithAI(Track $track)
    {

        // Получаем только текстовые и рукописные заметки (не голосовые)
        $notes = Note::where('track_id', $track->id)
            ->whereIn('type', ['text'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('exercises.create-ai', compact('track', 'notes'));
    }

    /**
     * Генерирует упражнение с помощью AI
     */
    public function generateWithAI(Request $request, Track $track)
    {
        $request->validate([
            'note_id' => 'required|exists:notes,id'
        ]);

        $note = Note::findOrFail($request->note_id);

        if ($note->track_id !== $track->id) {
            abort(403);
        }

        $noteContent = $note->content;

        // Для рукописных заметок контент - это JSON с текстом
        if ($note->type === 'handwriting') {
            $contentData = json_decode($note->content, true);
            // 🔥 ПРОВЕРКА: Если JSON пуст или не содержит ключа 'text'
            $noteContent = $contentData['text'] ?? 'Текст не распознан';
            if ($noteContent === 'Текст не распознан') {
                Log::error('Handwriting note content is invalid or missing "text" key.', ['note_id' => $note->id, 'content' => $note->content]);
                return back()->withErrors(['error' => 'Не удалось извлечь текст из рукописной заметки.']);
            }
        }

        // Генерируем упражнение через Gemini
        $result = $this->geminiService->generateExerciseFromNote($noteContent);

        Log::info('Gemini Generation Result (Parsed)', ['track_id' => $track->id, 'note_id' => $note->id, 'result' => $result]);

        // 1. Проверка общего успеха и наличия данных
        if (!isset($result['success']) || $result['success'] !== true || empty($result['exercise'])) {
            Log::error('AI Service failed or returned empty exercise data.', ['result' => $result]);
            return back()->withErrors(['error' => 'AI-сервис не смог сгенерировать упражнение или вернул ошибку. Проверьте логи сервиса.']);
        }

        $exerciseData = $result['exercise'];

        // 2. Дополнительная проверка структуры AI-ответа
        if (!isset($exerciseData['questions']) || !is_array($exerciseData['questions']) || count($exerciseData['questions']) === 0) {
            Log::error('AI Service returned success=true, but missing or empty "questions" structure.', ['response_data' => $exerciseData]);
            return back()->withErrors(['error' => 'AI сгенерировал ответ, но в нем отсутствуют вопросы для упражнения.']);
        }

        // Формируем content в нужном формате
        $content = [];
        foreach ($exerciseData['questions'] as $item) {
            // 3. Проверка наличия обязательных ключей для каждого вопроса
            if (isset($item['question']) && isset($item['answer'])) {
                $content[] = [
                    'question' => $item['question'],
                    'answer' => $item['answer'],
                ];
            } else {
                Log::warning('AI question item missing required keys (question or answer). Skipping item.', ['item' => $item]);
            }
        }

        // Проверяем, что после обработки остались валидные вопросы
        if (empty($content)) {
            Log::error('Exercise content is empty after validating questions and answers.', ['response_data' => $exerciseData]);
            return back()->withErrors(['error' => 'AI сгенерировал вопросы, но не смог предоставить валидные ответы. Упражнение пусто.']);
        }


        $exercise = $track->exercises()->create([
            // Используем оператор объединения ?? для безопасного получения заголовка
            'title' => $exerciseData['title'] ?? ('Упражнение на основе: ' . $note->getFirstLine()),
            'content' => $content,
            'user_id' => Auth::id(),
        ]);

        Log::info('Exercise successfully created via AI.', ['exercise_id' => $exercise->id]);


        return redirect()->route('tracks.show', $track)
            ->with('success', 'Упражнение успешно создано с помощью AI! 🤖');
    }

    public function store(Request $request, Track $track)
    {

        Log::info('Store request data', $request->all());

        $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'required|array',
            'questions.*' => 'required|string',
            'answers' => 'required|array',
            'answers.*' => 'required|string',
        ], [
            'questions.required' => 'Необходимо добавить хотя бы один вопрос.',
            'questions.*.required' => 'Текст вопроса не может быть пустым.',
            'answers.*.required' => 'Правильный ответ не может быть пустым.',
        ]);

        try {
            $content = [];
            $questions = $request->input('questions', []);
            $answers = $request->input('answers', []);

            Log::info('Questions and answers', ['questions' => $questions, 'answers' => $answers]);

            foreach ($questions as $index => $question) {
                if (isset($answers[$index])) {
                    $content[] = [
                        'question' => $question,
                        'answer' => $answers[$index],
                    ];
                }
            }

            $exercise = $track->exercises()->create([
                'title' => $request->title,
                'content' => $content,
                'user_id' => Auth::id(),
                'track_id' => $track->id,
            ]);

            Log::info('Exercise created', ['exercise' => $exercise]);

            return redirect()->route('tracks.show', $track)->with('success', 'Упражнение успешно создано.');
        } catch (\Exception $e) {
            Log::error('Error creating exercise', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Произошла ошибка при создании упражнения.'])->withInput();
        }
    }

    public function take(Track $track, Exercise $exercise)
    {
        if ($exercise->track_id !== $track->id || $track->user_id !== Auth::id()) {
            abort(403);
        }
        return view('exercises.take', compact('exercise', 'track'));
    }

    public function submit(Request $request, Track $track, Exercise $exercise)
    {
        if ($exercise->track_id !== $track->id || $track->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string',
        ]);

        $content = $exercise->content;
        $userAnswers = $request->input('answers', []);
        $results = [];

        foreach ($content as $index => $item) {
            $isCorrect = isset($userAnswers[$index]) && $userAnswers[$index] === $item['answer'];
            $results[] = [
                'question' => $item['question'],
                'correct_answer' => $item['answer'],
                'user_answer' => $userAnswers[$index] ?? null,
                'is_correct' => $isCorrect,
            ];
        }

        session()->flash('results', $results);

        return redirect()->route('exercises.index', $track)->with('success', 'Упражнение пройдено.');
    }

    public function destroy(Track $track, Exercise $exercise)
    {
        if ($exercise->track_id !== $track->id || $track->user_id !== Auth::id()) {
            abort(403);
        }
        $exercise->delete();
        return redirect()->route('tracks.show', $track)->with('success', 'Упражнение успешно удалено.');
    }
}
