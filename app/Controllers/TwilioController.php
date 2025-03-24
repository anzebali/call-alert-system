<?php
namespace App\Controllers;

use App\Libraries\TwilioService;
use App\Libraries\GoogleCalendar;
use Google\Client;
use Google\Service\Calendar;
use App\Models\UserModel;

class TwilioController extends BaseController
{
    public function sendReminder()
    {

        $userModel = new UserModel();

        $allLoginUsers = $userModel->findAll();
        
        $twilio = new TwilioService();

        if(!empty($allLoginUsers))
        {
            foreach($allLoginUsers as $key => $row)
            {

                if(!empty($row['access_token']) && !empty($row['phone_number']))
                {                   
                    $access_token = $row['access_token'];

                    $client = new Client();
                    $client->setAccessToken($access_token);


                    $client->setAccessToken($access_token);
                    $service = new Calendar($client);

                    
                   
                    $events = $service->events->listEvents('primary', [
                        'timeMin' => date('c'),
                        'timeMax' => date('c', strtotime('+5 minutes')),
                        'singleEvents' => true,
                        'orderBy' => 'startTime',
                    ]);                 

                    if (empty($events->getItems())) {
                       continue;
                    }

                    $userPhoneNumber = $row['phone_number'];

                    foreach ($events->getItems() as $event) {
                        $message = "Reminder: Your event '{$event->getSummary()}' starts soon!";
                        $twilio->makeCall($userPhoneNumber, $message);
                    }
                }
            }
        }

        return true;
        
    }
}
