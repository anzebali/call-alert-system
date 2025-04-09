<?php
namespace App\Controllers;

use Google\Client;
use Google\Service\Oauth2;
use App\Models\UserModel;
use CodeIgniter\Controller;
use CodeIgniter\I18n\Time;

class Auth extends Controller
{
    public function login()
    {
        $client = new Client();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->addScope(['email', 'profile', 'https://www.googleapis.com/auth/calendar.readonly']);


        return redirect()->to($client->createAuthUrl());
    }

    public function callback()
    {
        $db = \Config\Database::connect();
        $client = new Client();
        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));
    
        if ($this->request->getVar('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($this->request->getVar('code'));

            //print_r($token['access_token']); die;
        
            if (!$token || isset($token['error'])) {
                echo "Failed to get access token!";
                exit;
            }
    
            session()->set('access_token', $token['access_token']);
    
            $client->setAccessToken($token);
    
            $oauth2 = new Oauth2($client);
            $userInfo = $oauth2->userinfo->get();

            
            $userModel = new UserModel();

            $apiToken = bin2hex(random_bytes(32)); // Generate a secure random token
            

            $userData = $userModel->where('google_id', $userInfo->id)->first();

            
            //print_r($userData); die;

            if(!empty($userData))
            {
                $a = [
                    'api_token' => $apiToken,
                    'access_token' => $token['access_token']
                ];
                $id = $userData['id'];
                $qr = $db->table('users');
                $qr->where('id', $id);
                $qr->update($a);
            }
            else
            {
               $id = $userModel->save([
                    'google_id' => $userInfo->id,
                    'name' => $userInfo->name,
                    'email' => $userInfo->email,
                    'api_token' => $apiToken,
                    'access_token' => $token['access_token']
                ]);
            }
            
          
            session()->set('user', [
                'userId'=>$id,
                'id'    => $userInfo->id,
                'name'  => $userInfo->name,
                'email' => $userInfo->email,
                'token' => $apiToken,
            ]);
    
            return redirect()->to('/dashboard');
        }
    }

//     public function callback()
// {
//     $client = new Client();
//     $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
//     $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
//     $client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));

//     // ✅ Must be repeated here too
//     $client->setAccessType('offline');
//     $client->setPrompt('consent');

//     $client->addScope([
//         'https://www.googleapis.com/auth/userinfo.email',
//         'https://www.googleapis.com/auth/userinfo.profile',
//         'https://www.googleapis.com/auth/calendar.readonly',
//         'openid'
//     ]);

//     // Continue with the rest of your code...
//     if ($this->request->getVar('code')) {
//         $authCode = $this->request->getVar('code');

//         $token = $client->fetchAccessTokenWithAuthCode($authCode);

//         if (!$token || isset($token['error'])) {
//             echo "Failed to get access token!";
//             log_message('error', 'Google OAuth error: ' . json_encode($token));
//             exit;
//         }

//         $client->setAccessToken($token);

//         $oauth2 = new Oauth2($client);
//         $userInfo = $oauth2->userinfo->get();

//         $userModel = new UserModel();
//         $apiToken = bin2hex(random_bytes(32));

//         $userDataArray = [
//             'google_id'     => $userInfo->id,
//             'name'          => $userInfo->name,
//             'email'         => $userInfo->email,
//             'api_token'     => $apiToken,
//             'access_token'  => json_encode($token),
//         ];

//         if (isset($token['refresh_token'])) {
//             $userDataArray['refresh_token'] = $token['refresh_token'];
//         }

//         $existingUser = $userModel->where('google_id', $userInfo->id)->first();
//         if ($existingUser) {
//             $userModel->update($existingUser['id'], $userDataArray);
//             $userId = $existingUser['id'];
//         } else {
//             $userModel->insert($userDataArray);
//             $userId = $userModel->getInsertID();
//         }

//         session()->set('user', [
//             'userId' => $userId,
//             'id'     => $userInfo->id,
//             'name'   => $userInfo->name,
//             'email'  => $userInfo->email,
//             'token'  => $apiToken,
//         ]);

//         return redirect()->to('/dashboard');
//     }
// }

    

    

    public function logout()
    {
        $db = \Config\Database::connect();
        $google_id =  session()->get('user.id');
        session()->destroy();

        $a = ['access_token' => null];
        $qr = $db->table('users');
        $qr->where('google_id', $google_id);
        $updated = $qr->update($a);

        return redirect()->to('/');
    }

    public function updatePhoneNumber()
    {
        $db = \Config\Database::connect();
        $session = session();
        $userModel = new UserModel();

        if (!$session->has('user')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $userId = $session->get('user.id');
        $phoneNumber = $this->request->getPost('phone_number');

        if (!preg_match('/^\+?[0-9]+$/', $phoneNumber)) {
            return redirect()->back()->with('error', 'Invalid phone number format.');
        }

        $a = ['phone_number' => $phoneNumber];
        $qr = $db->table('users');
        $qr->where('google_id', $userId);
        $updated = $qr->update($a);

        if (!$updated) {
            return redirect()->back()->with('error', 'Failed to update phone number.');
        }

        return redirect()->to('/dashboard')->with('success', 'Phone number updated.');
    }
}
