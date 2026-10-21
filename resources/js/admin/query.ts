/** Build "?a=1&b=2" from an object, skipping empty values and the default "inbox" tab. */
export function qs(params: Record<string, string | number | null | undefined>): string {
    const parts: string[] = [];

    for (const [key, value] of Object.entries(params)) {
        if (value === null || value === undefined || value === '') continue;
        if (key === 'filter' && value === 'inbox') continue;
        if (key === 'page' && Number(value) <= 1) continue;

        parts.push(`${encodeURIComponent(key)}=${encodeURIComponent(String(value))}`);
    }

    return parts.length ? `?${parts.join('&')}` : '';
}
