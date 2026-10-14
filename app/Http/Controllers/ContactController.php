<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactFormMail;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse|RedirectResponse
    {
        // Honeypot: bots fill the hidden field. Pretend it worked, store nothing.
        if ($request->filled('botcheck')) {
            return $this->accepted($request);
        }

        $data = $request->validated();
        $name = $this->singleLine($data['name']);
        $topic = $this->singleLine($data['subject']);
        $body = isset($data['message']) ? trim((string) $data['message']) : null;

        // 1) Keep the message in the dashboard inbox (fails quietly if `migrate` was not run yet).
        $saved = null;

        try {
            $saved = Message::create([
                'name' => $name,
                'email' => $data['email'],
                'subject' => $topic,
                'body' => $body ?: null,
                'ip' => $request->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        // 2) Email it to your Gmail (a mail problem must never lose the message).
        $mailed = false;
        $to = config('portfolio.contact_email');

        if ($to) {
            try {
                Mail::to($to)->send(new ContactFormMail(
                    senderName: $name,
                    senderEmail: $data['email'],
                    topic: $topic,
                    body: $body ?: null,
                    dashboardUrl: $saved ? route('admin.messages.show', $saved) : null,
                ));
                $mailed = true;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if (! $saved && ! $mailed) {
            return $request->expectsJson()
                ? response()->json(['message' => 'The message could not be sent.'], 500)
                : back()->withErrors(['message' => 'The message could not be sent. Please try again later.']);
        }

        return $this->accepted($request);
    }

    private function accepted(Request $request): JsonResponse|RedirectResponse
    {
        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : back()->with('success', 'Message sent successfully!');
    }

    /** Collapse line breaks and repeated spaces (keeps email headers clean). */
    private function singleLine(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }
}
