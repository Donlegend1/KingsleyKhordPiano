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
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:tracks_loops,piano_plays',
            'audio_file' => 'required|file|mimes:mp3,wav,m4a,aac',
            'duration' => 'nullable|string',
            'file_size' => 'nullable|string',
        ]);

        if ($request->hasFile('audio_file')) {
            $audio = $request->file('audio_file');

            // New file name
            $audioName = time() . '_' . $audio->getClientOriginalName();

            // Destination path in public_html
            $destination = base_path('../public_html/uploads/audio');

            // Create directory if not exists
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Move uploaded file
            $audio->move($destination, $audioName);

            // Save clean public path
            $data['audio_file'] = "uploads/audio/$audioName";
        }

        AudioDownload::create($data);

        return response()->json([
            'message' => 'Audio uploaded successfully'
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
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

        // If new audio uploaded
        if ($request->hasFile('audio_file')) {

            // Delete old file if exists
            if (!empty($audioDownload->audio_file)) {
                $oldPath = base_path('../public_html/' . ltrim($audioDownload->audio_file, '/'));

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $audio = $request->file('audio_file');

            // New file name
            $audioName = time() . '_' . $audio->getClientOriginalName();

            // Destination
            $destination = base_path('../public_html/uploads/audio');

            // Create folder if missing
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Move file
            $audio->move($destination, $audioName);

            // Save clean public path
            $validated['audio_file'] = "uploads/audio/$audioName";
        }

        $audioDownload->update($validated);

        return response()->json([
            'message' => 'Audio updated successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(AudioDownload $audioDownload)
    // {
    //     try {
    //         if (file_exists(public_path($audioDownload->audio_file))) {
    //             unlink(public_path($audioDownload->audio_file));
    //         }

    //         $audioDownload->delete();

    //         return response()->json(['message' => 'Audio deleted successfully']);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

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
   /**
 * Helper to resolve full server path from stored relative path
 */
private function resolveFullPath(string $relativePath): string
{
    return base_path('../public_html/' . ltrim($relativePath, '/'));
}

/**
 * Remove the specified resource from storage.
 */
public function destroy(AudioDownload $audioDownload)
{
    try {
        $fullPath = $this->resolveFullPath($audioDownload->audio_file);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $audioDownload->delete();

        return response()->json(['message' => 'Audio deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

/**
 * Download audio file
 */
public function download(AudioDownload $audioDownload)
{
    $fullPath = $this->resolveFullPath($audioDownload->audio_file);

    if (file_exists($fullPath)) {
        return response()->download($fullPath);
    }

    return response()->json(['error' => 'File not found'], 404);
}
}
