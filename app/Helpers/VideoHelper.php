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

    public static function extractUrls(string $text): array
    {
        preg_match_all(
            '/https?:\/\/[^\s]+/i',
            $text,
            $matches
        );

        return array_unique($matches[0]);
    }

    public static function linkToEmbed(string $url): ?string
    {
        // YouTube
        if (preg_match('/youtu\.be\/([^\?]+)|youtube\.com\/watch\?v=([^\&]+)/', $url, $m)) {
            $id = $m[1] ?? $m[2];
            return "https://www.youtube.com/embed/$id";
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }

        return $url;
    }
}
