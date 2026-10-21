<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/** Presenta la pagina pubblica del servizio Sogiornia. */
class PageController extends Controller
{
    public function welcome(): View
    {
        return view('welcome');
    }
}
