<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/** Presenta la dashboard essenziale; calendario e riepiloghi sono nella pagina Soggiorni. */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.host.home');
    }
}
