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

//     public function sendReminder()
// {
//     $userModel = new UserModel();
//     $twilio = new TwilioService();
//     $allLoginUsers = $userModel->findAll();

//     if (!empty($allLoginUsers)) {
//         foreach ($allLoginUsers as $row) {
//             // Skip if missing phone number or access token
//             if (empty($row['access_token']) || empty($row['phone_number'])) {
//                 continue;
//             }

//             $storedToken1 = json_decode($row['access_token'], true);
//             $storedToken = $storedToken1['access_token'];
//             //print_r($storedToken['access_token']); die;
//             $refreshToken = $row['refresh_token'] ?? null;

//             $client = new Client();
//             $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
//             $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
//             $client->setAccessToken($storedToken);

            

//             // 🔄 Refresh token if access token is expired
//             if ($client->isAccessTokenExpired()) {
//                 if ($refreshToken) {
//                     $newToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);
//                     if (isset($newToken['access_token'])) {
//                         $userModel->update($row['id'], [
//                             'access_token' => json_encode($newToken),
//                         ]);
//                         $client->setAccessToken($newToken); // set new one for current run
//                     } else {
//                         echo "Failed to refresh token for user ID {$row['id']}"; die;
//                         log_message('error', "Failed to refresh token for user ID {$row['id']}");
//                         continue;
//                     }
//                 } else {
//                     echo "No refresh token for expired token (User ID: {$row['id']})"; die;
//                     log_message('error', "No refresh token for expired token (User ID: {$row['id']})");
//                     continue;
//                 }
//             }

//             try {
//                 $service = new Calendar($client);
//                 $events = $service->events->listEvents('primary', [
//                     'timeMin' => date('c'),
//                     'timeMax' => date('c', strtotime('+5 minutes')),
//                     'singleEvents' => true,
//                     'orderBy' => 'startTime',
//                 ]);

//                 if (empty($events->getItems())) {
//                     continue;
//                 }

                

//                 $userPhoneNumber = $row['phone_number'];

//                 foreach ($events->getItems() as $event) {
//                     $message = "Reminder: Your event '{$event->getSummary()}' starts soon!";
//                     $twilio->makeCall($userPhoneNumber, $message);
//                 }
//             } catch (\Exception $e) {
//                 log_message('error', "Google API error for user ID {$row['id']}: " . $e->getMessage());
//             }
//         }
//     }

//     return true;
// }


}
