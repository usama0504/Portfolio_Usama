<?php

namespace App\Support;

use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Records visits and clicks. It must never break the website, so every failure is swallowed. */
class Analytics
{
    /** Events the browser may report (see resources/js/lib/track.ts). */
    public const EVENTS = ['project_view', 'cv_download', 'whatsapp_click'];

    public static function pageView(Request $request): void
    {
        self::record($request, 'pageview', null, true);
    }

    public static function record(Request $request, string $type, ?string $label = null, bool $withReferrer = false): void
    {
        try {
            if (self::shouldIgnore($request)) {
                return;
            }

            AnalyticsEvent::create([
                'type' => $type,
                'label' => $label !== null ? Str::limit($label, 80, '') : null,
                'visitor' => self::visitor($request),
                'device' => self::device($request),
                'referrer' => $withReferrer ? self::referrerHost($request) : null,
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // analytics problems (e.g. table not migrated yet) are not worth an error page
        }
    }

    /** Your own visits (while signed in) and robots are not counted. */
    private static function shouldIgnore(Request $request): bool
    {
        if ($request->user()) {
            return true;
        }

        $agent = (string) $request->userAgent();

        return $agent === ''
            || (bool) preg_match('/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp|telegram|curl|wget|python|headless|lighthouse|monitor|uptime|ngrok/i', $agent);
    }

    /** Same person = same hash for one day only; the IP address itself is never stored. */
    private static function visitor(Request $request): string
    {
        $raw = $request->ip().'|'.$request->userAgent().'|'.now()->toDateString();

        return substr(hash_hmac('sha256', $raw, (string) config('app.key')), 0, 32);
    }

    private static function device(Request $request): string
    {
        $agent = strtolower((string) $request->userAgent());

        return match (true) {
            (bool) preg_match('/ipad|tablet/', $agent) => 'tablet',
            (bool) preg_match('/mobi|android|iphone|ipod/', $agent) => 'mobile',
            default => 'desktop',
        };
    }

    private static function referrerHost(Request $request): ?string
    {
        $host = parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);

        if (! is_string($host) || $host === '' || strcasecmp($host, $request->getHost()) === 0) {
            return null; // direct visit, or a click inside the site itself
        }

        return Str::limit(preg_replace('/^www\./i', '', $host), 120, '');
    }
}
