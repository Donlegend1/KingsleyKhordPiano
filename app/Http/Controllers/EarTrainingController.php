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
    function earTraining() {
        $data = Quiz::all();
        return view('memberpages.eartraining', compact('data'));
    }

    function create() {

        return view('admin.eartraining.create');
    }

    function index() {
       $quizzes = Quiz::with('questions')->get();
        return view('admin.eartraining.index', compact('quizzes'));
    }

    function showmember($id) {
        $quiz = Quiz::with('questions')->findOrFail($id);
        return view('memberpages.eartraining.show', compact('quiz'));
    }
     function showadmin() {
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
        
       $thumbnailPath = $request->file('thumbnail')->store('ear_training/thumbnails', 'public');
        $mainAudioPath = $request->file('main_audio')->store('ear_training/main_audios', 'public');

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'video_url' => $request->video_url,
            'thumbnail_path' => $thumbnailPath,
            'main_audio_path' => $mainAudioPath,
            'category' => $request->category,
        ]);

        foreach ($request->questions as $q) {
            $questionAudioPath = $q['audio']->store('ear_training/question_audios', 'public');

            $quiz->questions()->create([
                'audio_path' => $questionAudioPath,
                'correct_option' => $q['correct_option'],
            ]);
        }

        return response()->json([
            'message' => 'Ear training upload saved successfully.',
            'quiz' => $quiz->load('questions'),
        ], 201);
    }

    public function update(Request $request, Quiz $quiz)
    {
        // Update fields only if present
        if ($request->has('title')) {
            $quiz->title = $request->input('title');
        }

        if ($request->has('description')) {
            $quiz->description = $request->input('description');
        }

        if ($request->has('category')) {
            $quiz->category = $request->input('category');
        }

        if ($request->has('video_url')) {
            $quiz->video_url = $request->input('video_url');
        }

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('ear_training/thumbnails', 'public');
            $quiz->thumbnail_path = $thumbnailPath;
        }

        if ($request->hasFile('main_audio')) {
            $mainAudioPath = $request->file('main_audio')->store('ear_training/main_audios', 'public');
            $quiz->main_audio_path = $mainAudioPath;
        }

        $quiz->save();

        $questionsInput = $request->input('questions', []);  
        $questionsFiles = $request->file('questions', []);

        if (!empty($questionsInput)) {
            foreach ($questionsInput as $index => $q) {
                $correctOption = $q['correct_option'] ?? null;

                $audioFile = $questionsFiles[$index]['audio'] ?? null;

                $audioPath = null;
                if ($audioFile && $audioFile->isValid()) {
                    $audioPath = $audioFile->store('ear_training/question_audios', 'public');
                }

                $quiz->questions()->create([
                    'audio_path' => $audioPath,  
                    'correct_option' => $correctOption,
                ]);
            }
        }

        return response()->json([
            'message' => 'Quiz updated successfully.',
            'quiz' => $quiz->load('questions'),
        ]);
    }

   public function destroy(Quiz $quiz)
{
    // Delete related questions first
    $quiz->questions()->delete();

    // Delete the quiz itself
    $quiz->delete();

    return response()->json(['message' => 'Quiz and its questions deleted successfully.']);
}

}
