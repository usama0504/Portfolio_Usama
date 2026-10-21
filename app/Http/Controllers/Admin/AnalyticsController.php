<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\SiteContent;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function index(Request $request): Response
    {
        $days = (int) $request->query('days', 30);

        if (! in_array($days, [7, 30, 90], true)) {
            $days = 30;
        }

        try {
            $data = $this->build($days);
        } catch (\Throwable $e) {
            report($e);
            $data = null; // table missing → the page explains how to run the migration
        }

        return Inertia::render('admin/Analytics', [
            'days' => $days,
            'data' => $data,
            'projectNames' => $this->projectNames(),
        ]);
    }

    /** @return array<string, mixed> */
    private function build(int $days): array
    {
        $from = now()->subDays($days - 1)->startOfDay();
        $base = AnalyticsEvent::query()->where('created_at', '>=', $from);

        $daily = (clone $base)
            ->where('type', 'pageview')
            ->selectRaw('DATE(created_at) as day, COUNT(*) as views, COUNT(DISTINCT visitor) as visitors')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $series = [];
        $views = 0;
        $visitors = 0;

        for ($i = 0; $i < $days; $i++) {
            $day = $from->copy()->addDays($i)->toDateString();
            $row = $daily->get($day);
            $v = (int) ($row?->views ?? 0);
            $u = (int) ($row?->visitors ?? 0);

            $series[] = ['date' => $day, 'views' => $v, 'visitors' => $u];
            $views += $v;
            $visitors += $u;
        }

        [$prevViews, $prevVisitors] = $this->totals($from->copy()->subDays($days), $from);

        $counts = (clone $base)
            ->whereIn('type', ['cv_download', 'whatsapp_click'])
            ->selectRaw('type, COUNT(*) as c')
            ->groupBy('type')
            ->pluck('c', 'type')
            ->map(fn ($c) => (int) $c)
            ->all();

        $projects = (clone $base)
            ->where('type', 'project_view')
            ->whereNotNull('label')
            ->selectRaw('label, COUNT(*) as c')
            ->groupBy('label')
            ->orderByDesc('c')
            ->limit(8)
            ->get()
            ->map(fn ($r) => ['slug' => $r->label, 'count' => (int) $r->c])
            ->values();

        $referrers = (clone $base)
            ->where('type', 'pageview')
            ->whereNotNull('referrer')
            ->selectRaw('referrer, COUNT(*) as c')
            ->groupBy('referrer')
            ->orderByDesc('c')
            ->limit(8)
            ->get()
            ->map(fn ($r) => ['host' => $r->referrer, 'count' => (int) $r->c])
            ->values();

        $devices = (clone $base)
            ->where('type', 'pageview')
            ->whereNotNull('device')
            ->selectRaw('device, COUNT(*) as c')
            ->groupBy('device')
            ->orderByDesc('c')
            ->get()
            ->map(fn ($r) => ['device' => $r->device, 'count' => (int) $r->c])
            ->values();

        return [
            'views' => $views,
            'visitors' => $visitors,
            'prevViews' => $prevViews,
            'prevVisitors' => $prevVisitors,
            'cvDownloads' => $counts['cv_download'] ?? 0,
            'whatsappClicks' => $counts['whatsapp_click'] ?? 0,
            'series' => $series,
            'projects' => $projects,
            'referrers' => $referrers,
            'devices' => $devices,
        ];
    }

    /**
     * Page views and (daily unique) visitors in [from, to).
     *
     * @return array{0: int, 1: int}
     */
    private function totals(CarbonInterface $from, CarbonInterface $to): array
    {
        $rows = AnalyticsEvent::query()
            ->where('type', 'pageview')
            ->where('created_at', '>=', $from)
            ->where('created_at', '<', $to)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as views, COUNT(DISTINCT visitor) as visitors')
            ->groupBy('day')
            ->get();

        return [(int) $rows->sum('views'), (int) $rows->sum('visitors')];
    }

    /** @return array<string, string> slug => project name (from what you saved in the dashboard) */
    private function projectNames(): array
    {
        $projects = SiteContent::sections()['projects'] ?? [];
        $names = [];

        foreach (is_array($projects) ? $projects : [] as $project) {
            if (is_array($project) && isset($project['slug'], $project['name'])) {
                $names[(string) $project['slug']] = (string) $project['name'];
            }
        }

        return $names;
    }
}
