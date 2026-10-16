<?php

namespace App\Support;

use App\Models\SiteContent;
use Illuminate\Support\Str;

/**
 * Title / description / share image for the <head> of the page.
 * Rendered on the server so WhatsApp, LinkedIn, Google & co. (which do not run JavaScript) can read it.
 */
class Seo
{
    /** @return array{title: string, description: string, image: string, url: string} */
    public static function data(): array
    {
        $sections = SiteContent::sections();
        $profile = is_array($sections['profile'] ?? null) ? $sections['profile'] : [];
        $seo = is_array($sections['seo'] ?? null) ? $sections['seo'] : [];

        $name = self::text($profile['name'] ?? null);
        $job = self::text($profile['title'] ?? null);
        $derivedTitle = ($name !== '' && $job !== '') ? "{$name} — {$job}" : (string) config('portfolio.default_title');

        $title = self::text($seo['title'] ?? null) ?: $derivedTitle;
        $description = self::text($seo['description'] ?? null)
            ?: (self::text($profile['summary'] ?? null) ?: (string) config('portfolio.default_description'));
        $image = self::text($seo['image'] ?? null) ?: '/og-default.png';

        return [
            'title' => $title,
            'description' => Str::limit($description, 300),
            'image' => preg_match('~^https?://~i', $image) ? $image : url($image),
            'url' => url()->current(),
        ];
    }

    private static function text(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }
}
