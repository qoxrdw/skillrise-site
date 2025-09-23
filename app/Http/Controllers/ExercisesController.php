<?php
namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExercisesController extends Controller
{
    public function index(Track $track)
    {
        // Убедитесь, что пользователь владеет этим треком
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        $exercises = $track->exercises()->where('user_id', Auth::id())->get();
        return view('exercises.index', compact('exercises', 'track'));
    }

    public function create(Track $track)
    {
        // Убедитесь, что пользователь владеет этим треком
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
        return view('exercises.create', compact('track'));
    }

    public function store(Request $request, Track $track)
    {
        // Убедитесь, что пользователь владеет этим треком
        if ($track->user_id !== Auth::id()) {
            abort(403);
        }
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
                'track_id' => $track->id, // Привязываем упражнение к треку
            ]);

            Log::info('Exercise created', ['exercise' => $exercise]);

            return redirect()->route('exercises.index', $track)->with('success', 'Упражнение успешно создано.');
        } catch (\Exception $e) {
            Log::error('Error creating exercise', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Произошла ошибка при создании упражнения.'])->withInput();
        }
    }

    public function take(Track $track, Exercise $exercise)
    {
        // Убедитесь, что упражнение принадлежит треку и пользователь владеет треком
        if ($exercise->track_id !== $track->id || $track->user_id !== Auth::id()) {
            abort(403);
        }
        return view('exercises.take', compact('exercise', 'track'));
    }

    public function submit(Request $request, Track $track, Exercise $exercise)
    {
        // Убедитесь, что упражнение принадлежит треку и пользователь владеет треком
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

        // Здесь можно сохранить результаты, например, в отдельной таблице или в сессии
        session()->flash('results', $results);

        return redirect()->route('exercises.index', $track)->with('success', 'Упражнение пройдено.');
    }

    public function destroy(Track $track, Exercise $exercise)
    {
        // Убедитесь, что упражнение принадлежит треку и пользователь владеет треком
        if ($exercise->track_id !== $track->id || $track->user_id !== Auth::id()) {
            abort(403);
        }
        $exercise->delete();
        return redirect()->route('exercises.index', $track)->with('success', 'Упражнение успешно удалено.');
    }
}
