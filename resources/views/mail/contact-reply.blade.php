{!! $replyBody !!}

--
{!! config('mail.from.name') !!}

On {{ $original->created_at->format('M j, Y \a\t g:i A') }}, {!! $original->name !!} wrote:
@foreach (preg_split('/\r\n|\r|\n/', (string) ($original->body ?: '(no message)')) as $line)
> {!! $line !!}
@endforeach
