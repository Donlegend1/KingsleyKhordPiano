<?php

namespace App\Services;

use App\Models\MidiFile;

class MidiPracticeFileResolver
{
    public function forLesson(object $lesson): ?MidiFile
    {
        $category = method_exists($lesson, 'category') ? $lesson->category : null;
        $categoryTitle = is_object($category) ? ($category->category ?? null) : null;

        $titles = collect([
            $lesson->title ?? null,
            $lesson->series ?? null,
            $categoryTitle,
        ])->filter()->unique()->values();

        if ($titles->isEmpty()) {
            return null;
        }

        foreach ($titles as $title) {
            $exact = MidiFile::whereNotNull('midi_file_path')
                ->where('name', $title)
                ->first();

            if ($exact) {
                return $exact;
            }

            $contains = MidiFile::whereNotNull('midi_file_path')
                ->where('name', 'like', '%' . $title . '%')
                ->first();

            if ($contains) {
                return $contains;
            }
        }

        $lessonTokens = $this->tokens($titles->implode(' '));

        if ($lessonTokens->isEmpty()) {
            return null;
        }

        return MidiFile::whereNotNull('midi_file_path')
            ->get()
            ->map(function (MidiFile $midiFile) use ($lessonTokens) {
                $midiTokens = $this->tokens($midiFile->name);
                $matches = $lessonTokens->intersect($midiTokens)->count();

                $midiFile->match_score = $matches / max($lessonTokens->count(), 1);

                return $midiFile;
            })
            ->filter(fn (MidiFile $midiFile) => $midiFile->match_score >= 0.5)
            ->sortByDesc('match_score')
            ->first();
    }

    private function tokens(string $value): \Illuminate\Support\Collection
    {
        return collect(preg_split('/[^a-z0-9]+/', strtolower($value)))
            ->filter(fn ($token) => strlen($token) >= 3)
            ->reject(fn ($token) => in_array($token, ['the', 'and', 'for', 'with', 'lesson', 'part'], true))
            ->unique()
            ->values();
    }
}
