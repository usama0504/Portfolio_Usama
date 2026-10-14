import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import PortfolioLayout from '@/layouts/PortfolioLayout.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    // Dashboard pages (admin/*) bring their own layout; the public site uses the portfolio layout.
    layout: (name) => (name.startsWith('admin/') ? null : PortfolioLayout),
    progress: {
        color: '#0d8a7a',
    },
});

// Set light / dark mode on page load.
initializeTheme();
