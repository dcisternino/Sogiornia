<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config\app;

class PageController extends Controller
{
    public function welcome()
    {
        $appName = config('app.name');
        return view('welcome', ['title' => "$appName | Welcome Page"]);
    }
    public function home_admin()
    {
        $appName = config('app.name');
        return view('pages.admin.home', ['title' => "$appName | Admin Homepage"]);
    }
    public function host_admin()
    {
        $appName = config('app.name');
        return view('pages.host.home', ['title' => "$appName | Host Homepage"]);
    }
    public function guest_admin()
    {
        $appName = config('app.name');
        return view('pages.guest.home', ['title' => "$appName | Guest Homepage"]);
    }
}
