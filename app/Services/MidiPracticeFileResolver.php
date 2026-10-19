<?php

namespace App\Services;

use App\Models\MidiFile;

class MidiPracticeFileResolver
{
    public function forLesson(object $lesson): ?MidiFile
    {
        if (! empty($lesson->midi_resource)) {
            $uploaded = new MidiFile;
            $uploaded->incrementing = false;
            $uploaded->id = 'lesson-'.$lesson->id;
            $uploaded->name = $lesson->title;
            $uploaded->description = $lesson->description ?? null;
            $uploaded->midi_file_path = $lesson->midi_resource;
            $uploaded->thumbnail_path = $lesson->thumbnail ?? null;
            $uploaded->updated_at = $lesson->updated_at;

            return $uploaded;
        }

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

        // No exact or "contains" match: show no MIDI rather than guessing from
        // shared words, which could attach another piece's MIDI to the lesson.
        return null;
    }
}
