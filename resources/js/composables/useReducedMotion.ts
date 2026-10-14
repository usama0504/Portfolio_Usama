import { computed, onMounted, onUnmounted, ref } from 'vue';

function readPreference(): boolean {
    if (typeof window === 'undefined' || typeof window.matchMedia !== 'function') {
        return false;
    }

    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export function useReducedMotion() {
    const prefersReducedMotion = ref(readPreference());
    let mediaQuery: MediaQueryList | null = null;

    function updatePreference(event: MediaQueryListEvent) {
        prefersReducedMotion.value = event.matches;
    }

    onMounted(() => {
        if (typeof window === 'undefined' || typeof window.matchMedia !== 'function') {
            return;
        }

        mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        prefersReducedMotion.value = mediaQuery.matches;
        mediaQuery.addEventListener('change', updatePreference);
    });

    onUnmounted(() => {
        mediaQuery?.removeEventListener('change', updatePreference);
    });

    return {
        prefersReducedMotion: computed(() => prefersReducedMotion.value),
    };
}