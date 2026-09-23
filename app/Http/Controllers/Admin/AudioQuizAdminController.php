<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizReferenceAudio;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AudioQuizAdminController extends Controller
{
    // The exact set of DB categories the member-facing Audio Quiz page
    // exposes (see AudioQuizController::index) — this is the source of
    // truth for what belongs in this admin, so unrelated/orphaned DB
    // categories (e.g. a stray old "Intervals" category unrelated to this
    // feature) never show up here.
    protected const CATEGORY_ORDER = [
        'Relative Pitch',
        'Melodic Dictation',
        'Diatonic Intervals',
        'Basic Triad',
        'Add 9 & b9',
        '7th Degree Chords',
        'Secondary 7th Chords',
        '9th Degree Chords',
        'Secondary 9th Chords',
        '11th Degree Chords',
        'Secondary 11th Chords',
        '13th Degree Chords',
        'Extentions recognition',
        'Chord Progressions',
        'Modal Voicings',
        'Scales',
    ];

    // Display label shown to the admin, where it differs from the raw DB
    // category value — matching exactly what members see on the Audio Quiz
    // page (e.g. the "Diatonic Intervals" DB category is shown there as
    // just "Intervals").
    protected const CATEGORY_LABELS = [
        'Diatonic Intervals' => 'Intervals',
        'Basic Triad' => 'Basic Triads',
    ];

    // Categories where the correct answer is an ordered sequence of notes
    // (e.g. "0,2,4") rather than a single choice from a fixed list.
    protected const SEQUENCE_CATEGORIES = ['Melodic Dictation'];

    // The fixed one-octave note set sequence answers are built from — white
    // keys (indices 0-7) then black keys (indices 8-12) — matching the exact
    // order the member-facing quiz keyboard uses to check answers.
    protected const SEQUENCE_NOTE_LABELS = [
        'Doh', 'Re', 'Mi', 'Fa', 'Sol', 'La', 'Ti', 'Doh', 'Di', 'Ri', 'Fi', 'Si', 'Toh',
    ];

    // Lessons where black keys (sharps/flats) are ALSO clickable, in
    // addition to the usual white/diatonic notes (white keys are always
    // clickable for every Melodic Dictation lesson).
    protected const BLACK_KEY_LESSONS = ['Other Notes', 'chordal melodies'];

    // Lessons with a three-part answer: one chord quality, one or more
    // degrees (some of which may carry a #/b accidental), and one or more
    // excluded notes. Stored as "quality;deg1[#|b],deg2[#|b],...;exc1,exc2".
    protected const CHORD_NAMING_LESSONS = [
        'Extentions recognition' => ['Chord naming'],
    ];
    protected const CHORD_NAMING_QUALITY_OPTIONS = ['Major', 'Minor', 'Augmented', 'Dominant', 'Diminished'];
    protected const CHORD_NAMING_DEGREE_OPTIONS = ['b5', '6th', '7th', '9th', '11th', '13th', 'Sus 2', 'Sus 4'];
    protected const CHORD_NAMING_DEGREE_ACCIDENTAL_INDICES = [3, 4, 5];
    protected const CHORD_NAMING_EXCLUDE_OPTIONS = ['No 3', 'No 5', 'None'];

    // Lessons where the admin specifies a fixed sequence of scale-degree
    // position labels (e.g. "2,5,1" — purely cosmetic, shown to the student
    // as fixed position badges) and, for each position, the correct chord
    // quality. Stored as "<num1>,<num2>,...;<qualityIdx1>|<qualityIdx2>|...".
    protected const PROGRESSION_RECOGNITION_LESSONS = [
        'Chord Progressions' => ['Progression recognition'],
    ];
    protected const PROGRESSION_RECOGNITION_QUALITY_OPTIONS = [
        'Major 7th', 'Minor 7th', 'Minor 7b5', 'Dominant 7th', 'Minor 6', 'Major 6', 'minMaj7',
    ];

    // Lessons like PROGRESSION_RECOGNITION_LESSONS, but each position also
    // gets zero or more extension degrees. Stored as
    // "<num1>,<num2>,...;<qualityIdx>,<deg1>-<deg2>-...;<qualityIdx>,...;..."
    // — one ";"-separated group per position, quality and its hyphenated
    // degree list joined by a comma (degree list may be empty).
    protected const PROGRESSION_DEGREE_LESSONS = [
        'Chord Progressions' => ['2-5-1 chord degree'],
    ];
    protected const PROGRESSION_DEGREE_MULTI_OPTIONS = [
        'b9', '9th', '#9', '11th', '#11', 'b13', '13th', 'Sus4', 'Sus2', 'b5',
    ];

    // Lessons where the correct answer is two independent choices — a chord
    // quality and its inversion — stored as "qualityIndex,inversionIndex".
    protected const COMPOUND_LESSONS = [
        'Basic Triad' => [
            'Key C all inversions' => [
                'quality' => ['Major', 'Minor', 'Diminished', 'Augmented', 'Suspended'],
                'inversion' => ['Root Position', '1st Inversion', '2nd Inversion'],
            ],
            'Inversions all keys' => [
                'quality' => ['Major', 'Minor', 'Diminished', 'Augmented', 'Suspended'],
                'inversion' => ['Root Position', '1st Inversion', '2nd Inversion'],
            ],
            'drop 2 key c' => [
                'quality' => ['Major', 'Minor', 'Diminished', 'Augmented', 'Suspended'],
                'inversion' => ['Root Position', '1st Inversion', '2nd Inversion'],
            ],
            'drop 2 all keys' => [
                'quality' => ['Major', 'Minor', 'Diminished', 'Augmented', 'Suspended'],
                'inversion' => ['Root Position', '1st Inversion', '2nd Inversion'],
            ],
        ],
        '7th Degree Chords' => [
            'KEY C' => [
                'quality' => ['Major 7th', 'Minor 7th', 'Diminished 7th', 'Dominant 7th', 'Minor 7(b5)'],
                'inversion' => ['Root Inversion', '1st Inversion', '2nd Inversion', '3rd Inversion'],
            ],
            'ALL KEYS' => [
                'quality' => ['Major 7th', 'Minor 7th', 'Diminished 7th', 'Dominant 7th', 'Minor 7(b5)'],
                'inversion' => ['Root Inversion', '1st Inversion', '2nd Inversion', '3rd Inversion'],
            ],
            'drop 2 key c' => [
                'quality' => ['Major 7th', 'Minor 7th', 'Diminished 7th', 'Dominant 7th', 'Minor 7(b5)'],
                'inversion' => ['Root Inversion', '1st Inversion', '2nd Inversion', '3rd Inversion'],
            ],
            'drop 2 all keys' => [
                'quality' => ['Major 7th', 'Minor 7th', 'Diminished 7th', 'Dominant 7th', 'Minor 7(b5)'],
                'inversion' => ['Root Inversion', '1st Inversion', '2nd Inversion', '3rd Inversion'],
            ],
        ],
        'Secondary 7th Chords' => [
            'On key C' => [
                'quality' => ['Augmented 7th', 'minMaj 7th', 'minMaj 7(b5)', 'Dominant 7(b5)', 'Major 7(#5)', 'Major 7b5'],
                'inversion' => ['Root Inversion', '1st Inversion', '2nd Inversion', '3rd Inversion'],
            ],
            'on all keys' => [
                'quality' => ['Augmented 7th', 'minMaj 7th', 'minMaj 7(b5)', 'Dominant 7(b5)', 'Major 7(#5)', 'Major 7b5'],
                'inversion' => ['Root Inversion', '1st Inversion', '2nd Inversion', '3rd Inversion'],
            ],
            'drop 2 key c' => [
                'quality' => ['Augmented 7th', 'minMaj 7th', 'minMaj 7(b5)', 'Dominant 7(b5)', 'Major 7(#5)', 'Major 7b5'],
                'inversion' => ['Root Inversion', '1st Inversion', '2nd Inversion', '3rd Inversion'],
            ],
            'drop 2 all keys' => [
                'quality' => ['Augmented 7th', 'minMaj 7th', 'minMaj 7(b5)', 'Dominant 7(b5)', 'Major 7(#5)', 'Major 7b5'],
                'inversion' => ['Root Inversion', '1st Inversion', '2nd Inversion', '3rd Inversion'],
            ],
        ],
    ];

    // Lessons where multiple answers are chosen (checkboxes) from one fixed
    // list, stored as comma-separated indices (order doesn't matter).
    protected const MULTI_SELECT_LESSONS = [
        'Extentions recognition' => [
            'Major Chord Extentions' => ['7th', 'b9', '9th', '11th', '#11', 'b13', '13th'],
            'Minor Chord Extentions' => ['7th', 'b9', '9th', '11th', '#11', 'b13', '13th'],
            'Dominant Chord Extentions' => ['b9', '9th', '#9', '11th', '#11', 'b13', '13th'],
        ],
    ];

    // Lessons where the correct answer is an ordered sequence built by
    // tapping chords (not notes) in order, from one fixed list, stored as
    // comma-separated indices — same mechanic as the melody sequence
    // picker, just with a different, per-lesson option list and no
    // black/white key distinction.
    protected const CHORD_SEQUENCE_LESSONS = [
        'Chord Progressions' => [
            'Basic 4 lines' => ['I (C)', 'ii (Dm)', 'iii (Em)', 'IV (F)', 'V (G)', 'vi (Am)', 'vii° (Bdim)'],
            'Basic 5 lines' => ['Imaj7 (Cmaj7)', 'ii7 (Dm7)', 'iii7 (Em7)', 'IVmaj7 (Fmaj7)', 'V7 (G7)', 'vi7 (Am7)', 'vii7b5 (Bm7b5)'],
        ],
    ];

    protected const FIXED_OPTIONS = [
        'Relative Pitch' => [
            'Single tone Pitch' => ['Doh', 'Reh', 'Mi', 'Fah', 'Sol', 'Lah', 'Ti'],
            'Single Tone Pitch' => ['Doh', 'Reh', 'Mi', 'Fah', 'Sol', 'Lah', 'Ti'],
            'Single Tone Pitch 2' => ['Doh', 'Reh', 'Mi', 'Fah', 'Sol', 'Lah', 'Ti'],
            'Relative second pitch' => ['Doh - Reh', 'Reh - Mi', 'Mi - Fah', 'Fah - Sol', 'Sol - Lah', 'Lah - Ti', 'Ti - Doh'],
            'Relative Thirds Pitch' => ['Doh - Mi', 'Reh - Fah', 'Mi - Sol', 'Fah - Lah', 'Sol - Ti', 'Lah - Doh', 'Ti - Reh'],
            'Relative Fourth Pitch' => ["Doh - Fah", "Reh - Sol", "Mi - Lah", "Fah - Ti", "Sol - Doh'", "Lah - Reh'", "Ti - Mi'"],
            'Relative Fifth Pitch' => ["Doh - Sol", "Reh - Lah", "Mi - Ti", "Fah - Doh'", "Sol - Reh'", "Lah - Mi'", "Ti - Fah'"],
            'Relative Sixth Pitch' => ["Doh - Lah", "Reh - Ti", "Mi - Doh'", "Fah - Reh'", "Sol - Mi'", "Lah - Fah'", "Ti - Sol'"],
            'Relative Seventh Pitch' => ["Doh - Ti", "Reh - Doh'", "Mi - Reh'", "Fah - Mi'", "Sol - Fah'", "Lah - Sol'", "Ti - Lah'"],
            'Find the Key' => ['C', 'C♯/Db', 'D', 'D♯/Eb', 'E', 'F', 'F♯/Gb', 'G', 'G♯/Ab', 'A', 'A♯/Bb', 'B'],
            'Find the key #2' => ['C', 'C♯/Db', 'D', 'D♯/Eb', 'E', 'F', 'F♯/Gb', 'G', 'G♯/Ab', 'A', 'A♯/Bb', 'B'],
        ],
        'Diatonic Intervals' => [
            'Diatonic Intervals' => ['Major 2nd', 'Major 3rd', 'Perfect 4th', 'Perfect 5th', 'Major 6th', 'Major 7th', 'Octave'],
            'Non-diatonic Intervals' => ['Minor 2nd', 'Minor 3rd', 'Tri tone', 'Minor 6th', 'Minor 7th'],
            'Intervals' => ['Minor 2nd', 'Major 2nd', 'Minor 3rd', 'Major 3rd', 'Perfect 4th', 'Tri tone', 'Perfect 5th', 'Minor 6th', 'Major 6th', 'Minor 7th', 'Major 7th', 'Octave'],
        ],
        'Basic Triad' => [
            'Key C root inversions' => ['Major', 'Minor', 'Diminished', 'Augmented', 'Suspended'],
            'All keys Root inversions' => ['Major', 'Minor', 'Diminished', 'Augmented', 'Suspended'],
            'Drop 2s Root Inversions' => ['Major', 'Minor', 'Diminished', 'Augmented', 'Suspended'],
            'Drop 2s all keys Root inversions' => ['Major', 'Minor', 'Diminished', 'Augmented', 'Suspended'],
        ],
        'Add 9 & b9' => [
            'Add 9 key c' => ['Major add 9', 'Minor add 9', 'Diminished add 9', 'Augmented add9'],
            'Add 9 all keys' => ['Major add 9', 'Minor add 9', 'Diminished add 9', 'Augmented add9'],
            'drop 2 key c' => ['Major add 9', 'Minor add 9', 'Diminished add 9', 'Augmented add9'],
            'drop 2 all keys' => ['Major add 9', 'Minor add 9', 'Diminished add 9', 'Augmented add9'],
            'add b9 key c' => ['Major b9', 'Minor b9', 'Diminished b9', 'Augmented b9'],
            'add b9 all keys' => ['Major b9', 'Minor b9', 'Diminished b9', 'Augmented b9'],
            'drop 2 add b9' => ['Major b9', 'Minor b9', 'Diminished b9', 'Augmented b9'],
            'drop 2 add b9 all keys' => ['Major b9', 'Minor b9', 'Diminished b9', 'Augmented b9'],
            'add 9 and add b9' => ['Major add 9', 'Minor add 9', 'Diminished add 9', 'Augmented add9', 'Major b9', 'Minor b9', 'Diminished b9', 'Augmented b9'],
        ],
        '7th Degree Chords' => [
            '7th Degree Chords' => ['Diminished 7th', 'Dominant 7th', 'Minor 7b5', 'Major 7th', 'Minor 7th'],
            'all keys root inv' => ['Major 7th', 'Minor 7th', 'Diminished 7th', 'Dominant 7th', 'Minor 7(b5)'],
            'drop 2 key c root' => ['Major 7th', 'Minor 7th', 'Diminished 7th', 'Dominant 7th', 'Minor 7(b5)'],
            'drop 2 all keys root' => ['Major 7th', 'Minor 7th', 'Diminished 7th', 'Dominant 7th', 'Minor 7(b5)'],
        ],
        'Secondary 7th Chords' => [
            'key c root' => ['Augmented 7th', 'minMaj 7th', 'minMaj 7(b5)', 'Dominant 7(b5)', 'Major 7(#5)', 'Major 7b5'],
            'all keys root' => ['Augmented 7th', 'minMaj 7th', 'minMaj 7(b5)', 'Dominant 7(b5)', 'Major 7(#5)', 'Major 7b5'],
            'Drop 2 key c (root inv)' => ['Augmented 7th', 'minMaj 7th', 'minMaj 7(b5)', 'Dominant 7(b5)', 'Major 7(#5)', 'Major 7b5'],
            'Drop 2 all keys (root inv)' => ['Augmented 7th', 'minMaj 7th', 'minMaj 7(b5)', 'Dominant 7(b5)', 'Major 7(#5)', 'Major 7b5'],
            'general' => ['Diminished 7th', 'Minor 7(b5)', 'Dominant 7th', 'Augmented 7th', 'minMaj 7th', 'Dominant 7(b5)', 'Minor 7th', 'Major 7th', 'Major 7(#5)', 'minMaj 7(b5)', 'Major 7b5'],
        ],
        '9th Degree Chords' => [
            'key c' => ['Major 9th', 'Minor 9th', 'Diminished 7(9)', 'Dominant 9th', 'Minor 9(b5)', 'Dominant 7(b9)', 'Minor 7(b5,b9)'],
            'diff keys' => ['Major 9th', 'Minor 9th', 'Diminished 7(9)', 'Dominant 9th', 'Minor 9(b5)', 'Dominant 7(b9)', 'Minor 7(b5,b9)'],
            'Inversion (with the root base)' => ['Major 9th', 'Minor 9th', 'Diminished 7(9)', 'Dominant 9th', 'Minor 9(b5)', 'Dominant 7(b9)', 'Minor 7(b5,b9)'],
            'Inversion without the root on the bass' => ['Major 9th', 'Minor 9th', 'Diminished 7(9)', 'Dominant 9th', 'Minor 9(b5)', 'Dominant 7(b9)', 'Minor 7(b5,b9)'],
        ],
        'Secondary 9th Chords' => [
            'sec key c' => ['Major 9(#5)', 'minMaj 9(b5)', 'Diminished 7(b9)', 'Dominant 9(b5)', 'minMaj 9th', 'Augmented 9th', 'Minor 7(b9,b5)', 'Major 7 (9,b5)'],
            'diff keys' => ['Major 9(#5)', 'minMaj 9(b5)', 'Diminished 7(b9)', 'Dominant 9(b5)', 'minMaj 9th', 'Augmented 9th', 'Minor 7(b9,b5)', 'Major 7 (9,b5)'],
            'Inversion (with the root base)' => ['Major 9(#5)', 'minMaj 9(b5)', 'Diminished 7(b9)', 'Dominant 9(b5)', 'minMaj 9th', 'Augmented 9th', 'Minor 7(b9,b5)', 'Major 7 (9,b5)'],
            'Inversion without the root on the bass' => ['Major 9(#5)', 'minMaj 9(b5)', 'Diminished 7(b9)', 'Dominant 9(b5)', 'minMaj 9th', 'Augmented 9th', 'Minor 7(b9,b5)', 'Major 7 (9,b5)'],
            'general' => ['minMaj 9th', 'Dominant 7(b9)', 'Minor 9(b5)', 'Augmented 9th', 'Major 9(#5)', 'Diminished 7(9)', 'Minor 7(b9,b5)', 'Dominant 9th', 'minMaj 9(b5)', 'Major 9th', 'Diminished 7(b9)', 'Minor 9th', 'Dominant 9(b5)', 'Major 7 (9,b5)'],
        ],
        '11th Degree Chords' => [
            'key c' => ['Major 9(#11)', 'Minor 11th', 'Diminished 7(9,11)', 'Dominant 9(#11)', 'Minor 11(b5)', 'Dominant 7(b9,#11)'],
            'diff keys' => ['Major 9(#11)', 'Minor 11th', 'Diminished 7(9,11)', 'Dominant 9(#11)', 'Minor 11(b5)', 'Dominant 7(b9,#11)'],
            'Inversion (with the root base)' => ['Major 9(#11)', 'Minor 11th', 'Diminished 7(9,11)', 'Dominant 9(#11)', 'Minor 11(b5)', 'Dominant 7(b9,#11)'],
            'Inversion without the root on the bass' => ['Major 9(#11)', 'Minor 11th', 'Diminished 7(9,11)', 'Dominant 9(#11)', 'Minor 11(b5)', 'Dominant 7(b9,#11)'],
        ],
        'Secondary 11th Chords' => [
            'key c' => ['Major 9(#11,#5)', 'minMaj 9(#11,b5)', 'Diminished 7(11,b9)', 'minMaj 11th', 'Augmented 9(#11)', 'Minor 11(b9,b5)'],
            'diff key' => ['Major 9(#11,#5)', 'minMaj 9(#11,b5)', 'Diminished 7(11,b9)', 'minMaj 11th', 'Augmented 9(#11)', 'Minor 11(b9,b5)'],
            'Inversion (with the root base)' => ['Major 9(#11,#5)', 'minMaj 9(#11,b5)', 'Diminished 7(11,b9)', 'minMaj 11th', 'Augmented 9(#11)', 'Minor 11(b9,b5)'],
            'Inversion (without the root base)' => ['Major 9(#11,#5)', 'minMaj 9(#11,b5)', 'Diminished 7(11,b9)', 'minMaj 11th', 'Augmented 9(#11)', 'Minor 11(b9,b5)'],
            'general' => ['minMaj 11th', 'Dominant 9(#11)', 'Minor 11(b9,b5)', 'Major 9(#11,#5)', 'Diminished 7(11,b9)', 'Major 9(#11)', 'Dominant 7(b9,#11)', 'Minor 11th', 'minMaj 9(#11,b5)', 'Diminished 7(9,11)', 'Augmented 9(#11)', 'Minor 11(b5)'],
        ],
        '13th Degree Chords' => [
            'key c' => ['Major 9(#11,13)', 'Minor 13th', 'Dominant 9(#11,13)', 'Minor #11(13,b5)', 'Dominant 7(b9,#11,13)'],
            'diff key' => ['Major 9(#11,13)', 'Minor 13th', 'Dominant 9(#11,13)', 'Minor #11(13,b5)', 'Dominant 7(b9,#11,13)'],
            'Inversion (with the root base)' => ['Major 9(#11,13)', 'Minor 13th', 'Dominant 9(#11,13)', 'Minor #11(13,b5)', 'Dominant 7(b9,#11,13)'],
            'Inversion (without the root base)' => ['Major 9(#11,13)', 'Minor 13th', 'Dominant 9(#11,13)', 'Minor #11(13,b5)', 'Dominant 7(b9,#11,13)'],
        ],
        'Extentions recognition' => [
            'Dominant Technique' => ['Sus Dominant', 'Slash Dominant', 'Rootless Dominant', 'Altered Dominant', 'Augmented Dominant'],
        ],
        'Chord Progressions' => [
            'Cadence Identification' => ['Authentic', 'Plagal', 'Minor Plagal', 'Sub-Plagal', 'Deceptive'],
            'Passing Progression' => ['2-5-1', '3-6-2', 'b5-7-3', '5-1-4', '6-2-5', '7-3-6'],
            'Dom resolution' => ['Authentic', 'Tri-tone', 'Back-door', 'Double Back-door', 'Double Plagal', 'Screen door', 'Double Predominant'],
            'Modulation' => ['Minor 6th', 'Major 2nd', 'Minor 2nd', 'Perfect 4th', 'Perfect Fifth', 'Minor 3rd'],
        ],
        'Modal Voicings' => [
            'Over Single Note #1' => ['Aeolian', 'Lydian Augmented', 'Altered Dominant', 'Mixolydian', 'Lydian Diminished'],
            'Over Single Note #2' => ['Aeolian', 'Lydian Augmented', 'Altered Dominant', 'Mixolydian', 'Lydian Diminished'],
            'Over Major 7ths #1' => ['Gypsy major', 'Lydian Mode', 'Harmonic Major', 'Ionian mode'],
            'Over Major 7ths #2' => ['Gypsy major', 'Lydian Mode', 'Harmonic Major', 'Ionian mode'],
            'Over Dominant #1' => ['Phrygian Dominant', 'Lydian Dominant', 'Hungarian Major', 'Romanian Major', 'Super Phrygian', 'Dominant Diminished'],
            'Over Dominant #2' => ['Phrygian Dominant', 'Lydian Dominant', 'Hungarian Major', 'Romanian Major', 'Super Phrygian', 'Dominant Diminished'],
            'Over Minor 7th #1' => ['Phrygian', 'Romanian Minor', 'Dorian', 'Altered Minor', 'Phrygian Blues'],
            'Over Minor 7th #2' => ['Phrygian', 'Romanian Minor', 'Dorian', 'Altered Minor', 'Phrygian Blues'],
        ],
        'Scales' => [
            'Tonal Modes' => ['Ionian', 'Harmonic Major', 'Harmonic Minor', 'Melodic Minor', 'Aeolian'],
            'Tonal Modes #2' => ['Ionian', 'Harmonic Major', 'Harmonic Minor', 'Melodic Minor', 'Aeolian'],
            'Major mode' => ['Ionian', 'Dorian', 'Phrygian', 'Lydian', 'Mixolydian', 'Aeolian', 'Locrian'],
            'Dorian Mode' => ['Dorian', 'Dorian b2', 'Dorian #4', 'Dorian #5'],
            'Lydian Mode' => ['Lydian', 'Lydian Augmented', 'Lydian Dominant', 'Lydian #2', 'Lydian Diminished'],
            'Locrian Mode' => ['Locrian', 'Locrian ♮2', 'Super Locrian', 'Locrian ♮6', 'Ultralocrian', 'Locrian 𝄫7'],
            'Phrygian Mode' => ['Phrygian', 'Phrygian Dominant', 'Phrygian #6', 'Phrygian b4', 'Melodic Phrygian'],
            'Mixolydian Mode' => ['Mixolydian', 'Mixolydian b9', 'Mixolydian #11', 'Mixolydian Altered', 'Mixolydian b13'],
        ],
    ];

    public function index(Request $request)
    {
        $category = $request->query('category');

        if (!$category) {
            return $this->categoryGrid();
        }

        return $this->categoryShow($request, $category);
    }

    protected function categoryGrid()
    {
        $quizzes = Quiz::withCount('questions')->get()->groupBy('category');

        $categories = collect(self::CATEGORY_ORDER)
            ->filter(fn ($name) => $quizzes->has($name))
            ->map(function ($name) use ($quizzes) {
                $lessons = $quizzes->get($name);

                return [
                    'name' => self::CATEGORY_LABELS[$name] ?? $name,
                    'db_category' => $name,
                    'lesson_count' => $lessons->count(),
                    'configured_count' => $lessons->filter(fn ($q) => $q->questions_count > 0)->count(),
                    'is_built' => array_key_exists($name, self::FIXED_OPTIONS)
                        || array_key_exists($name, self::COMPOUND_LESSONS)
                        || array_key_exists($name, self::MULTI_SELECT_LESSONS)
                        || array_key_exists($name, self::CHORD_SEQUENCE_LESSONS)
                        || array_key_exists($name, self::CHORD_NAMING_LESSONS)
                        || array_key_exists($name, self::PROGRESSION_RECOGNITION_LESSONS)
                        || array_key_exists($name, self::PROGRESSION_DEGREE_LESSONS)
                        || in_array($name, self::SEQUENCE_CATEGORIES),
                ];
            })
            ->values();

        return view('admin.audio-quiz.index', compact('categories'));
    }

    protected function categoryShow(Request $request, string $category)
    {
        $lessons = Quiz::where('category', $category)
            ->with('questions', 'referenceAudios')
            ->orderBy('id')
            ->get();

        abort_if($lessons->isEmpty(), 404);

        $activeId = (int) $request->query('quiz', optional($lessons->first())->id);
        $activeLesson = $lessons->firstWhere('id', $activeId) ?? $lessons->first();
        $categoryLabel = self::CATEGORY_LABELS[$category] ?? $category;

        $answerType = 'none';
        $fixedOptions = [];
        $compoundOptions = null;
        $isSequenceCategory = in_array($category, self::SEQUENCE_CATEGORIES);
        $sequenceLabels = self::SEQUENCE_NOTE_LABELS;
        $sequenceNoteCount = null;
        $allowBlackKeys = false;
        $chordSequenceOptions = null;
        $chordSequenceLength = null;
        $chordNamingOptions = null;
        $progressionQualityOptions = null;
        $progressionDegreeOptions = null;

        if ($activeLesson) {
            if ($isSequenceCategory) {
                $answerType = 'sequence';
                $sequenceNoteCount = $this->expectedSequenceLength($activeLesson);
                $allowBlackKeys = in_array($activeLesson->title, self::BLACK_KEY_LESSONS);
            } elseif (in_array($activeLesson->title, self::PROGRESSION_DEGREE_LESSONS[$category] ?? [])) {
                $answerType = 'progression-degree';
                $progressionQualityOptions = self::PROGRESSION_RECOGNITION_QUALITY_OPTIONS;
                $progressionDegreeOptions = self::PROGRESSION_DEGREE_MULTI_OPTIONS;
            } elseif (in_array($activeLesson->title, self::PROGRESSION_RECOGNITION_LESSONS[$category] ?? [])) {
                $answerType = 'progression-recognition';
                $progressionQualityOptions = self::PROGRESSION_RECOGNITION_QUALITY_OPTIONS;
            } elseif (in_array($activeLesson->title, self::CHORD_NAMING_LESSONS[$category] ?? [])) {
                $answerType = 'chord-naming';
                $chordNamingOptions = [
                    'quality' => self::CHORD_NAMING_QUALITY_OPTIONS,
                    'degree' => self::CHORD_NAMING_DEGREE_OPTIONS,
                    'accidentalIndices' => self::CHORD_NAMING_DEGREE_ACCIDENTAL_INDICES,
                    'exclude' => self::CHORD_NAMING_EXCLUDE_OPTIONS,
                ];
            } elseif ($chordSeq = self::CHORD_SEQUENCE_LESSONS[$category][$activeLesson->title] ?? null) {
                $answerType = 'chord-sequence';
                $chordSequenceOptions = $chordSeq;
                $chordSequenceLength = $this->expectedChordSequenceLength($activeLesson);
            } elseif ($compound = self::COMPOUND_LESSONS[$category][$activeLesson->title] ?? null) {
                $answerType = 'compound';
                $compoundOptions = $compound;
            } elseif ($multi = self::MULTI_SELECT_LESSONS[$category][$activeLesson->title] ?? null) {
                $answerType = 'multiselect';
                $fixedOptions = $multi;
            } elseif ($single = self::FIXED_OPTIONS[$category][$activeLesson->title] ?? null) {
                $answerType = 'single';
                $fixedOptions = $single;
            }
        }

        $allCategories = collect(self::CATEGORY_ORDER)->map(fn ($name) => [
            'db_category' => $name,
            'label' => self::CATEGORY_LABELS[$name] ?? $name,
        ]);

        return view('admin.audio-quiz.show', compact(
            'category', 'categoryLabel', 'lessons', 'activeLesson', 'allCategories',
            'answerType', 'fixedOptions', 'compoundOptions',
            'isSequenceCategory', 'sequenceLabels', 'sequenceNoteCount', 'allowBlackKeys',
            'chordSequenceOptions', 'chordSequenceLength', 'chordNamingOptions',
            'progressionQualityOptions', 'progressionDegreeOptions'
        ));
    }

    // "3-note melody" -> 3. "Other Notes" / "chordal melodies" have no fixed
    // length — the picker (and validation) let the admin build a melody of
    // any length for those two.
    protected function expectedSequenceLength(Quiz $quiz): ?int
    {
        if (preg_match('/^(\d+)-note/i', $quiz->title, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    // Both "Basic 4 lines" and "Basic 5 lines" are 4-chord progressions —
    // the "4"/"5" in the title refers to the chord-quality set used (triads
    // vs. seventh chords), not the progression length.
    protected function expectedChordSequenceLength(Quiz $quiz): ?int
    {
        return 4;
    }

    public function renameLesson(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $quiz->update(['title' => $validated['title']]);

        return redirect()
            ->route('admin.audio-quiz', ['category' => $quiz->category, 'quiz' => $quiz->id])
            ->with('success', 'Lesson renamed successfully.');
    }

    public function renameCategory(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ]);

        Quiz::where('category', $validated['category'])->update(['category' => $validated['name']]);

        return redirect()
            ->route('admin.audio-quiz', ['category' => $validated['name']])
            ->with('success', 'Category renamed successfully.');
    }

    public function updateLesson(Request $request, Quiz $quiz)
    {
        if ($request->has('video_url')) {
            $quiz->video_url = $request->input('video_url', '');
        }

        if ($request->has('difficulty')) {
            $request->validate([
                'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
            ]);
            $quiz->difficulty = $request->input('difficulty');
        }

        if ($request->has('question_prompt')) {
            $request->validate([
                'question_prompt' => 'nullable|string|max:255',
            ]);
            $quiz->question_prompt = $request->input('question_prompt') ?: null;
        }

        if ($request->has('progression_numbers')) {
            $request->validate([
                'progression_numbers' => ['required', 'regex:/^\d+(,\d+)*$/'],
            ]);
            $quiz->progression_numbers = $request->input('progression_numbers');
        }

        $quiz->save();

        return redirect()
            ->route('admin.audio-quiz', ['category' => $quiz->category, 'quiz' => $quiz->id])
            ->with('success', 'Lesson updated successfully.');
    }

    public function storeReferenceAudio(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'audio' => 'required|file|mimes:mp3,wav,ogg',
        ]);

        $audio = $validated['audio'];
        $audioName = time() . '_' . $audio->getClientOriginalName();

        $destination = base_path('../public_html/uploads/audio');
        if (!file_exists($destination)) {
            $destination = public_path('uploads/audio');
        }
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $audio->move($destination, $audioName);

        QuizReferenceAudio::create([
            'quiz_id' => $quiz->id,
            'name' => $validated['name'] ?? null,
            'audio_path' => "/uploads/audio/$audioName",
        ]);

        return redirect()
            ->route('admin.audio-quiz', ['category' => $quiz->category, 'quiz' => $quiz->id])
            ->with('success', 'Reference audio added successfully.');
    }

    public function destroyReferenceAudio(QuizReferenceAudio $referenceAudio)
    {
        $quiz = $referenceAudio->quiz;
        $referenceAudio->delete();

        return redirect()
            ->route('admin.audio-quiz', ['category' => $quiz->category, 'quiz' => $quiz->id])
            ->with('success', 'Reference audio deleted.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate(['audio' => 'required|file|mimes:mp3,wav,ogg']);
        $correctOption = $this->validateCorrectOption($request, $quiz);

        $audio = $request->file('audio');
        $audioName = time() . '_' . $audio->getClientOriginalName();

        $destination = base_path('../public_html/uploads/audio');
        if (!file_exists($destination)) {
            $destination = public_path('uploads/audio');
        }
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $audio->move($destination, $audioName);

        QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'audio_path' => "/uploads/audio/$audioName",
            'correct_option' => $correctOption,
        ]);

        $this->mirrorToFindTheKeyTwo($quiz, function ($siblingQuiz) use ($audioName, $correctOption) {
            QuizQuestion::create([
                'quiz_id' => $siblingQuiz->id,
                'audio_path' => "/uploads/audio/$audioName",
                'correct_option' => $correctOption,
            ]);
        });

        return redirect()
            ->route('admin.audio-quiz', ['category' => $quiz->category, 'quiz' => $quiz->id])
            ->with('success', 'Question added successfully.');
    }

    public function destroyQuestion(QuizQuestion $question)
    {
        $quiz = $question->quiz;
        $audioPath = $question->audio_path;
        $question->delete();

        $this->mirrorToFindTheKeyTwo($quiz, function ($siblingQuiz) use ($audioPath) {
            QuizQuestion::where('quiz_id', $siblingQuiz->id)
                ->where('audio_path', $audioPath)
                ->delete();
        });

        return redirect()
            ->route('admin.audio-quiz', ['category' => $quiz->category, 'quiz' => $quiz->id])
            ->with('success', 'Question deleted.');
    }

    public function updateQuestion(Request $request, QuizQuestion $question)
    {
        $quiz = $question->quiz;
        $correctOption = $this->validateCorrectOption($request, $quiz);

        $question->update([
            'correct_option' => $correctOption,
        ]);

        $this->mirrorToFindTheKeyTwo($quiz, function ($siblingQuiz) use ($question, $correctOption) {
            QuizQuestion::where('quiz_id', $siblingQuiz->id)
                ->where('audio_path', $question->audio_path)
                ->update(['correct_option' => $correctOption]);
        });

        return redirect()
            ->route('admin.audio-quiz', ['category' => $quiz->category, 'quiz' => $quiz->id])
            ->with('success', 'Correct answer updated.');
    }

    /**
     * "Find the Key" and "Find the key #2" are near-identical lessons, so
     * adding/editing/removing a question on "Find the Key" mirrors the same
     * change onto "Find the key #2" — saves the admin from managing both by
     * hand. One-directional only: changes on "Find the key #2" don't mirror
     * back. Matching between the two lessons' questions is by audio_path,
     * since mirrored questions always share the exact same uploaded file.
     */
    protected function mirrorToFindTheKeyTwo(Quiz $quiz, callable $callback): void
    {
        if ($quiz->title !== 'Find the Key') {
            return;
        }

        $siblingQuiz = Quiz::where('category', $quiz->category)
            ->where('title', 'Find the key #2')
            ->first();

        if ($siblingQuiz) {
            $callback($siblingQuiz);
        }
    }

    // A plain abort(422) renders Laravel's blank JSON/error page instead of
    // redirecting back to the form — this instead behaves like a normal
    // $request->validate() failure: redirect back with the message flashed
    // into $errors (already rendered by the "@if ($errors->any())" banner)
    // and the submitted input preserved.
    protected function failValidation(string $message): never
    {
        throw \Illuminate\Validation\ValidationException::withMessages(['correct_option' => $message]);
    }

    protected function validateCorrectOption(Request $request, Quiz $quiz): string
    {
        if (in_array($quiz->category, self::SEQUENCE_CATEGORIES)) {
            // White keys (0-7) are always allowed; a couple of lessons
            // additionally allow the 5 black keys (8-12).
            $allowBlackKeys = in_array($quiz->title, self::BLACK_KEY_LESSONS);
            $validRange = $allowBlackKeys ? range(0, 12) : range(0, 7);

            $validated = $request->validate([
                'correct_option' => ['required', 'regex:/^\d+(,\d+)*$/'],
            ]);

            $indices = array_map('intval', explode(',', $validated['correct_option']));

            foreach ($indices as $index) {
                if (!in_array($index, $validRange)) {
                    $this->failValidation('Invalid note in melody sequence.');
                }
            }

            $expectedLength = $this->expectedSequenceLength($quiz);
            if ($expectedLength !== null && count($indices) !== $expectedLength) {
                $this->failValidation("This lesson requires exactly {$expectedLength} notes.");
            }

            return implode(',', $indices);
        }

        if (in_array($quiz->title, self::PROGRESSION_DEGREE_LESSONS[$quiz->category] ?? [])) {
            if (!$quiz->progression_numbers) {
                $this->failValidation('Set this lesson\'s progression pattern first (in the Progression tab above).');
            }

            $qualityMax = count(self::PROGRESSION_RECOGNITION_QUALITY_OPTIONS) - 1;
            $degreeMax = count(self::PROGRESSION_DEGREE_MULTI_OPTIONS) - 1;
            $positions = explode(',', $quiz->progression_numbers);

            $validated = $request->validate([
                'qualities' => ['required', 'array', 'size:' . count($positions)],
                'qualities.*' => 'required|integer|min:0|max:' . $qualityMax,
                'degrees' => ['nullable', 'array'],
                'degrees.*' => ['nullable', 'array'],
                'degrees.*.*' => 'integer|min:0|max:' . $degreeMax,
            ]);

            $degreesPerPosition = $validated['degrees'] ?? [];

            $groups = [];
            foreach ($validated['qualities'] as $idx => $qualityIndex) {
                $degrees = array_map('intval', $degreesPerPosition[$idx] ?? []);
                $groups[] = $qualityIndex . ',' . implode('-', $degrees);
            }

            return $quiz->progression_numbers . ';' . implode(';', $groups);
        }

        if (in_array($quiz->title, self::PROGRESSION_RECOGNITION_LESSONS[$quiz->category] ?? [])) {
            if (!$quiz->progression_numbers) {
                $this->failValidation('Set this lesson\'s progression pattern first (in the Progression tab above).');
            }

            $qualityMax = count(self::PROGRESSION_RECOGNITION_QUALITY_OPTIONS) - 1;
            $positions = explode(',', $quiz->progression_numbers);

            $validated = $request->validate([
                'qualities' => ['required', 'array', 'size:' . count($positions)],
                'qualities.*' => 'required|integer|min:0|max:' . $qualityMax,
            ]);

            return $quiz->progression_numbers . ';' . implode('|', $validated['qualities']);
        }

        if (in_array($quiz->title, self::CHORD_NAMING_LESSONS[$quiz->category] ?? [])) {
            $degreeMax = count(self::CHORD_NAMING_DEGREE_OPTIONS) - 1;
            $excludeMax = count(self::CHORD_NAMING_EXCLUDE_OPTIONS) - 1;

            $validated = $request->validate([
                'quality' => 'required|integer|min:0|max:' . (count(self::CHORD_NAMING_QUALITY_OPTIONS) - 1),
                'degrees_encoded' => ['required', 'regex:/^\d+[#b]?(,\d+[#b]?)*$/'],
                'exclude_encoded' => ['required', 'regex:/^\d+(,\d+)*$/'],
            ]);

            foreach (explode(',', $validated['degrees_encoded']) as $token) {
                preg_match('/^(\d+)([#b])?$/', $token, $m);
                $index = (int) $m[1];
                if ($index < 0 || $index > $degreeMax) {
                    $this->failValidation('Invalid chord degree.');
                }
                if (!empty($m[2]) && !in_array($index, self::CHORD_NAMING_DEGREE_ACCIDENTAL_INDICES)) {
                    $this->failValidation('That degree cannot take a sharp/flat.');
                }
            }

            foreach (explode(',', $validated['exclude_encoded']) as $token) {
                $index = (int) $token;
                if ($index < 0 || $index > $excludeMax) {
                    $this->failValidation('Invalid excluded note.');
                }
            }

            return "{$validated['quality']};{$validated['degrees_encoded']};{$validated['exclude_encoded']}";
        }

        if ($chordOptions = self::CHORD_SEQUENCE_LESSONS[$quiz->category][$quiz->title] ?? null) {
            $validated = $request->validate([
                'correct_option' => ['required', 'regex:/^\d+(,\d+)*$/'],
            ]);

            $indices = array_map('intval', explode(',', $validated['correct_option']));
            $maxIndex = count($chordOptions) - 1;

            foreach ($indices as $index) {
                if ($index < 0 || $index > $maxIndex) {
                    $this->failValidation('Invalid chord in progression.');
                }
            }

            $expectedLength = $this->expectedChordSequenceLength($quiz);
            if ($expectedLength !== null && count($indices) !== $expectedLength) {
                $this->failValidation("This lesson requires exactly {$expectedLength} chords.");
            }

            return implode(',', $indices);
        }

        if ($compound = self::COMPOUND_LESSONS[$quiz->category][$quiz->title] ?? null) {
            $validated = $request->validate([
                'quality' => 'required|integer|min:0|max:' . (count($compound['quality']) - 1),
                'inversion' => 'required|integer|min:0|max:' . (count($compound['inversion']) - 1),
            ]);

            return "{$validated['quality']},{$validated['inversion']}";
        }

        if ($options = self::MULTI_SELECT_LESSONS[$quiz->category][$quiz->title] ?? null) {
            $validated = $request->validate([
                'correct_option' => ['required', 'array', 'min:1'],
                'correct_option.*' => 'integer|min:0|max:' . (count($options) - 1),
            ]);

            $indices = array_unique(array_map('intval', $validated['correct_option']));
            sort($indices);

            return implode(',', $indices);
        }

        $options = self::FIXED_OPTIONS[$quiz->category][$quiz->title] ?? [];

        $validated = $request->validate([
            'correct_option' => 'required|integer|min:0|max:' . max(0, count($options) - 1),
        ]);

        return (string) $validated['correct_option'];
    }

    /**
     * For "Find the Key" style lessons, the key highlighted on the member
     * player's mini keyboard for this question. Returns null for every other
     * lesson, so it's always safe to call regardless of quiz type.
     */
}
