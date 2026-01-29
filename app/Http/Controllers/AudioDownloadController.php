<?php

namespace App\Http\Controllers;

use App\Models\AudioDownload;
use App\Http\Requests\StoreAudioDownloadRequest;
use Illuminate\Http\Request;

class AudioDownloadController extends Controller
{

    public function index()
    {

        return view('admin.audio-download.index');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'category' => 'required|in:tracks_loops,piano_plays',
                'audio_file' => 'required|file|mimes:mp3,wav,m4a,aac',
                'duration' => 'nullable|string',
                'file_size' => 'nullable|string',
            ]);

            $audioFile = $request->file('audio_file');
            $audioFileName = time() . '_' . $audioFile->getClientOriginalName();
            $audioFile->move(public_path('uploads/audio'), $audioFileName);
            $validated['audio_file'] = 'uploads/audio/' . $audioFileName;

            AudioDownload::create($validated);

            return response()->json(['message' => 'Audio uploaded successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AudioDownload $audioDownload)
    {
        return response()->json($audioDownload);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AudioDownload $audioDownload)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'category' => 'required|in:tracks_loops,piano_plays',
                'audio_file' => 'nullable|file|mimes:mp3,wav,m4a,aac',
                'duration' => 'nullable|string',
                'file_size' => 'nullable|string',
            ]);

            if ($request->hasFile('audio_file')) {
                if (file_exists(public_path($audioDownload->audio_file))) {
                    unlink(public_path($audioDownload->audio_file));
                }

                $audioFile = $request->file('audio_file');
                $audioFileName = time() . '_' . $audioFile->getClientOriginalName();
                $audioFile->move(public_path('uploads/audio'), $audioFileName);
                $validated['audio_file'] = 'uploads/audio/' . $audioFileName;
            }

            $audioDownload->update($validated);

            return response()->json(['message' => 'Audio updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AudioDownload $audioDownload)
    {
        try {
            if (file_exists(public_path($audioDownload->audio_file))) {
                unlink(public_path($audioDownload->audio_file));
            }

            $audioDownload->delete();

            return response()->json(['message' => 'Audio deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all audio downloads
     */
    public function fetchAll()
    {
        $audioDownloads = AudioDownload::all();
        return response()->json($audioDownloads);
    }

    /**
     * Download audio file
     */
    public function download(AudioDownload $audioDownload)
    {
        $filePath = public_path($audioDownload->audio_file);
        
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
