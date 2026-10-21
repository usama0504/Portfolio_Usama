<?php

namespace App\Http\Controllers;

use App\Support\Analytics;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TrackController extends Controller
{
    /** Clicks reported by the website (project opened, CV downloaded, WhatsApp clicked). */
    public function store(Request $request): Response
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'in:'.implode(',', Analytics::EVENTS)],
            'label' => ['nullable', 'string', 'max:80', 'regex:/^[A-Za-z0-9._:\- ]*$/'],
        ]);

        Analytics::record($request, $data['type'], $data['label'] ?? null);

        return response()->noContent();
    }
}
