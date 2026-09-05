<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class EarTrainingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sharedAudio = [
            '/uploads/audio/1769332639_pre1.mp3',
            '/uploads/audio/1769332691_pre1.mp3',
        ];

        $quizzes = [
            [
                'title' => 'Single tone Pitch',
                'description' => 'A starter ear training set focused on identifying solfege tones by ear.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                ],
            ],
            [
                'title' => 'Single Tone Pitch',
                'description' => 'A follow-up ear training set focused on identifying solfege tones by ear.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                ],
            ],
            [
                'title' => 'Relative Thirds Pitch',
                'description' => 'Practice identifying pairs of solfege tones a third apart by ear.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '5'],
                ],
            ],
            [
                'title' => 'Relative Fourth Pitch',
                'description' => 'Practice identifying pairs of solfege tones a fourth apart by ear.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '4'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '6'],
                ],
            ],
            [
                'title' => 'Relative Fifth Pitch',
                'description' => 'Practice identifying pairs of solfege tones a fifth apart by ear.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '5'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                ],
            ],
            [
                'title' => 'Relative Sixth Pitch',
                'description' => 'Practice identifying pairs of solfege tones a sixth apart by ear.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '6'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                ],
            ],
            [
                'title' => 'Relative Seventh Pitch',
                'description' => 'Practice identifying pairs of solfege tones a seventh apart by ear.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                ],
            ],
            [
                'title' => 'Find the Key',
                'description' => 'Practice identifying the chromatic note you hear by ear.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '7'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '9'],
                ],
            ],
            [
                'title' => 'Melodic dictation',
                'description' => 'Practice writing down a 3-note melody by ear, in solfa notation.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores a comma-separated sequence of indices
                // into ["Doh","Re","Mi","Fa","Sol","La","Ti"] for this quiz.
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0,6,4'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '2,0,4'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4,3,0'],
                ],
            ],
            [
                'title' => 'Melodic dictation (Part 2)',
                'description' => 'Practice writing down a 4-note melody by ear, in solfa notation.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores a comma-separated sequence of indices
                // into ["Doh","Re","Mi","Fa","Sol","La","Ti"] for this quiz.
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0,2,4,6'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '4,2,0,4'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '6,5,4,0'],
                ],
            ],
            [
                'title' => 'Melodic dictation (Part 3)',
                'description' => 'Practice writing down a 5-note melody by ear, in solfa notation.',
                'category' => 'Relative Pitch',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores a comma-separated sequence of indices
                // into ["Doh","Re","Mi","Fa","Sol","La","Ti"] for this quiz.
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0,2,4,2,0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '4,3,2,1,0'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0,4,6,4,2'],
                ],
            ],
            [
                'title' => 'Diatonic Intervals',
                'description' => 'Practice identifying diatonic intervals (Major 2nd through Octave) by ear.',
                'category' => 'Diatonic Intervals',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '6'],
                ],
            ],
            [
                'title' => 'Non-diatonic Intervals',
                'description' => 'Practice identifying non-diatonic intervals (Minor 2nd, Minor 3rd, Tritone, Minor 6th, Minor 7th) by ear.',
                'category' => 'Diatonic Intervals',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                ],
            ],
            [
                'title' => 'Intervals',
                'description' => 'Practice identifying all intervals, from Minor 2nd through Octave, by ear.',
                'category' => 'Diatonic Intervals',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '5'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '11'],
                ],
            ],
            [
                'title' => 'Diatonic scale',
                'description' => 'Practice hearing common intervals before moving into more advanced chord work.',
                'category' => 'Intervals',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[1],
                'questions' => [
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '7'],
                ],
            ],
            [
                'title' => 'Key C root inversions',
                'description' => 'Practice identifying the quality of root-position triads in the key of C by ear.',
                'category' => 'Basic Triad',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                ],
            ],
            [
                'title' => 'All keys Root inversions',
                'description' => 'Practice identifying the quality of root-position triads across all keys by ear.',
                'category' => 'Basic Triad',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '4'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                ],
            ],
            [
                'title' => 'Drop 2s Root Inversions',
                'description' => 'Practice identifying the quality of drop 2 root-position triads by ear.',
                'category' => 'Basic Triad',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                ],
            ],
            [
                'title' => 'Drop 2s all keys Root inversions',
                'description' => 'Practice identifying the quality of drop 2 root-position triads across all keys by ear.',
                'category' => 'Basic Triad',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                ],
            ],
            [
                'title' => 'Key C all inversions',
                'description' => 'Practice identifying the quality of all triad inversions in the key of C by ear.',
                'category' => 'Basic Triad',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores "qualityIndex,inversionIndex" for this
                // quiz: quality indexes into
                // ["Major","Minor","Diminished","Augmented","Suspended"],
                // inversion indexes into ["Root Position","1st Inversion","2nd Inversion"].
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2,1'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '0,2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1,0'],
                ],
            ],
            [
                'title' => 'Inversions all keys',
                'description' => 'Practice identifying the quality of all triad inversions across all keys by ear.',
                'category' => 'Basic Triad',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores "qualityIndex,inversionIndex" for this
                // quiz: quality indexes into
                // ["Major","Minor","Diminished","Augmented","Suspended"],
                // inversion indexes into ["Root Position","1st Inversion","2nd Inversion"].
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3,0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '4,2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2,1'],
                ],
            ],
            [
                'title' => 'drop 2 key c',
                'description' => 'Practice identifying the quality of drop 2 triad inversions in the key of C by ear.',
                'category' => 'Basic Triad',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores "qualityIndex,inversionIndex" for this
                // quiz: quality indexes into
                // ["Major","Minor","Diminished","Augmented","Suspended"],
                // inversion indexes into ["Root Position","1st Inversion","2nd Inversion"].
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0,1'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '1,2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3,0'],
                ],
            ],
            [
                'title' => 'drop 2 all keys',
                'description' => 'Practice identifying the quality of drop 2 triad inversions across all keys by ear.',
                'category' => 'Basic Triad',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores "qualityIndex,inversionIndex" for this
                // quiz: quality indexes into
                // ["Major","Minor","Diminished","Augmented","Suspended"],
                // inversion indexes into ["Root Position","1st Inversion","2nd Inversion"].
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4,1'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '2,0'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1,2'],
                ],
            ],
            [
                'title' => 'Add 9 key c',
                'description' => 'Practice identifying add 9 chord qualities in the key of C by ear.',
                'category' => 'Add 9 & b9',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                ],
            ],
            [
                'title' => 'Add 9 all keys',
                'description' => 'Practice identifying add 9 chord qualities across all keys by ear.',
                'category' => 'Add 9 & b9',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                ],
            ],
            [
                'title' => 'drop 2 key c',
                'description' => 'Practice identifying drop 2 add 9 chord qualities in the key of C by ear.',
                'category' => 'Add 9 & b9',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3'],
                ],
            ],
            [
                'title' => 'drop 2 all keys',
                'description' => 'Practice identifying drop 2 add 9 chord qualities across all keys by ear.',
                'category' => 'Add 9 & b9',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                ],
            ],
            [
                'title' => 'add b9 key c',
                'description' => 'Practice identifying b9 chord qualities in the key of C by ear.',
                'category' => 'Add 9 & b9',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                ],
            ],
            [
                'title' => 'add b9 all keys',
                'description' => 'Practice identifying b9 chord qualities across all keys by ear.',
                'category' => 'Add 9 & b9',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                ],
            ],
            [
                'title' => 'drop 2 add b9',
                'description' => 'Practice identifying drop 2 b9 chord qualities by ear.',
                'category' => 'Add 9 & b9',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                ],
            ],
            [
                'title' => 'drop 2 add b9 all keys',
                'description' => 'Practice identifying drop 2 b9 chord qualities across all keys by ear.',
                'category' => 'Add 9 & b9',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3'],
                ],
            ],
            [
                'title' => 'add 9 and add b9',
                'description' => 'Practice identifying ninth chord qualities (add9, b9, dominant, minor) by ear.',
                'category' => 'Add 9 & b9',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '5'],
                ],
            ],
            [
                'title' => '7th Degree Chords',
                'description' => 'Practice identifying 7th degree chord qualities by ear.',
                'category' => '7th Degree Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                ],
            ],
            [
                'title' => 'all keys root inv',
                'description' => 'Practice identifying 7th degree chord qualities in root position across all keys by ear.',
                'category' => '7th Degree Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                ],
            ],
            [
                'title' => 'drop 2 key c root',
                'description' => 'Practice identifying drop 2 7th degree chord qualities in the key of C by ear.',
                'category' => '7th Degree Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '4'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                ],
            ],
            [
                'title' => 'drop 2 all keys root',
                'description' => 'Practice identifying drop 2 7th degree chord qualities across all keys by ear.',
                'category' => '7th Degree Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3'],
                ],
            ],
            [
                'title' => 'KEY C',
                'description' => 'Practice identifying 7th degree chord qualities and inversions in the key of C by ear.',
                'category' => '7th Degree Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores "qualityIndex,inversionIndex" for this
                // quiz: quality indexes into
                // ["Major 7th","Minor 7th","Diminished 7th","Dominant 7th","Minor 7(b5)"],
                // inversion indexes into ["Root Inversion","1st Inversion","2nd Inversion","3rd Inversion"].
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0,2'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '3,0'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2,3'],
                ],
            ],
            [
                'title' => 'ALL KEYS',
                'description' => 'Practice identifying 7th degree chord qualities and inversions across all keys by ear.',
                'category' => '7th Degree Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores "qualityIndex,inversionIndex" for this
                // quiz: quality indexes into
                // ["Major 7th","Minor 7th","Diminished 7th","Dominant 7th","Minor 7(b5)"],
                // inversion indexes into ["Root Inversion","1st Inversion","2nd Inversion","3rd Inversion"].
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1,3'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '4,1'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0,0'],
                ],
            ],
            [
                'title' => 'drop 2 key c',
                'description' => 'Practice identifying drop 2 7th degree chord qualities and inversions in the key of C by ear.',
                'category' => '7th Degree Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores "qualityIndex,inversionIndex" for this
                // quiz: quality indexes into
                // ["Major 7th","Minor 7th","Diminished 7th","Dominant 7th","Minor 7(b5)"],
                // inversion indexes into ["Root Inversion","1st Inversion","2nd Inversion","3rd Inversion"].
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2,1'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '0,3'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4,2'],
                ],
            ],
            [
                'title' => 'drop 2 all keys',
                'description' => 'Practice identifying drop 2 7th degree chord qualities and inversions across all keys by ear.',
                'category' => '7th Degree Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                // correct_option stores "qualityIndex,inversionIndex" for this
                // quiz: quality indexes into
                // ["Major 7th","Minor 7th","Diminished 7th","Dominant 7th","Minor 7(b5)"],
                // inversion indexes into ["Root Inversion","1st Inversion","2nd Inversion","3rd Inversion"].
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '3,2'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '1,0'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '4,3'],
                ],
            ],
            [
                'title' => 'key c root',
                'description' => 'Practice identifying extended seventh chord qualities in the key of C by ear.',
                'category' => 'Secondary 7th Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '3'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '1'],
                ],
            ],
            [
                'title' => 'all keys root',
                'description' => 'Practice identifying extended seventh chord qualities across all keys by ear.',
                'category' => 'Secondary 7th Chords',
                'video_url' => <<<'HTML'
<script src="https://fast.wistia.com/player.js" async></script><script src="https://fast.wistia.com/embed/gugwhti8p0.js" async type="module"></script><style>wistia-player[media-id='gugwhti8p0']:not(:defined) { background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/gugwhti8p0/swatch'); display: block; filter: blur(5px); padding-top:56.25%; }</style> <wistia-player media-id="gugwhti8p0" aspect="1.7777777777777777"></wistia-player>
HTML,
                'thumbnail_path' => '/images/eartraining.png',
                'main_audio_path' => $sharedAudio[0],
                'questions' => [
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '2'],
                    ['audio_path' => $sharedAudio[1], 'correct_option' => '4'],
                    ['audio_path' => $sharedAudio[0], 'correct_option' => '0'],
                ],
            ],
        ];

        foreach ($quizzes as $quizData) {
            $questions = $quizData['questions'];
            unset($quizData['questions']);

            // Matched on description rather than title: MySQL's default
            // collation is case-insensitive, so titles that only differ by
            // case (e.g. "Single tone Pitch" vs "Single Tone Pitch") would
            // otherwise collide and overwrite each other.
            $quiz = Quiz::updateOrCreate(
                ['description' => $quizData['description']],
                $quizData
            );

            $quiz->questions()->delete();
            $quiz->questions()->createMany($questions);
        }
    }
}
