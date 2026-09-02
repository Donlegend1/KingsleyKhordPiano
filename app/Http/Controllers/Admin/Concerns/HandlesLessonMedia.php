<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Helpers\VideoHelper;
use Illuminate\Http\Request;

trait HandlesLessonMedia
{
    protected function storePublicFile($file, string $subdir): string
    {
        $filename = time().'_'.uniqid().'_'.$file->getClientOriginalName();
        $destination = base_path('../public_html/uploads/'.$subdir);
        if (! file_exists($destination)) {
            $destination = public_path('uploads/'.$subdir);
        }
        if (! file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
        $file->move($destination, $filename);

        return 'uploads/'.$subdir.'/'.$filename;
    }

    protected function deletePublicFile(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }

    protected function processVideoUrl(?string $videoType, ?string $videoUrl, string $mode = 'id'): ?string
    {
        if (! $videoUrl) {
            return $videoUrl;
        }

        if ($mode === 'embed') {
            return VideoHelper::linkToEmbed($videoUrl) ?: $videoUrl;
        }

        if ($videoType === 'youtube') {
            return VideoHelper::extractYoutubeId($videoUrl) ?: $videoUrl;
        }

        if ($videoType === 'google') {
            return VideoHelper::extractGoogleDriveId($videoUrl) ?: $videoUrl;
        }

        return $videoUrl;
    }

    protected function collectMediaFromRequest(Request $request, $existing = null, bool $withImages = true): array
    {
        $thumbnailPath = $existing?->thumbnail;
        if ($request->hasFile('thumbnail')) {
            $this->deletePublicFile($existing?->thumbnail);
            $thumbnailPath = $this->storePublicFile($request->file('thumbnail'), 'thumbnails');
        }

        $descriptionImages = $existing?->images ?? [];
        if ($withImages && $request->hasFile('images')) {
            if (is_array($existing?->images)) {
                foreach ($existing->images as $oldImg) {
                    $this->deletePublicFile($oldImg);
                }
            }
            $descriptionImages = [];
            foreach ($request->file('images') as $imgFile) {
                $descriptionImages[] = $this->storePublicFile($imgFile, 'descriptions');
            }
        }

        $audioResourcePath = $existing?->audio_resource;
        if ($request->hasFile('audio_resource')) {
            $this->deletePublicFile($existing?->audio_resource);
            $audioResourcePath = $this->storePublicFile($request->file('audio_resource'), 'resources/audio');
        }

        $pdfResourcePath = $existing?->pdf_resource;
        if ($request->hasFile('pdf_resource')) {
            $this->deletePublicFile($existing?->pdf_resource);
            $pdfResourcePath = $this->storePublicFile($request->file('pdf_resource'), 'resources/pdf');
        }

        $midiResourcePath = $existing?->midi_resource;
        if ($request->hasFile('midi_resource')) {
            $this->deletePublicFile($existing?->midi_resource);
            $midiResourcePath = $this->storePublicFile($request->file('midi_resource'), 'resources/midi');
        }

        return [
            'thumbnail' => $thumbnailPath,
            'images' => $descriptionImages,
            'audio_resource' => $audioResourcePath,
            'pdf_resource' => $pdfResourcePath,
            'midi_resource' => $midiResourcePath,
        ];
    }
}
