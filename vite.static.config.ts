import { cpSync, readdirSync } from 'node:fs';
import { resolve } from 'node:path';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import { defineConfig } from 'vite';
import type { Plugin } from 'vite';

/**
 * Static build of the portfolio for GitHub Pages (no Laravel/PHP needed).
 *   npm run build:static  →  dist-static/
 * STATIC_BASE must match the repo name: https://<user>.github.io/<repo>/
 */
const base = process.env.STATIC_BASE ?? '/Portfolio-website/';
const outDir = resolve(__dirname, 'dist-static');

/**
 * Everything in /public is copied (images, favicons…) except Laravel-only
 * entries, so newly added images are picked up automatically.
 */
const laravelOnly = new Set(['index.php', '.htaccess', 'build', 'hot', 'storage', 'fonts-manifest.dev.json']);

function copyPublicAssets(): Plugin {
    return {
        name: 'copy-portfolio-public-assets',
        apply: 'build',
        closeBundle() {
            for (const file of readdirSync(resolve(__dirname, 'public'))) {
                if (!laravelOnly.has(file)) {
                    cpSync(resolve(__dirname, 'public', file), resolve(outDir, file), { recursive: true });
                }
            }
        },
    };
}

export default defineConfig({
    root: resolve(__dirname, 'resources/static'),
    base,
    publicDir: false,
    resolve: {
        alias: { '@': resolve(__dirname, 'resources/js') },
    },
    define: {
        'import.meta.env.VITE_ASSET_BASE': JSON.stringify(base),
    },
    plugins: [tailwindcss(), vue(), copyPublicAssets()],
    build: {
        outDir,
        emptyOutDir: true,
    },
});
