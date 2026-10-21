import { site } from '@/data/portfolio';
import { xsrfToken } from '@/lib/csrf';

type TrackEvent = 'project_view' | 'cv_download' | 'whatsapp_click';

/**
 * Tell the dashboard that something was clicked (privacy-friendly: no cookies, no personal data).
 * Only works on the Laravel site; on static hosting it does nothing, and it can never break the page.
 */
export function track(type: TrackEvent, label?: string): void {
    if (!site.serverContact) {
        return;
    }

    try {
        void fetch('/track', {
            method: 'POST',
            credentials: 'same-origin',
            keepalive: true,
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-XSRF-TOKEN': xsrfToken(),
            },
            body: JSON.stringify({ type, label }),
        }).catch(() => undefined);
    } catch {
        // ignore
    }
}
