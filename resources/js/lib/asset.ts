/**
 * Resolve a file from /public. The Laravel app serves it from "/", while the
 * static GitHub Pages build lives under a sub-path (set via VITE_ASSET_BASE).
 */
const base = (import.meta.env.VITE_ASSET_BASE as string | undefined) ?? '/';

export function asset(path: string): string {
    return `${base}${path.replace(/^\//, '')}`;
}
