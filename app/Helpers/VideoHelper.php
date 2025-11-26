<?php

namespace App\Helpers;

class VideoHelper
{
    public static function extractYoutubeId($url)
    {
        if (!$url) return null;

        // Handles full YouTube URL, short URL, embed URL, etc.
        preg_match(
            '/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&?\/]+)/',
            $url,
            $matches
        );

        return $matches[1] ?? $url; // if it's already an ID, keep it
    }

    public static function extractGoogleDriveId($url)
    {
        if (!$url) return null;

        // Handles standard Drive format
        preg_match('/\/d\/(.*?)\//', $url, $matches);

        return $matches[1] ?? $url; // if already ID, keep it
    }
}
