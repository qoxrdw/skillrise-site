<?php
namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Track;
use App\Models\Note;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExercisesController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
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
        $notes = Note::where('track_id', $track->id)
            ->whereIn('type', ['text'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('exercises.create-ai', compact('track', 'notes'));
    }

    /**
     * Генерирует Q&A упражнение с помощью AI
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

        $noteContent = $this->extractNoteContent($note);
        if ($noteContent === null) {
            return back()->withErrors(['error' => 'Не удалось извлечь текст из заметки.']);
        }

        $result = $this->aiService->generateExerciseFromNote($noteContent);

        Log::info('AI Generation Result (QA)', ['track_id' => $track->id, 'note_id' => $note->id, 'result' => $result]);

        if (!isset($result['success']) || $result['success'] !== true || empty($result['exercise'])) {
            Log::error('AI Service failed or returned empty exercise data.', ['result' => $result]);
            return back()->withErrors(['error' => 'AI-сервис не смог сгенерировать упражнение. Проверьте логи.']);
        }

        $exerciseData = $result['exercise'];

        if (!isset($exerciseData['questions']) || !is_array($exerciseData['questions']) || count($exerciseData['questions']) === 0) {
            return back()->withErrors(['error' => 'AI сгенерировал ответ, но в нем отсутствуют вопросы.']);
        }

        $content = [];
        foreach ($exerciseData['questions'] as $item) {
            if (isset($item['question']) && isset($item['answer'])) {
                $content[] = [
                    'question' => $item['question'],
                    'answer'   => $item['answer'],
                ];
            }
        }

        if (empty($content)) {
            return back()->withErrors(['error' => 'AI сгенерировал вопросы, но без валидных ответов.']);
        }

        $exercise = $track->exercises()->create([
            'title'   => $exerciseData['title'] ?? ('Упражнение на основе: ' . $note->getFirstLine()),
            'content' => $content,
            'type'    => 'qa',
            'user_id' => Auth::id(),
        ]);

        Log::info('QA Exercise created via AI.', ['exercise_id' => $exercise->id]);

        return redirect()->route('tracks.show', $track)
            ->with('success', 'Упражнение успешно создано с помощью AI! 🤖');
    }

    /**
     * Генерирует одну задачу с AI-проверкой
     */
    public function generateTaskWithAI(Request $request, Track $track)
    {
        $request->validate([
            'note_id' => 'required|exists:notes,id'
        ]);

        $note = Note::findOrFail($request->note_id);

        if ($note->track_id !== $track->id) {
            abort(403);
        }

        $noteContent = $this->extractNoteContent($note);
        if ($noteContent === null) {
            return back()->withErrors(['error' => 'Не удалось извлечь текст из заметки.']);
        }

        $result = $this->aiService->generateTaskFromNote($noteContent);

        Log::info('AI Task Generation Result', ['track_id' => $track->id, 'note_id' => $note->id, 'result' => $result]);

        if (!$result['success'] || empty($result['task'])) {
            Log::error('AI Task generation failed.', ['result' => $result]);
            return back()->withErrors(['error' => 'AI не смог сгенерировать задачу. Попробуйте ещё раз.']);
        }

        $taskData = $result['task'];

        $exercise = $track->exercises()->create([
            'title'   => $taskData['title'] ?? ('Задача на основе: ' . $note->getFirstLine()),
            'content' => [
                'task'             => $taskData['task'],
                'hints'            => $taskData['hints'] ?? [],
                'expected_aspects' => $taskData['expected_aspects'] ?? [],
            ],
            'type'    => 'task',
            'user_id' => Auth::id(),
        ]);

        Log::info('Task Exercise created via AI.', ['exercise_id' => $exercise->id]);

        return redirect()->route('exercises.take-task', [$track, $exercise])
            ->with('success', 'Задача сгенерирована! Попробуйте её решить. 🧠');
    }

    /**
     * Страница прохождения задачи (type=task)
     */
    public function takeTask(Track $track, Exercise $exercise)
    {
        if ($exercise->track_id !== $track->id || $track->user_id !== Auth::id()) {
            abort(403);
        }

        if ($exercise->type !== 'task') {
            abort(404);
        }

        return view('exercises.take-task', compact('exercise', 'track'));
    }

    /**
     * Отправка решения задачи на проверку AI
     */
    public function submitTask(Request $request, Track $track, Exercise $exercise)
    {
        if ($exercise->track_id !== $track->id || $track->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'solution' => 'required|string|min:10',
        ], [
            'solution.required' => 'Напишите ваше решение перед отправкой.',
            'solution.min'      => 'Решение слишком короткое. Напишите хотя бы несколько предложений.',
        ]);

        $content         = $exercise->content;
        $taskDescription = $content['task'] ?? '';

        $result = $this->aiService->checkSolution(
            $exercise->title,
            $taskDescription,
            $request->input('solution')
        );

        Log::info('AI Solution Check Result', ['exercise_id' => $exercise->id, 'result' => $result]);

        if (!$result['success'] || empty($result['feedback'])) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'AI не смог проверить решение. Попробуйте ещё раз.']);
        }

        session()->flash('feedback', $result['feedback']);
        session()->flash('user_solution', $request->input('solution'));

        return redirect()->route('exercises.take-task', [$track, $exercise]);
    }

    // ─────────────────────────────────────────────
    // Стандартные методы (без изменений)
    // ─────────────────────────────────────────────

    public function store(Request $request, Track $track)
    {
        Log::info('Store request data', $request->all());

        $request->validate([
            'title'       => 'required|string|max:255',
            'questions'   => 'required|array',
            'questions.*' => 'required|string',
            'answers'     => 'required|array',
            'answers.*'   => 'required|string',
        ], [
            'questions.required'   => 'Необходимо добавить хотя бы один вопрос.',
            'questions.*.required' => 'Текст вопроса не может быть пустым.',
            'answers.*.required'   => 'Правильный ответ не может быть пустым.',
        ]);

        try {
            $content   = [];
            $questions = $request->input('questions', []);
            $answers   = $request->input('answers', []);

            foreach ($questions as $index => $question) {
                if (isset($answers[$index])) {
                    $content[] = [
                        'question' => $question,
                        'answer'   => $answers[$index],
                    ];
                }
            }

            $exercise = $track->exercises()->create([
                'title'    => $request->title,
                'content'  => $content,
                'type'     => 'qa',
                'user_id'  => Auth::id(),
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
            'answers'   => 'required|array',
            'answers.*' => 'required|string',
        ]);

        $content     = $exercise->content;
        $userAnswers = $request->input('answers', []);
        $results     = [];

        foreach ($content as $index => $item) {
            $isCorrect = isset($userAnswers[$index]) && $userAnswers[$index] === $item['answer'];
            $results[] = [
                'question'       => $item['question'],
                'correct_answer' => $item['answer'],
                'user_answer'    => $userAnswers[$index] ?? null,
                'is_correct'     => $isCorrect,
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

    // ─────────────────────────────────────────────
    // Вспомогательные методы
    // ─────────────────────────────────────────────

    private function extractNoteContent(Note $note): ?string
    {
        if ($note->type === 'handwriting') {
            $contentData = json_decode($note->content, true);
            $text        = $contentData['text'] ?? null;
            if (!$text) {
                Log::error('Handwriting note missing "text" key.', ['note_id' => $note->id]);
                return null;
            }
            return $text;
        }

        return $note->content;
    }
}
