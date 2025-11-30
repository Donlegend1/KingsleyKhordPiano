<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreQuizRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class EarTrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the ear training page.
     *
     * @return \Illuminate\View\View
     */
    public function earTraining(Request $request) {
        $query = Quiz::query();
        if ($request->filled('name')) {
            $query->where('title', 'like', '%' . $request->input('name') . '%');
        }
        $data = $query->paginate(12)->withQueryString();
        return view('memberpages.eartraining', compact('data'));
    }

    public function create() {

        return view('admin.eartraining.create');
    }

    public function index() {
       $quizzes = Quiz::with('questions')->get();
        return view('admin.eartraining.index', compact('quizzes'));
    }

    public function showmember($id) {
        $quiz = Quiz::with('questions')->findOrFail($id);
        return view('memberpages.eartraining.show', compact('quiz'));
    }

    public  function showadmin() {
        return view('admin.eartraining.show');
    }

    public function show($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        return response()->json($quiz);
    }

    public function list()
    {
        $quiz = Quiz::with('questions')->paginate(10);
        return response()->json($quiz);
    }

    public function store(StoreQuizRequest $request)
    {
        try {
            $thumbnail = $request->file('thumbnail');
            $mainAudio = $request->file('main_audio');

            // Process thumbnail
            $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
            $thumbnail->move(
                base_path('../public_html/ear_training/thumbnails'),
                $thumbnailName
            );

            // Process main audio only if provided
            $mainAudioName = null;
            if ($mainAudio) {
                $mainAudioName = time() . '_' . $mainAudio->getClientOriginalName();
                $mainAudio->move(
                    base_path('../public_html/ear_training/main_audios'),
                    $mainAudioName
                );
            }

            // Create quiz record
            $quiz = Quiz::create([
                'title'           => $request->title,
                'description'     => $request->description,
                'video_url'       => $request->video_url,
                'thumbnail_path'  => "/ear_training/thumbnails/$thumbnailName",
                'main_audio_path' => $mainAudioName 
                                        ? "/ear_training/main_audios/$mainAudioName"
                                        : null,
                'category'        => $request->category,
            ]);

            // Process questions
            foreach ($request->questions as $q) {
                $audio = $q['audio'];
                $audioName = time() . '_' . $audio->getClientOriginalName();

                $audio->move(
                    base_path('../public_html/ear_training/question_audios'),
                    $audioName
                );

                $quiz->questions()->create([
                    'audio_path'     => "/ear_training/question_audios/$audioName",
                    'correct_option' => $q['correct_option'],
                ]);
            }

            return response()->json([
                'message' => 'Quiz created successfully.',
                'quiz'    => $quiz->load('questions'),
            ], 201);

        } catch (\Throwable $e) {
            \Log::error('Quiz creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error'   => 'Failed to create quiz.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($request->has('title')) {
            $quiz->title = $request->title;
        }
    
        if ($request->has('description')) {
            $quiz->description = $request->description;
        }
    
        if ($request->has('video_url')) {
            $quiz->video_url = $request->video_url;
        }
    
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . '_' . $thumbnail->getClientOriginalName();
    
            $destination = base_path('../public_html/ear_training/thumbnails');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
    
            $thumbnail->move($destination, $thumbnailName);
    
            $quiz->thumbnail_path = "/ear_training/thumbnails/$thumbnailName";
        }
    
        if ($request->hasFile('main_audio')) {
            $mainAudio = $request->file('main_audio');
            $mainAudioName = time() . '_' . $mainAudio->getClientOriginalName();
    
            $destination = base_path('../public_html/ear_training/main_audios');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
    
            $mainAudio->move($destination, $mainAudioName);
    
            $quiz->main_audio_path = "/ear_training/main_audios/$mainAudioName";
        }
    
        $quiz->save();
    
        return response()->json([
            'message' => 'Quiz updated successfully.',
            'quiz'    => $quiz->load('questions'),
        ]);
    }


    public function destroy(Quiz $quiz)
    {

        $quiz->questions()->delete();

        $quiz->delete();

        return response()->json(['message' => 'Quiz and its questions deleted successfully.']);
    }

    public function deleteQuestion(QuizQuestion $question)
    {

    $question->delete();
    return response()->json(['message' => 'questions deleted successfully.']);
    }

  public function storeQuestions(Request $request, Quiz $quiz)
{
    $validated = $request->validate([
        'questions' => 'required|array|min:1',
        'questions.*.audio' => 'required|file|mimes:mp3,wav,ogg',
        'questions.*.correct_option' => 'required|integer|min:0',
    ]);

    foreach ($validated['questions'] as $q) {
        $audio = $q['audio'];
        $audioName = time() . '_' . $audio->getClientOriginalName();

        // Destination inside public_html
        $destination = base_path('../public_html/ear_training/question_audios');
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        // Move file
        $audio->move($destination, $audioName);

        QuizQuestion::create([
            'quiz_id'        => $quiz->id,
            'audio_path'     => "/ear_training/question_audios/$audioName",
            'correct_option' => $q['correct_option'],
        ]);
    }

    return response()->json([
        'message' => 'Questions added successfully.',
    ], 201);
}
}
