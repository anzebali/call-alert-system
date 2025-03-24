<?php

namespace App\Libraries;

use Google_Client;
use Google_Service_Calendar;

class GoogleCalendar
{
    private $client;
    
    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(APPPATH . 'Config/google_credentials.json');
        $this->client->setAccessType('offline');
        $this->client->setScopes([Google_Service_Calendar::CALENDAR_READONLY]);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getEvents($accessToken)
    {
        $this->client->setAccessToken($accessToken);
        $service = new Google_Service_Calendar($this->client);
        $calendarId = 'primary';

        $events = $service->events->listEvents($calendarId);
        return $events->getItems();
    }
}
