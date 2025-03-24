<?php
namespace App\Libraries;

use Twilio\Rest\Client;

class TwilioService
{
    private $client;
    private $from;

    public function __construct()
    {
        $sid = getenv('TWILIO_SID');
        $token = getenv('TWILIO_AUTH_TOKEN');
        $this->from = getenv('TWILIO_PHONE_NUMBER');

        $this->client = new Client($sid, $token);
    }

    public function makeCall($to, $message)
    {
        $twiml = '<Response><Say>' . htmlspecialchars($message) . '</Say></Response>';

        return $this->client->calls->create(
            $to,
            $this->from,
            ['twiml' => $twiml]
        );
    }
}
