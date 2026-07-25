<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ZoomService
{
    public function getAccessToken()
    {
        // Cache token to avoid repeated calls
        return Cache::remember('zoom_access_token', 3500, function () {
            $clientId = config('services.zoom.client_id');
            $clientSecret = config('services.zoom.client_secret');
            $accountId = config('services.zoom.account_id');

            $basicAuth = base64_encode("$clientId:$clientSecret");

            $response = Http::withHeaders([
                'Authorization' => "Basic $basicAuth",
                'Accept' => 'application/json',
            ])->asForm()->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to retrieve Zoom access token');
            }


            return $response->json();
        });
    }

    public function createMeeting(array $payload)
    {
        $tokenData = $this->getAccessToken();
        $accessToken = $tokenData['access_token'];

        $response = Http::withToken($accessToken)->post('https://api.zoom.us/v2/users/me/meetings', [
            'topic' => $payload['topic'],
            'type' => 2,
            'start_time' => $payload['start_time'], // Format: 2025-06-01T15:00:00Z
            'duration' => $payload['duration'], // in minutes
            'timezone' => $payload['timezone'] ?? 'UTC',
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
                'waiting_room' => true,
            ],
        ]);

        if ($response->failed()) {
            Log::error('Zoom meeting creation failed: ' . $response->body());
            throw new \Exception('Zoom meeting creation failed: ' . $response->body());
        }

        return $response->json();
    }
}
