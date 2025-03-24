<?php

namespace App\Controllers;

use App\Libraries\GoogleCalendar;
use CodeIgniter\Controller;
use CodeIgniter\Session\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $session = session();      
        $accessToken = $session->get('access_token');

        $googleCalendar = new GoogleCalendar();

        if (!$session->has('access_token')) {
            return redirect()->to('auth/login');
        }

        $events = $googleCalendar->getEvents($session->get('access_token'));

        return view('dashboard', ['events' => $events]);
    }
}
