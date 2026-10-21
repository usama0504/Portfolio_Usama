<?php

namespace App\Http\Controllers;

use App\Models\SiteContent;
use App\Support\Analytics;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function show(Request $request): Response
    {
        Analytics::pageView($request);

        return Inertia::render('Portfolio', [
            'content' => SiteContent::sections(),
            // Tells the contact form to post to this server (saved in the inbox) instead of FormSubmit.
            'contactMode' => 'server',
        ]);
    }
}
