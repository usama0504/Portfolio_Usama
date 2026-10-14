<?php

namespace App\Http\Controllers;

use App\Models\SiteContent;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Portfolio', [
            'content' => SiteContent::sections(),
            // Tells the contact form to post to this server (saved in the inbox) instead of FormSubmit.
            'contactMode' => 'server',
        ]);
    }
}
