/** Upload a picture to the dashboard and return its URL path (e.g. /uploads/abc.png). */
export async function uploadImage(file: File): Promise<string> {
    const prefix = 'XSRF-TOKEN=';
    const cookie = document.cookie.split('; ').find((row) => row.startsWith(prefix));
    const token = cookie ? decodeURIComponent(cookie.slice(prefix.length)) : '';

    const body = new FormData();
    body.append('file', file);

    const response = await fetch('/admin/upload', {
        method: 'POST',
        body,
        credentials: 'same-origin',
        headers: { 'X-XSRF-TOKEN': token, Accept: 'application/json' },
    });

    const json = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(json?.errors?.file?.[0] ?? json?.message ?? 'Upload failed.');
    }

    return json.url as string;
}
