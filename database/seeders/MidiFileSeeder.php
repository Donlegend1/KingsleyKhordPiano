<?php

namespace Database\Seeders;

use App\Models\MidiFile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MidiFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoFiles = [
            [
                'name' => 'Five Finger Independence - C Major Warmup',
                'description' => 'Slow C major independence pattern for testing the piano exercise MIDI display.',
                'file' => 'five-finger-independence-c-major.mid',
                'bpm' => 92,
                'repeats' => 10,
                'progression' => [
                    ['left' => [48, 55], 'right' => [60, 64, 67]],
                    ['left' => [50, 57], 'right' => [62, 65, 69]],
                    ['left' => [52, 59], 'right' => [64, 67, 71]],
                    ['left' => [53, 60], 'right' => [65, 69, 72]],
                ],
            ],
            [
                'name' => 'Gospel Passing Chords Guided Practice',
                'description' => 'Guided practice loop with gospel-style passing movement.',
                'file' => 'gospel-passing-chords-guided-practice.mid',
                'bpm' => 104,
                'repeats' => 12,
                'progression' => [
                    ['left' => [48, 55], 'right' => [60, 64, 67, 72]],
                    ['left' => [49, 56], 'right' => [61, 65, 68, 73]],
                    ['left' => [50, 57], 'right' => [62, 65, 69, 74]],
                    ['left' => [43, 55], 'right' => [59, 62, 65, 71]],
                ],
            ],
            [
                'name' => 'Amazing Grace - Beginner Chord Breakdown',
                'description' => 'A simple Amazing Grace chord study that matches the Learn Songs demo lesson.',
                'file' => 'amazing-grace-beginner-chord-breakdown.mid',
                'bpm' => 78,
                'repeats' => 8,
                'progression' => [
                    ['left' => [43, 55], 'right' => [59, 62, 67]],
                    ['left' => [48, 55], 'right' => [60, 64, 67]],
                    ['left' => [50, 57], 'right' => [62, 65, 69]],
                    ['left' => [43, 55], 'right' => [59, 62, 67]],
                ],
            ],
            [
                'name' => 'Way Maker - Worship Piano Breakdown',
                'description' => 'Worship chord loop that matches the seeded Way Maker Learn Songs lesson.',
                'file' => 'way-maker-worship-piano-breakdown.mid',
                'bpm' => 72,
                'repeats' => 8,
                'progression' => [
                    ['left' => [40, 52], 'right' => [56, 59, 64]],
                    ['left' => [47, 59], 'right' => [55, 59, 62]],
                    ['left' => [37, 49], 'right' => [56, 61, 64]],
                    ['left' => [45, 57], 'right' => [57, 61, 64]],
                ],
            ],
        ];

        $directory = public_path('midi-files/midi/demo');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        foreach ($demoFiles as $demoFile) {
            $path = "midi-files/midi/demo/{$demoFile['file']}";
            File::put(public_path($path), $this->makeMidiFile(
                $demoFile['progression'],
                $demoFile['bpm'],
                $demoFile['repeats']
            ));

            MidiFile::updateOrCreate(
                ['name' => $demoFile['name']],
                [
                    'description' => $demoFile['description'],
                    'video_type' => 'youtube',
                    'video_path' => 'CD-E-LDc384',
                    'midi_file_path' => $path,
                    'thumbnail_path' => 'images/music.png',
                ]
            );
        }
    }

    private function makeMidiFile(array $progression, int $bpm, int $repeats): string
    {
        $division = 480;
        $quarter = $division;
        $track = '';
        $tempo = (int) round(60000000 / $bpm);

        $track .= $this->varLen(0) . "\xFF\x51\x03" . substr(pack('N', $tempo), 1);
        $track .= $this->varLen(0) . "\xC0\x00";

        for ($repeat = 0; $repeat < $repeats; $repeat++) {
            foreach ($progression as $bar) {
                $notes = array_merge($bar['left'], $bar['right']);

                foreach ($notes as $note) {
                    $track .= $this->varLen(0) . chr(0x90) . chr($note) . chr(86);
                }

                foreach ($notes as $index => $note) {
                    $track .= $this->varLen($index === 0 ? $quarter : 0) . chr(0x80) . chr($note) . chr(0);
                }
            }
        }

        $track .= $this->varLen(0) . "\xFF\x2F\x00";

        return 'MThd' . pack('Nnnn', 6, 0, 1, $division) . 'MTrk' . pack('N', strlen($track)) . $track;
    }

    private function varLen(int $value): string
    {
        $buffer = $value & 0x7F;

        while (($value >>= 7) > 0) {
            $buffer <<= 8;
            $buffer |= (($value & 0x7F) | 0x80);
        }

        $bytes = '';

        while (true) {
            $bytes .= chr($buffer & 0xFF);

            if ($buffer & 0x80) {
                $buffer >>= 8;
            } else {
                break;
            }
        }

        return $bytes;
    }
}
