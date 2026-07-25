@php
    $midiPracticeFile = $midiPracticeFile ?? null;
    $midiPracticeFiles = collect($midiPracticeFiles ?? []);
    $midiPracticeData = [
        'title' => $midiPracticeTitle ?? 'MIDI Practice',
        'selectedFileId' => $midiPracticeFile?->id,
        'files' => $midiPracticeFiles->map(fn ($file) => [
            'id' => $file->id,
            'name' => $file->name,
            'description' => $file->description,
            'midiUrl' => $file->midi_file_path
                ? asset($file->midi_file_path) . '?v=' . optional($file->updated_at)->timestamp
                : null,
            'thumbnailUrl' => $file->thumbnail_path ? asset($file->thumbnail_path) : null,
        ])->values(),
    ];
@endphp

<div
    class="midi-practice-player mt-8 mb-8"
    data-midi-practice='@json($midiPracticeData, JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG)'
></div>
