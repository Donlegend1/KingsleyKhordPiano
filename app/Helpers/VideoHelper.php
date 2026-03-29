<?php

namespace App\Helpers;

class VideoHelper
{
    public static function linkToEmbed(string $url): ?string
    {
        // YouTube
        // if (preg_match('/youtu\.be\/([^\?]+)|youtube\.com\/watch\?v=([^\&]+)/', $url, $m)) {
        //     $id = $m[1] ?? $m[2];
        //     return "https://www.youtube.com/embed/$id?autoplay=0&rel=0";
        // }

            if (preg_match('/youtu\.be\/([^\?]+)|youtube\.com\/watch\?v=([^\&]+)/', $url, $m)) {
            $id = !empty($m[1]) ? $m[1] : ($m[2] ?? null);  
            if ($id) {
                return "https://www.youtube.com/embed/$id?autoplay=0&rel=0";
            }
        }

        // YouTube Shorts
        if (preg_match('/youtube\.com\/shorts\/([^\?&]+)/', $url, $m)) {
            $id = !empty($m[1]) ? $m[1] : ($m[2] ?? null);  
            if ($id) {
                return "https://www.youtube.com/embed/{$m[1]}";
            }
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }

        // Dailymotion
        if (preg_match('/dailymotion\.com\/video\/([^\?&]+)/', $url, $m)) {
            return "https://www.dailymotion.com/embed/video/{$m[1]}";
        }

        // Facebook video
        if (preg_match('/facebook\.com\/.+\/videos\/(\d+)/', $url, $m)) {
            return "https://www.facebook.com/plugins/video.php?href=" . urlencode($url);
        }

        // Twitch clip
        if (preg_match('/clips\.twitch\.tv\/([^\?&]+)/', $url, $m)) {
            return "https://clips.twitch.tv/embed?clip={$m[1]}&parent=yourdomain.com";
        }

        // Twitch channel
        if (preg_match('/twitch\.tv\/([^\?&\/]+)$/', $url, $m)) {
            return "https://player.twitch.tv/?channel={$m[1]}&parent=yourdomain.com";
        }

        // TikTok
        if (preg_match('/tiktok\.com\/.+\/video\/(\d+)/', $url, $m)) {
            return "https://www.tiktok.com/embed/v2/{$m[1]}";
        }

        // Google Drive
        if (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/', $url, $m)) {
            return "https://drive.google.com/file/d/{$m[1]}/preview";
        }

        // Direct video file — no embed needed, flag it
        if (preg_match('/\.(mp4|webm|ogg|mov)(\?.*)?$/i', $url)) {
            return $url; // will render as <video> tag in frontend
        }

        // Unknown — return as-is (will attempt iframe)
        return $url;
    }

    public static function getLinkType(string $url): string
    {
        if (preg_match('/youtu\.be\/|youtube\.com|vimeo\.com|dailymotion\.com|tiktok\.com|twitch\.tv|facebook\.com\/.*\/videos/', $url)) {
            return 'embed'; // use <iframe>
        }

        if (preg_match('/\.(mp4|webm|ogg|mov)(\?.*)?$/i', $url)) {
            return 'video'; // use <video> tag
        }

        if (preg_match('/\.(mp3|wav|ogg|aac)(\?.*)?$/i', $url)) {
            return 'audio'; // use <audio> tag
        }

        return 'iframe'; // generic iframe or modal
    }
}
