@php($seo = \App\Support\Seo::data())
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: #0a1017;
            }

            html.light {
                background-color: #f3f7fa;
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ $seo['title'] }}</title>
            @if (str_starts_with($page['component'], 'admin/'))
                <meta name="robots" content="noindex, nofollow">
            @else
                <meta name="description" content="{{ $seo['description'] }}">
                <link rel="canonical" href="{{ $seo['url'] }}">
                <meta property="og:type" content="website">
                <meta property="og:site_name" content="{{ config('app.name') }}">
                <meta property="og:title" content="{{ $seo['title'] }}">
                <meta property="og:description" content="{{ $seo['description'] }}">
                <meta property="og:url" content="{{ $seo['url'] }}">
                <meta property="og:image" content="{{ $seo['image'] }}">
                <meta name="twitter:card" content="summary_large_image">
                <meta name="twitter:title" content="{{ $seo['title'] }}">
                <meta name="twitter:description" content="{{ $seo['description'] }}">
                <meta name="twitter:image" content="{{ $seo['image'] }}">
            @endif
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
