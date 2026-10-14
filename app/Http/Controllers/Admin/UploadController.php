<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Save an uploaded picture into public/uploads and return its URL path.
     * SVG is intentionally not allowed (it can contain scripts).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $file = $request->file('file');
        $name = Str::uuid()->toString().'.'.$file->extension();

        $file->move(public_path('uploads'), $name);

        return response()->json(['url' => '/uploads/'.$name]);
    }
}
