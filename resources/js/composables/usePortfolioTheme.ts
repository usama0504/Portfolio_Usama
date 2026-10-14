import { useAppearance } from '@/composables/useAppearance';

export function usePortfolioTheme() {
    const { appearance, resolvedAppearance, updateAppearance } =
        useAppearance();

    return {
        appearance,
        resolvedAppearance,
        updateAppearance,
    };
}
