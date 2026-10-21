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
        'seo',
        'stats',
        'techStack',
        'skills',
        'experiences',
        'projects',
        'testimonials',
        'certificates',
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
            foreach (['photo', 'cv'] as $key) {
                if (isset($data[$key])) {
                    $data[$key] = $this->tidyPath($data[$key]);
                }
            }

            if (isset($data['whatsapp'])) {
                $data['whatsapp'] = $this->tidyPhone($data['whatsapp']);
            }
        } elseif ($section === 'seo') {
            if (isset($data['image'])) {
                $data['image'] = $this->tidyPath($data['image']);
            }
        } elseif ($section === 'testimonials' || $section === 'certificates') {
            $key = $section === 'testimonials' ? 'photo' : 'image';

            foreach ($data as $i => $item) {
                if (is_array($item) && isset($item[$key])) {
                    $data[$i][$key] = $this->tidyPath($item[$key]);
                }
            }
        } elseif ($section === 'projects') {
            foreach ($data as $i => $project) {
                if (! is_array($project)) {
                    continue;
                }

                if (isset($project['image'])) {
                    $data[$i]['image'] = $this->tidyPath($project['image']);
                }

                if (isset($project['gallery']) && is_array($project['gallery'])) {
                    $data[$i]['gallery'] = array_map(fn ($path) => $this->tidyPath($path), $project['gallery']);
                }
            }
        }

        return $data;
    }

    /** "0300 1234567", "+92 300 1234567" or "923001234567" → 923001234567 (what wa.me needs). */
    private function tidyPhone(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $digits = (string) preg_replace('/\D+/', '', $value);

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = '92'.substr($digits, 1); // Pakistani local number
        }

        return $digits;
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
            'data.whatsapp' => 'WhatsApp number',
            'data.*.photo' => 'photo',
            'data.cv' => 'CV file',
            'data.image' => 'share image',
            'data.*.gallery.*' => 'gallery image',
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
                'data.cv' => ['nullable', 'string', 'max:300', $url],
                'data.whatsapp' => ['nullable', 'string', 'regex:/^[0-9]{8,15}$/'],
                'data.whatsappMessage' => ['nullable', 'string', 'max:200'],
                'data.github' => ['nullable', 'string', 'max:300', $url],
                'data.linkedin' => ['nullable', 'string', 'max:300', $url],
            ],
            'seo' => [
                'data' => ['required', 'array'],
                'data.title' => ['nullable', 'string', 'max:70'],
                'data.description' => ['nullable', 'string', 'max:300'],
                'data.image' => ['nullable', 'string', 'max:300', $url],
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
                'data.*.gallery' => ['nullable', 'array', 'max:12'],
                'data.*.gallery.*' => ['required', 'string', 'max:300', $url],
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
            'testimonials' => [
                'data' => ['present', 'array', 'max:12'],
                'data.*.name' => ['required', 'string', 'max:100'],
                'data.*.role' => ['nullable', 'string', 'max:120'],
                'data.*.quote' => ['required', 'string', 'max:600'],
                'data.*.photo' => ['nullable', 'string', 'max:300', $url],
            ],
            'certificates' => [
                'data' => ['present', 'array', 'max:24'],
                'data.*.title' => ['required', 'string', 'max:150'],
                'data.*.issuer' => ['nullable', 'string', 'max:120'],
                'data.*.year' => ['nullable', 'string', 'max:20'],
                'data.*.image' => ['nullable', 'string', 'max:300', $url],
                'data.*.link' => ['nullable', 'string', 'max:300', $url],
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
