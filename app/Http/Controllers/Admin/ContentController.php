<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContentController extends Controller
{
    /** Sections that can be edited from the dashboard. */
    public const SECTIONS = [
        'profile',
        'stats',
        'techStack',
        'skills',
        'experiences',
        'projects',
        'services',
        'education',
    ];

    /** Icon names the frontend knows how to draw. */
    private const ICONS = [
        'server', 'layout-template', 'database', 'wrench', 'layers',
        'code', 'globe', 'git-branch', 'sparkles',
    ];

    public function edit(): Response
    {
        return Inertia::render('admin/Dashboard', [
            'content' => SiteContent::sections(),
        ]);
    }

    public function update(Request $request, string $section): RedirectResponse
    {
        abort_unless(in_array($section, self::SECTIONS, true), 404);

        $request->merge(['data' => $this->tidyPaths($section, $request->input('data'))]);

        // validated() only returns keys that have a rule, so unknown fields are dropped.
        $validated = $request->validate($this->rules($section), [], $this->attributeNames());

        SiteContent::updateOrCreate(
            ['key' => $section],
            ['value' => $validated['data']],
        );

        return back();
    }

    /** Remove the saved version so the website falls back to the built-in default. */
    public function destroy(string $section): RedirectResponse
    {
        abort_unless(in_array($section, self::SECTIONS, true), 404);

        SiteContent::query()->where('key', $section)->delete();

        return back();
    }

    /**
     * People often type just "me.png" or "public/me.png" for a picture that
     * lives in the public folder. Turn those into "/me.png" instead of failing.
     */
    private function tidyPaths(string $section, mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        if ($section === 'profile') {
            if (isset($data['photo'])) {
                $data['photo'] = $this->tidyPath($data['photo']);
            }
        } elseif ($section === 'projects') {
            foreach ($data as $i => $project) {
                if (is_array($project) && isset($project['image'])) {
                    $data[$i]['image'] = $this->tidyPath($project['image']);
                }
            }
        }

        return $data;
    }

    private function tidyPath(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = trim(str_replace('\\', '/', $value));

        // empty, a full link, or already a path starting with "/" → leave as is
        if ($value === '' || preg_match('~^(https?://|mailto:|#|/)~i', $value)) {
            return $value;
        }

        // a Windows path like C:/Users/... cannot work on a website
        if (preg_match('~^[a-z]:/~i', $value)) {
            return $value;
        }

        $value = preg_replace('~^(\./|public/)~i', '', $value);

        return '/'.ltrim($value, '/');
    }

    /** @return array<string, string> */
    private function attributeNames(): array
    {
        return [
            'data.photo' => 'photo',
            'data.github' => 'GitHub link',
            'data.linkedin' => 'LinkedIn link',
            'data.*.image' => 'project image',
            'data.*.links.*.url' => 'project link',
        ];
    }

    /**
     * Links must be http(s), mailto, an anchor, or a path on this site —
     * never "javascript:" or similar.
     */
    private function safeUrl(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if ($value === null || $value === '') {
                return;
            }

            if (! is_string($value) || ! preg_match('~^(https?://|mailto:|#|/(?!/))~i', $value)) {
                $fail('The :attribute must be a full link starting with https:// , or a file path on this site such as /me.png or /uploads/photo.png.');
            }
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(string $section): array
    {
        $url = $this->safeUrl();
        $icon = ['required', 'string', 'in:'.implode(',', self::ICONS)];

        return match ($section) {
            'profile' => [
                'data' => ['required', 'array'],
                'data.name' => ['required', 'string', 'max:100'],
                'data.title' => ['required', 'string', 'max:120'],
                'data.summary' => ['required', 'string', 'max:600'],
                'data.location' => ['nullable', 'string', 'max:120'],
                'data.timezone' => ['nullable', 'string', 'max:60'],
                'data.email' => ['required', 'email', 'max:150'],
                'data.availability' => ['nullable', 'string', 'max:120'],
                'data.photo' => ['nullable', 'string', 'max:300', $url],
                'data.github' => ['nullable', 'string', 'max:300', $url],
                'data.linkedin' => ['nullable', 'string', 'max:300', $url],
            ],
            'stats' => [
                'data' => ['present', 'array', 'max:6'],
                'data.*.value' => ['required', 'string', 'max:20'],
                'data.*.label' => ['required', 'string', 'max:80'],
            ],
            'techStack' => [
                'data' => ['present', 'array', 'max:40'],
                'data.*' => ['required', 'string', 'max:40'],
            ],
            'skills' => [
                'data' => ['present', 'array', 'max:12'],
                'data.*.category' => ['required', 'string', 'max:60'],
                'data.*.icon' => $icon,
                'data.*.blurb' => ['nullable', 'string', 'max:200'],
                'data.*.skills' => ['present', 'array', 'max:30'],
                'data.*.skills.*.name' => ['required', 'string', 'max:50'],
                'data.*.skills.*.isCore' => ['nullable', 'boolean'],
            ],
            'experiences' => [
                'data' => ['present', 'array', 'max:15'],
                'data.*.company' => ['required', 'string', 'max:120'],
                'data.*.role' => ['required', 'string', 'max:120'],
                'data.*.startDate' => ['required', 'string', 'max:30'],
                'data.*.endDate' => ['nullable', 'string', 'max:30'],
                'data.*.location' => ['nullable', 'string', 'max:120'],
                'data.*.description' => ['present', 'array', 'max:12'],
                'data.*.description.*' => ['required', 'string', 'max:400'],
                'data.*.technologies' => ['present', 'array', 'max:20'],
                'data.*.technologies.*' => ['required', 'string', 'max:40'],
            ],
            'projects' => [
                'data' => ['present', 'array', 'max:20'],
                'data.*.slug' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9-]+$/'],
                'data.*.name' => ['required', 'string', 'max:120'],
                'data.*.category' => ['required', 'string', 'max:60'],
                'data.*.status' => ['nullable', 'string', 'in:live,demo,in-progress'],
                'data.*.image' => ['nullable', 'string', 'max:300', $url],
                'data.*.overview' => ['required', 'string', 'max:1200'],
                'data.*.problem' => ['nullable', 'string', 'max:600'],
                'data.*.role' => ['nullable', 'string', 'max:100'],
                'data.*.technologies' => ['present', 'array', 'max:20'],
                'data.*.technologies.*' => ['required', 'string', 'max:40'],
                'data.*.features' => ['present', 'array', 'max:12'],
                'data.*.features.*' => ['required', 'string', 'max:300'],
                'data.*.outcome' => ['nullable', 'string', 'max:600'],
                'data.*.links' => ['present', 'array', 'max:4'],
                'data.*.links.*.type' => ['required', 'string', 'in:live,repository'],
                'data.*.links.*.url' => ['required', 'string', 'max:300', $url],
            ],
            'services' => [
                'data' => ['present', 'array', 'max:8'],
                'data.*.title' => ['required', 'string', 'max:100'],
                'data.*.description' => ['required', 'string', 'max:300'],
                'data.*.icon' => $icon,
            ],
            'education' => [
                'data' => ['present', 'array', 'max:10'],
                'data.*.institution' => ['required', 'string', 'max:150'],
                'data.*.qualification' => ['required', 'string', 'max:150'],
                'data.*.startDate' => ['required', 'string', 'max:30'],
                'data.*.endDate' => ['required', 'string', 'max:30'],
                'data.*.description' => ['nullable', 'string', 'max:400'],
            ],
        };
    }
}
