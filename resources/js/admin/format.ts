export function formatDate(iso: string | null): string {
    if (!iso) return '';

    return new Date(iso).toLocaleString(undefined, {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
}

/** "5 min ago", "Yesterday", "12 Mar" … for the inbox list. */
export function shortDate(iso: string | null): string {
    if (!iso) return '';

    const date = new Date(iso);
    const minutes = Math.round((Date.now() - date.getTime()) / 60000);

    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes} min ago`;
    if (minutes < 60 * 24) return date.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });

    return date.toLocaleDateString(undefined, { day: 'numeric', month: 'short' });
}
