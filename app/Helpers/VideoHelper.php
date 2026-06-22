<?php

namespace App\Helpers;

class VideoHelper
{
    public static function linkToEmbed(string $url): ?string
    {
        $googleDriveFileId = self::extractGoogleDriveFileId($url);

        if ($googleDriveFileId) {
            return "https://drive.google.com/file/d/{$googleDriveFileId}/preview";
        }

        // Wistia — returns the full <wistia-player> embed markup (scripts,
        // placeholder style, and custom element), not a bare iframe src,
        // since Wistia's player is a web component rather than an iframe.
        $wistiaMediaId = self::extractWistiaId($url);

        if ($wistiaMediaId) {
            return self::wistiaEmbedHtml($wistiaMediaId);
        }

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

        // Direct video file — no embed needed, flag it
        if (preg_match('/\.(mp4|webm|ogg|mov)(\?.*)?$/i', $url)) {
            return $url; // will render as <video> tag in frontend
        }

        // Unknown — return as-is (will attempt iframe)
        return $url;
    }

    public static function getLinkType(string $url): string
    {
        if (self::extractWistiaId($url)) {
            return 'wistia'; // render the raw embed HTML from linkToEmbed(), not an <iframe>
        }

        if (
            self::extractGoogleDriveFileId($url) ||
            preg_match('/youtu\.be\/|youtube\.com|vimeo\.com|dailymotion\.com|tiktok\.com|twitch\.tv|facebook\.com\/.*\/videos/', $url)
        ) {
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

    private static function extractGoogleDriveFileId(string $url): ?string
    {
        if (!str_contains($url, 'drive.google.com')) {
            return null;
        }

        if (preg_match('/drive\.google\.com\/(?:u\/\d+\/)?file\/d\/([^\/\?\&]+)/', $url, $matches)) {
            return $matches[1];
        }

        $query = parse_url($url, PHP_URL_QUERY);

        if (!$query) {
            return null;
        }

        parse_str($query, $params);

        return $params['id'] ?? null;
    }

    /**
     * Extracts a Wistia media ID from any of its common URL forms, e.g.
     * https://*.wistia.com/medias/{id}, https://fast.wistia.net/embed/iframe/{id},
     * or https://fast.wistia.net/embed/medias/{id}.jsonp.
     */
    private static function extractWistiaId(string $url): ?string
    {
        if (!str_contains($url, 'wistia.com') && !str_contains($url, 'wistia.net')) {
            return null;
        }

        if (preg_match('/wistia\.(?:com|net)\/(?:medias|embed\/iframe|embed\/medias)\/([a-zA-Z0-9]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Builds the full <wistia-player> embed markup (player script, the
     * placeholder/blur style, and the custom element) for a given media ID.
     * Unlike the other providers, Wistia's modern embed is a web component
     * rather than a plain iframe, so this returns ready-to-render HTML
     * instead of a bare src URL.
     */
    public static function wistiaEmbedHtml(string $mediaId, string $aspect = '1.7777777777777777'): string
    {
        $mediaId = htmlspecialchars($mediaId, ENT_QUOTES);

        return <<<HTML
<script src="https://fast.wistia.com/player.js" async></script>
<script src="https://fast.wistia.com/embed/{$mediaId}.js" async type="module"></script>
<style>
    wistia-player[media-id='{$mediaId}']:not(:defined) {
        background: center / contain no-repeat url('https://fast.wistia.com/embed/medias/{$mediaId}/swatch');
        display: block;
        filter: blur(5px);
        padding-top: 56.25%;
    }
</style>
<wistia-player media-id="{$mediaId}" aspect="{$aspect}"></wistia-player>
HTML;
    }
}
