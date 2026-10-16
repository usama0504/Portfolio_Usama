<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /** Your account details + the two forms. */
    public function edit(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('admin/Profile', [
            'account' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
        ]);
    }

    /** Change name / login email (changing the email needs your current password). */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'current_password' => [
                Rule::requiredIf(fn () => $request->input('email') !== $user->email),
                'nullable',
                'current_password',
            ],
        ]);

        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
        ])->save();

        return back()->with('status', 'Your details have been updated.');
    }

    /** Change the password (needs the current one). */
    public function password(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ]);

        // The User model hashes the password automatically ("hashed" cast).
        $request->user()->forceFill([
            'password' => $data['password'],
            'remember_token' => Str::random(60),
        ])->save();

        return back()->with('status', 'Your password has been changed.');
    }
}
