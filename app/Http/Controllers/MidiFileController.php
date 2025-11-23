<?php

namespace App\Http\Controllers;

use App\Models\MidiFile;
use App\Http\Requests\StoreMidiFileRequest;
use App\Http\Requests\UpdateMidiFileRequest;

class MidiFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.midi-file.index', [
            'midiFiles' => MidiFile::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMidiFileRequest $request)
    {
        $data = $request->validated();

        // Ensure directories exist
        $midiDir = public_path('midi-files/midi');
        $lmvDir = public_path('midi-files/lmv');
        $thumbDir = public_path('midi-files/thumbnails');

        if (!is_dir($midiDir)) mkdir($midiDir, 0777, true);
        if (!is_dir($lmvDir)) mkdir($lmvDir, 0777, true);
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0777, true);

        /* -------------------- MIDI FILE -------------------- */
        if ($request->hasFile('midi_file')) {
            $midiName = time() . '_midi.' . $request->file('midi_file')->getClientOriginalExtension();
            $request->file('midi_file')->move($midiDir, $midiName);

            $data['midi_file_path'] = 'midi-files/midi/' . $midiName;
        }

        /* -------------------- THUMBNAIL FILE -------------------- */
        if ($request->hasFile('thumbnail')) {
            $thumbnailName = time() . '_thumbnail.' . $request->file('thumbnail')->getClientOriginalExtension();
            $request->file('thumbnail')->move($thumbDir, $thumbnailName);

            $data['thumbnail_path'] = 'midi-files/thumbnails/' . $thumbnailName;
        }

        /* -------------------- LMV FILE -------------------- */
        if ($request->hasFile('lmv_file')) {
            $lmvName = time() . '_lmv.' . $request->file('lmv_file')->getClientOriginalExtension();
            $request->file('lmv_file')->move($lmvDir, $lmvName);

            $data['lmv_file_path'] = 'midi-files/lmv/' . $lmvName;
        }

        // Create database record
        $file = MidiFile::create($data);

        return response()->json($file, 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMidiFileRequest $request, MidiFile $midiFile)
    {
        $data = $request->validated();

        $midiDir = public_path('midi-files/midi');
        $lmvDir = public_path('midi-files/lmv');
        $thumbDir = public_path('midi-files/thumbnails');

        if (!is_dir($midiDir)) mkdir($midiDir, 0777, true);
        if (!is_dir($lmvDir)) mkdir($lmvDir, 0777, true);
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0777, true);

        /* -------------------- UPDATE MIDI FILE -------------------- */
        if ($request->hasFile('midi_file')) {

            if ($midiFile->midi_file_path && file_exists(public_path($midiFile->midi_file_path))) {
                unlink(public_path($midiFile->midi_file_path));
            }

            $midiName = time() . '_midi.' . $request->file('midi_file')->getClientOriginalExtension();
            $request->file('midi_file')->move($midiDir, $midiName);

            $data['midi_file_path'] = 'midi-files/midi/' . $midiName;
        }

        /* -------------------- UPDATE LMV FILE -------------------- */
        if ($request->hasFile('lmv_file')) {

            if ($midiFile->lmv_file_path && file_exists(public_path($midiFile->lmv_file_path))) {
                unlink(public_path($midiFile->lmv_file_path));
            }

            $lmvName = time() . '_lmv.' . $request->file('lmv_file')->getClientOriginalExtension();
            $request->file('lmv_file')->move($lmvDir, $lmvName);

            $data['lmv_file_path'] = 'midi-files/lmv/' . $lmvName;
        }

        /* -------------------- UPDATE THUMBNAIL -------------------- */
        if ($request->hasFile('thumbnail')) {

            if ($midiFile->thumbnail_path && file_exists(public_path($midiFile->thumbnail_path))) {
                unlink(public_path($midiFile->thumbnail_path));
            }

            $thumbnailName = time() . '_thumbnail.' . $request->file('thumbnail')->getClientOriginalExtension();
            $request->file('thumbnail')->move($thumbDir, $thumbnailName);

            $data['thumbnail_path'] = 'midi-files/thumbnails/' . $thumbnailName;
        }

        $midiFile->update($data);

        return response()->json($midiFile, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MidiFile $midiFile)
    {
        // Delete MIDI file
        if ($midiFile->midi_file_path && file_exists(public_path($midiFile->midi_file_path))) {
            unlink(public_path($midiFile->midi_file_path));
        }

        // Delete MKV file
        if ($midiFile->lmv_file_path && file_exists(public_path($midiFile->lmv_file_path))) {
            unlink(public_path($midiFile->lmv_file_path));
        }

        // Thumbnail (if stored in public)
        if ($midiFile->thumbnail_path && file_exists(public_path($midiFile->thumbnail_path))) {
            unlink(public_path($midiFile->thumbnail_path));
        }

        $midiFile->delete();
        return  response()->json($midiFile, 200);
    }

    /**
     * Fetch all MIDI files (for API).
     */
    public function fetchAll()
    {
        return response()->json(MidiFile::all());

    }
}
