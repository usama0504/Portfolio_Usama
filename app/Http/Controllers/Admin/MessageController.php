<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function index(): Response
    {
        return $this->render(null);
    }

    public function show(Message $message): Response
    {
        if ($message->read_at === null) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return $this->render($message);
    }

    public function reply(Request $request, Message $message): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        try {
            Mail::to($message->email)->send(new ContactReplyMail($message, $data['body']));
        } catch (\Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'body' => 'The email could not be sent: '.Str::limit($e->getMessage(), 220)
                    .' — check the Gmail settings in your .env file (see MESSAGES.md).',
            ]);
        }

        $message->replies()->create(['body' => $data['body']]);

        return back();
    }

    public function destroy(Message $message): RedirectResponse
    {
        $message->replies()->delete();
        $message->delete();

        return redirect()->route('admin.messages.index');
    }

    private function render(?Message $selected): Response
    {
        $messages = Message::query()
            ->withCount('replies')
            ->latest()
            ->limit(200)
            ->get()
            ->map(fn (Message $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'subject' => $m->subject,
                'preview' => Str::limit((string) preg_replace('/\s+/u', ' ', (string) $m->body), 110),
                'unread' => $m->read_at === null,
                'replied' => $m->replies_count > 0,
                'created_at' => $m->created_at?->toIso8601String(),
            ]);

        return Inertia::render('admin/Messages', [
            'messages' => $messages,
            'selected' => $selected ? [
                'id' => $selected->id,
                'name' => $selected->name,
                'email' => $selected->email,
                'subject' => $selected->subject,
                'body' => $selected->body,
                'created_at' => $selected->created_at?->toIso8601String(),
                'replies' => $selected->replies->map(fn ($r) => [
                    'id' => $r->id,
                    'body' => $r->body,
                    'created_at' => $r->created_at?->toIso8601String(),
                ])->values(),
            ] : null,
        ]);
    }
}
