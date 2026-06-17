<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAssessment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // If retake is requested, we show the quiz regardless
        if ($request->query('retake') == 1) {
            return view('memberpages.quiz', [
                'questions' => $this->getQuestions()
            ]);
        }

        // Fetch latest assessment
        $assessment = UserAssessment::where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($assessment) {
            return view('memberpages.quiz-results', [
                'assessment' => $assessment
            ]);
        }

        return view('memberpages.quiz', [
            'questions' => $this->getQuestions()
        ]);
    }

    public function submit(Request $request)
    {
        $submittedAnswers = $request->input('answers', []);
        $questions = $this->getQuestions();

        $fundamentalsScore = 0;
        $earTrainingScore = 0;
        $chordsHarmonyScore = 0;
        $experienceScore = 0;

        $detailedAnswers = [];

        foreach ($questions as $q) {
            $qId = $q['id'];
            $ans = $submittedAnswers[$qId] ?? null;

            $pointsEarned = 0;
            $selectedText = '';

            if ($q['type'] === 'multi') {
                // Answers can be an array of selected option IDs
                $selectedOptionIds = is_array($ans) ? $ans : [];
                $selectedTextArray = [];
                foreach ($q['options'] as $opt) {
                    if (in_array($opt['id'], $selectedOptionIds)) {
                        $pointsEarned += $opt['points'];
                        $selectedTextArray[] = $opt['text'];
                    }
                }
                // Cap the points if specified (e.g. Question 8 has max points 15)
                if ($pointsEarned > $q['points']) {
                    $pointsEarned = $q['points'];
                }
                $selectedText = implode(', ', $selectedTextArray);
            } else {
                // Single select - ans is the text of the selected option
                $selectedText = $ans;
                foreach ($q['options'] as $opt) {
                    if ($opt['text'] === $ans) {
                        $pointsEarned = $opt['points'];
                        break;
                    }
                }
            }

            // Group scores by category
            if ($qId >= 1 && $qId <= 4) {
                $fundamentalsScore += $pointsEarned;
            } elseif ($qId >= 5 && $qId <= 7) {
                $earTrainingScore += $pointsEarned;
            } elseif ($qId >= 8 && $qId <= 11) {
                $chordsHarmonyScore += $pointsEarned;
            } elseif ($qId >= 12 && $qId <= 14) {
                $experienceScore += $pointsEarned;
            }

            $detailedAnswers[] = [
                'question_id' => $qId,
                'question' => $q['question'],
                'category' => $q['category'],
                'selected' => $selectedText,
                'points' => $pointsEarned
            ];
        }

        // Calculate percentages
        $fundamentalsPercent = round(($fundamentalsScore / 25) * 100);
        $earTrainingPercent = round(($earTrainingScore / 25) * 100);
        $chordsHarmonyPercent = round(($chordsHarmonyScore / 42) * 100);
        $experiencePercent = round(($experienceScore / 18) * 100);

        $totalScore = $fundamentalsScore + $earTrainingScore + $chordsHarmonyScore + $experienceScore;
        $overallPercent = round(($totalScore / 110) * 100);

        // Grade skill level
        // Beginner: 0-39%
        // Intermediate: 40-74%
        // Advanced: 75-100%
        if ($overallPercent < 40) {
            $skillLevel = 'Beginner';
        } elseif ($overallPercent <= 74) {
            $skillLevel = 'Intermediate';
        } else {
            $skillLevel = 'Advanced';
        }

        // Save assessment record
        UserAssessment::create([
            'user_id' => Auth::id(),
            'score' => $overallPercent,
            'skill_level' => $skillLevel,
            'fundamentals_score' => $fundamentalsPercent,
            'ear_training_score' => $earTrainingPercent,
            'chords_harmony_score' => $chordsHarmonyPercent,
            'experience_score' => $experiencePercent,
            'answers' => $detailedAnswers
        ]);

        // Update user profile skill level
        $user = Auth::user();
        $user->skill_level = strtolower($skillLevel);
        $user->save();

        return redirect()->route('member.quiz')->with('success', 'Assessment completed successfully!');
    }

    public function getQuestions()
    {
        return [
            [
                'id' => 1,
                'category' => 'Piano Fundamentals',
                'question' => 'Can you identify all 12 piano notes?',
                'points' => 5,
                'type' => 'single',
                'options' => [
                    ['text' => 'Yes confidently', 'points' => 5, 'subtext' => 'I can name and find all 12 notes on the keyboard instantly.'],
                    ['text' => 'Mostly', 'points' => 3, 'subtext' => 'I know most of them but sometimes have to think for a second.'],
                    ['text' => 'A few only', 'points' => 1, 'subtext' => 'I only know basic notes like C, F, or G.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I am still learning the note names on the keyboard.'],
                ],
                'tip' => 'Identifying notes quickly is the foundation of all chord construction and scale navigation.'
            ],
            [
                'id' => 2,
                'category' => 'Piano Fundamentals',
                'question' => 'Can you play major scales?',
                'points' => 8,
                'type' => 'single',
                'options' => [
                    ['text' => 'All 12 keys', 'points' => 8, 'subtext' => 'I can play major scales in all 12 keys comfortably.'],
                    ['text' => 'Some keys', 'points' => 5, 'subtext' => 'I can play major scales in several keys.'],
                    ['text' => 'Only C major', 'points' => 2, 'subtext' => 'I can only play the C major scale.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I am still learning how to construct major scales.'],
                ],
                'tip' => 'Major scales are the foundation for many songs and help you understand key signatures and melodies.'
            ],
            [
                'id' => 3,
                'category' => 'Piano Fundamentals',
                'question' => 'Can you play minor scales?',
                'points' => 5,
                'type' => 'single',
                'options' => [
                    ['text' => 'Yes comfortably', 'points' => 5, 'subtext' => 'I can play natural, harmonic, or melodic minor scales.'],
                    ['text' => 'A little', 'points' => 3, 'subtext' => 'I know a couple of minor scales.'],
                    ['text' => 'Rarely', 'points' => 1, 'subtext' => 'I struggle with minor scales.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I don\'t know minor scales yet.'],
                ],
                'tip' => 'Minor scales add emotional depth and are vital for understanding chord variations.'
            ],
            [
                'id' => 4,
                'category' => 'Piano Fundamentals',
                'question' => 'Can you play with both hands together?',
                'points' => 7,
                'type' => 'single',
                'options' => [
                    ['text' => 'Very comfortably', 'points' => 7, 'subtext' => 'I can play melodies and chords simultaneously with good rhythm.'],
                    ['text' => 'Sometimes', 'points' => 4, 'subtext' => 'I can manage basic patterns but struggle with complex rhythms.'],
                    ['text' => 'With difficulty', 'points' => 2, 'subtext' => 'I can only play very slowly when using both hands.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I play hands separately only.'],
                ],
                'tip' => 'Hand independence is built over time through focused finger exercises and slow practice.'
            ],
            [
                'id' => 5,
                'category' => 'Ear Training',
                'question' => 'Can you identify chord progressions by ear?',
                'points' => 10,
                'type' => 'single',
                'options' => [
                    ['text' => 'Easily', 'points' => 10, 'subtext' => 'I can hear a progression (e.g. 2-5-1, 7-3-6) and identify it instantly.'],
                    ['text' => 'Sometimes', 'points' => 6, 'subtext' => 'I can identify simple progressions but struggle with advanced ones.'],
                    ['text' => 'Rarely', 'points' => 2, 'subtext' => 'I need to test notes on the piano to find the progression.'],
                    ['text' => 'Never tried', 'points' => 0, 'subtext' => 'I don\'t know how to identify progressions by ear.'],
                ],
                'tip' => 'Training your ear to hear numbers/intervals makes learning songs on the fly incredibly fast.'
            ],
            [
                'id' => 6,
                'category' => 'Ear Training',
                'question' => 'Can you learn songs by ear?',
                'points' => 8,
                'type' => 'single',
                'options' => [
                    ['text' => 'Easily', 'points' => 8, 'subtext' => 'I can listen to a song and figure out the melody and chords quickly.'],
                    ['text' => 'With practice', 'points' => 5, 'subtext' => 'I can do it, but it takes me some time and trial-and-error.'],
                    ['text' => 'Slowly', 'points' => 2, 'subtext' => 'I can only figure out basic melodies or simple bass notes.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I rely on sheets, tutorials, or chord charts.'],
                ],
                'tip' => 'Learning by ear is a superpower that connects what you hear directly to your fingers.'
            ],
            [
                'id' => 7,
                'category' => 'Ear Training',
                'question' => 'Can you recognize chord changes while listening?',
                'points' => 7,
                'type' => 'single',
                'options' => [
                    ['text' => 'Most of the time', 'points' => 7, 'subtext' => 'I feel the harmonic movement and know when the chords change.'],
                    ['text' => 'Sometimes', 'points' => 4, 'subtext' => 'I notice changes in simple songs, but get lost in complex arrangements.'],
                    ['text' => 'Rarely', 'points' => 1, 'subtext' => 'I find it hard to distinguish between different chords in a recording.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I don\'t pay attention to chord changes yet.'],
                ],
                'tip' => 'Active listening is the first step to developing a strong musical ear.'
            ],
            [
                'id' => 8,
                'category' => 'Chords & Harmony',
                'question' => 'Which chords can you play confidently?',
                'points' => 15,
                'type' => 'multi',
                'options' => [
                    ['id' => 'major', 'text' => 'Major triads', 'points' => 2, 'subtext' => 'Basic three-note major chords.'],
                    ['id' => 'minor', 'text' => 'Minor triads', 'points' => 2, 'subtext' => 'Basic three-note minor chords.'],
                    ['id' => '7th', 'text' => '7th chords', 'points' => 3, 'subtext' => 'Dominant 7ths, Major 7ths, and Minor 7ths.'],
                    ['id' => 'extended', 'text' => 'Extended chords (9ths, 11ths, 13ths)', 'points' => 4, 'subtext' => 'Chords with rich, lush extensions.'],
                    ['id' => 'dim_aug', 'text' => 'Diminished/Augmented', 'points' => 2, 'subtext' => 'Tension chords used for passing harmony.'],
                    ['id' => 'slash', 'text' => 'Slash chords', 'points' => 2, 'subtext' => 'Chords with a specified bass note (e.g. C/E).'],
                ],
                'tip' => 'Expanding your chord vocabulary allows you to add color and sophistication to your playing.'
            ],
            [
                'id' => 9,
                'category' => 'Chords & Harmony',
                'question' => 'Can you play common gospel progressions?',
                'points' => 10,
                'type' => 'single',
                'options' => [
                    ['text' => 'Very comfortably', 'points' => 10, 'subtext' => 'I know and play standard gospel progressions (e.g. 7-3-6, 3-6-2-5-1) in multiple keys.'],
                    ['text' => 'Somewhat', 'points' => 6, 'subtext' => 'I know a few common movements, but mostly in C or Ab.'],
                    ['text' => 'Beginner level', 'points' => 2, 'subtext' => 'I play basic 1-4-5 progressions only.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I don\'t know gospel progressions yet.'],
                ],
                'tip' => 'Gospel music heavily utilizes secondary dominants and passing chords to create smooth transitions.'
            ],
            [
                'id' => 10,
                'category' => 'Chords & Harmony',
                'question' => 'Can you harmonize melodies?',
                'points' => 7,
                'type' => 'single',
                'options' => [
                    ['text' => 'Comfortably', 'points' => 7, 'subtext' => 'I can assign appropriate chords to a given melody line on the fly.'],
                    ['text' => 'A little', 'points' => 4, 'subtext' => 'I can do it with simple songs, but struggle to find the right colors.'],
                    ['text' => 'Beginner level', 'points' => 2, 'subtext' => 'I can find the bass notes but struggle to build chords.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I don\'t know how to harmonize melodies.'],
                ],
                'tip' => 'Melody harmonization is the bridge between playing notes and creating complete arrangements.'
            ],
            [
                'id' => 11,
                'category' => 'Chords & Harmony',
                'question' => 'Can you transpose songs into other keys?',
                'points' => 10,
                'type' => 'single',
                'options' => [
                    ['text' => 'Any key', 'points' => 10, 'subtext' => 'I can transpose songs to any key instantly using number systems.'],
                    ['text' => 'Some keys', 'points' => 6, 'subtext' => 'I can transpose to a few keys I am familiar with.'],
                    ['text' => 'Only simple songs', 'points' => 3, 'subtext' => 'I can transpose simple melodies or 3-chord songs.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I have to relearn the song completely for a new key.'],
                ],
                'tip' => 'Transposing is vital when accompanying singers who need a song higher or lower.'
            ],
            [
                'id' => 12,
                'category' => 'Experience & Confidence',
                'question' => 'Can you accompany singers smoothly?',
                'points' => 8,
                'type' => 'single',
                'options' => [
                    ['text' => 'Very confidently', 'points' => 8, 'subtext' => 'I can follow a singer\'s timing, dynamic changes, and stay in key.'],
                    ['text' => 'Somewhat', 'points' => 5, 'subtext' => 'I can accompany if they sing at a steady tempo, but struggle with rubato.'],
                    ['text' => 'Beginner level', 'points' => 2, 'subtext' => 'I can play basic chords behind them, but it sounds empty.'],
                    ['text' => 'Not yet', 'points' => 0, 'subtext' => 'I have only played solo or for myself.'],
                ],
                'tip' => 'Good accompaniment is about listening and supporting the singer, not overplaying.'
            ],
            [
                'id' => 13,
                'category' => 'Experience & Confidence',
                'question' => 'How long have you played piano?',
                'points' => 5,
                'type' => 'single',
                'options' => [
                    ['text' => '5+ years', 'points' => 5, 'subtext' => 'I have been playing for more than 5 years.'],
                    ['text' => '2–3 years', 'points' => 4, 'subtext' => 'I have been playing for 2 to 3 years.'],
                    ['text' => '1 year', 'points' => 2, 'subtext' => 'I have been playing for about 1 year.'],
                    ['text' => 'Less than 6 months', 'points' => 1, 'subtext' => 'I am brand new to the piano.'],
                ],
                'tip' => 'Consistency over time is what builds muscle memory and musical intuition.'
            ],
            [
                'id' => 14,
                'category' => 'Experience & Confidence',
                'question' => 'How do you explore and refine your personal sound?',
                'points' => 5,
                'type' => 'single',
                'options' => [
                    ['text' => 'Focus on emotional impact', 'points' => 5, 'subtext' => 'I select voicings and dynamics to create specific moods and expressions.'],
                    ['text' => 'Experiment with techniques', 'points' => 4, 'subtext' => 'I try different runs, fills, and rhythms to see what sounds good.'],
                    ['text' => 'Learn chords/scales/patterns', 'points' => 2, 'subtext' => 'I focus on building my library of shapes and mechanical patterns.'],
                ],
                'tip' => 'Refining your sound is a creative journey of finding what resonates most with you.'
            ],
            [
                'id' => 15,
                'category' => 'Experience & Confidence',
                'question' => 'What do you want to improve most?',
                'points' => 0,
                'type' => 'multi',
                'options' => [
                    ['id' => 'passing', 'text' => 'Passing Chords', 'points' => 0, 'subtext' => 'Smooth transitions between main chords.'],
                    ['id' => 'ear', 'text' => 'Ear Training', 'points' => 0, 'subtext' => 'Learning to play what you hear.'],
                    ['id' => 'improvisation', 'text' => 'Improvisation', 'points' => 0, 'subtext' => 'Creating fills, runs, and solos on the spot.'],
                    ['id' => 'technique', 'text' => 'Finger Technique', 'points' => 0, 'subtext' => 'Speed, agility, and hand independence.'],
                    ['id' => 'worship', 'text' => 'Worship Playing', 'points' => 0, 'subtext' => 'Accompanying worship service, talk music, and dynamics.'],
                    ['id' => 'theory', 'text' => 'Music Theory', 'points' => 0, 'subtext' => 'Understanding keys, numbers, and scale construction.'],
                    ['id' => 'playing_by_ear', 'text' => 'Playing by Ear', 'points' => 0, 'subtext' => 'Figuring out songs without sheets.'],
                    ['id' => 'gospel_embellishments', 'text' => 'Gospel Embellishments', 'points' => 0, 'subtext' => 'Adding slides, grace notes, and runs.'],
                    ['id' => 'confidence', 'text' => 'Confidence', 'points' => 0, 'subtext' => 'Overcoming performance anxiety and playing smoothly.'],
                ],
                'tip' => 'Identifying your goals helps focus your practice time for maximum growth.'
            ]
        ];
    }
}
