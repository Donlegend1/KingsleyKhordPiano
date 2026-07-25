<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventConferenceData;
use Google\Service\Calendar\ConferenceSolutionKey;
use Google\Service\Calendar\CreateConferenceRequest;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->client->addScope(Calendar::CALENDAR_EVENTS);
        $this->client->setAccessType('offline');
        
        $this->service = new Calendar($this->client);
    }

    /**
     * Create a plain calendar event (no auto-generated Meet link), used for
     * live coaching bookings which run over the studio's existing Zoom room.
     */
    public function createEvent($details)
    {
        try {
            $timeZone = $details['timezone'] ?? config('app.timezone', 'UTC');

            $event = new Event([
                'summary' => $details['title'],
                'description' => $details['description'] ?? '',
                'start' => [
                    'dateTime' => $details['start_time'],
                    'timeZone' => $timeZone,
                ],
                'end' => [
                    'dateTime' => $details['end_time'],
                    'timeZone' => $timeZone,
                ],
            ]);

            $calendarId = config('services.google.calendar_id', 'primary');

            $createdEvent = $this->service->events->insert($calendarId, $event);

            return [
                'event_id' => $createdEvent->id,
                'html_link' => $createdEvent->htmlLink,
            ];
        } catch (\Exception $e) {
            Log::error('Google Calendar event creation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function createMeeting($details)
    {
        try {
            $event = new Event([
                'summary' => $details['topic'],
                'description' => $details['description'] ?? '',
                'start' => [
                    'dateTime' => $details['start_time'],
                    'timeZone' => config('app.timezone', 'UTC'),
                ],
                'end' => [
                    'dateTime' => date('Y-m-d\TH:i:s\Z', strtotime($details['start_time'] . ' + ' . ($details['duration'] ?? 60) . ' minutes')),
                    'timeZone' => config('app.timezone', 'UTC'),
                ],
                'conferenceData' => new EventConferenceData([
                    'createRequest' => new CreateConferenceRequest([
                        'requestId' => uniqid(),
                        'conferenceSolutionKey' => new ConferenceSolutionKey([
                            'type' => 'hangoutsMeet'
                        ])
                    ])
                ])
            ]);

            $calendarId = config('services.google.calendar_id', 'primary');
            $optParams = ['conferenceDataVersion' => 1];
            
            $createdEvent = $this->service->events->insert($calendarId, $event, $optParams);

            return [
                'event_id' => $createdEvent->id,
                'meet_link' => $createdEvent->hangoutLink,
            ];
        } catch (\Exception $e) {
            Log::error('Google Calendar meeting creation failed: ' . $e->getMessage());
            return null;
        }
    }
}
