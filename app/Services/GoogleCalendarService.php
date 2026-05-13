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
