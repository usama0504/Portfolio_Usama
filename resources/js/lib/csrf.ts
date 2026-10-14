/** Laravel's CSRF token, read from the XSRF-TOKEN cookie (needed for fetch() POST requests). */
export function xsrfToken(): string {
    const prefix = 'XSRF-TOKEN=';
    const cookie = document.cookie.split('; ').find((row) => row.startsWith(prefix));

    return cookie ? decodeURIComponent(cookie.slice(prefix.length)) : '';
}
