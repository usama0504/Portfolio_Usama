<script setup lang="ts">
import { Moon, Sun } from '@lucide/vue';
import { usePortfolioTheme } from '@/composables/usePortfolioTheme';

const { resolvedAppearance, updateAppearance } = usePortfolioTheme();

function toggle() {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
}
</script>

<template>
    <button
        type="button"
        class="relative flex h-9 w-9 cursor-pointer items-center justify-center overflow-hidden rounded-full text-ink-soft transition-colors hover:bg-ink/5 hover:text-ink"
        :aria-label="`Switch to ${resolvedAppearance === 'dark' ? 'light' : 'dark'} mode`"
        @click="toggle"
    >
        <Sun class="h-[1.05rem] w-[1.05rem] scale-100 rotate-0 transition-transform duration-500 dark:scale-0 dark:-rotate-90" />
        <Moon class="absolute h-[1.05rem] w-[1.05rem] scale-0 rotate-90 transition-transform duration-500 dark:scale-100 dark:rotate-0" />
    </button>
</template>
