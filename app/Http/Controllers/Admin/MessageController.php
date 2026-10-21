<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReplyMail;
use App\Models\Message;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageController extends Controller
{
    private const FILTERS = ['inbox', 'unread', 'unreplied', 'replied', 'archived'];

    private const PER_PAGE = 25;

    public function index(Request $request): Response
    {
        return $this->render($request, null);
    }

    public function show(Request $request, Message $message): Response
    {
        if ($message->read_at === null) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return $this->render($request, $message);
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

    /** Back to "unread" (shows in bold again in the list). */
    public function markUnread(Request $request, Message $message): RedirectResponse
    {
        $message->forceFill(['read_at' => null])->save();

        return $this->backToList($request);
    }

    /** Move to the archive (or back to the inbox with archived=false). */
    public function archive(Request $request, Message $message): RedirectResponse
    {
        $message->forceFill([
            'archived_at' => $request->boolean('archived', true) ? now() : null,
        ])->save();

        return $this->backToList($request);
    }

    public function readAll(): RedirectResponse
    {
        Message::query()->whereNull('read_at')->update(['read_at' => now()]);

        return back();
    }

    public function destroy(Request $request, Message $message): RedirectResponse
    {
        $message->replies()->delete();
        $message->delete();

        return $this->backToList($request);
    }

    /** Download the current list (same filter and search) as a CSV file that opens in Excel. */
    public function export(Request $request): StreamedResponse
    {
        $query = $this->query($request)->latest();
        $name = 'messages-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 marker so Excel shows Urdu / special letters correctly

            fputcsv($out, ['Date', 'Name', 'Email', 'Subject', 'Message', 'Read', 'Replied', 'Archived'], ',', '"', '\\');

            foreach ($query->cursor() as $m) {
                fputcsv($out, [
                    $m->created_at?->format('Y-m-d H:i'),
                    self::csvSafe($m->name),
                    self::csvSafe($m->email),
                    self::csvSafe($m->subject),
                    self::csvSafe((string) $m->body),
                    $m->read_at ? 'Yes' : 'No',
                    $m->replies_count > 0 ? 'Yes' : 'No',
                    $m->archived_at ? 'Yes' : 'No',
                ], ',', '"', '\\');
            }

            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** A cell starting with = + - @ would be run as a formula by Excel; prefix it so it stays plain text. */
    public static function csvSafe(string $value): string
    {
        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }

    private function filter(Request $request): string
    {
        $filter = (string) $request->query('filter', 'inbox');

        return in_array($filter, self::FILTERS, true) ? $filter : 'inbox';
    }

    /** The list for the chosen tab, narrowed by the search box. */
    private function query(Request $request): Builder
    {
        $query = Message::query()->withCount('replies');

        match ($this->filter($request)) {
            'archived' => $query->whereNotNull('archived_at'),
            'unread' => $query->whereNull('archived_at')->whereNull('read_at'),
            'unreplied' => $query->whereNull('archived_at')->doesntHave('replies'),
            'replied' => $query->whereNull('archived_at')->has('replies'),
            default => $query->whereNull('archived_at'),
        };

        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $like = '%'.$search.'%';

            $query->where(function (Builder $w) use ($like) {
                $w->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('subject', 'like', $like)
                    ->orWhere('body', 'like', $like);
            });
        }

        return $query;
    }

    private function backToList(Request $request): RedirectResponse
    {
        $params = array_filter(
            $request->only(['filter', 'q']),
            fn ($value) => $value !== null && $value !== '',
        );

        return redirect()->route('admin.messages.index', $params);
    }

    /** @return array<string, int> */
    private function counts(): array
    {
        $active = fn () => Message::query()->whereNull('archived_at');

        return [
            'inbox' => $active()->count(),
            'unread' => $active()->whereNull('read_at')->count(),
            'unreplied' => $active()->doesntHave('replies')->count(),
            'replied' => $active()->has('replies')->count(),
            'archived' => Message::query()->whereNotNull('archived_at')->count(),
        ];
    }

    private function render(Request $request, ?Message $selected): Response
    {
        $messages = $this->query($request)
            ->latest()
            ->paginate(self::PER_PAGE)
            ->withQueryString()
            ->through(fn (Message $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'subject' => $m->subject,
                'preview' => Str::limit((string) preg_replace('/\s+/u', ' ', (string) $m->body), 110),
                'unread' => $m->read_at === null,
                'replied' => $m->replies_count > 0,
                'archived' => $m->archived_at !== null,
                'created_at' => $m->created_at?->toIso8601String(),
            ]);

        return Inertia::render('admin/Messages', [
            'messages' => $messages,
            'filter' => $this->filter($request),
            'q' => trim((string) $request->query('q', '')),
            'counts' => $this->counts(),
            'selected' => $selected ? [
                'id' => $selected->id,
                'name' => $selected->name,
                'email' => $selected->email,
                'subject' => $selected->subject,
                'body' => $selected->body,
                'archived' => $selected->archived_at !== null,
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
