<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetController extends Controller
{
    /** "Forgot password?" form. */
    public function create(): Response
    {
        return Inertia::render('admin/ForgotPassword');
    }

    /** Email the reset link (through the Gmail settings in .env). */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        try {
            Password::sendResetLink($data);
        } catch (\Throwable $e) {
            report($e);

            // While developing, show what is wrong (usually the Gmail settings).
            // In production the answer stays the same so nobody can find out which email is the admin's.
            if (config('app.debug')) {
                throw ValidationException::withMessages([
                    'email' => 'The email could not be sent: '.Str::limit($e->getMessage(), 200)
                        .' — check the Gmail settings in .env (see MESSAGES.md).',
                ]);
            }
        }

        return back()->with('status', 'If that email belongs to the admin account, a password reset link has been sent. It is valid for 60 minutes.');
    }

    /** The page opened from the link in the email. */
    public function edit(Request $request, string $token): Response
    {
        return Inertia::render('admin/ResetPassword', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    /** Save the new password. */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset($data, function (User $user, string $password) {
            // The User model hashes the password automatically ("hashed" cast).
            $user->forceFill([
                'password' => $password,
                'remember_token' => Str::random(60), // signs out "keep me signed in" devices
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PasswordReset) {
            $message = match ($status) {
                Password::InvalidUser => 'No admin account has this email address. Use the same email the reset link was sent to.',
                Password::ResetThrottled => 'Please wait a minute and try again.',
                default => 'This reset link is invalid, already used, or expired (links last 60 minutes, and only the newest email works). Please request a new link.',
            };

            throw ValidationException::withMessages(['email' => $message]);
        }

        // If someone was signed in on this browser, sign them out so the new password is used.
        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('admin.login')
            ->with('status', 'Your password has been changed. You can sign in now.');
    }
}
