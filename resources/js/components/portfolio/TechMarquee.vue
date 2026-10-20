<script setup lang="ts">
import { techStack } from '@/data/portfolio';

/* Rendered twice so the -50% translate loops seamlessly. */
const loop = [...techStack, ...techStack];
</script>

<template>
    <div class="relative border-y border-line bg-inverse py-5 text-inverse-ink md:py-6" aria-label="Technologies I use">
        <div class="folio-marquee-mask overflow-hidden">
            <ul class="flex w-max animate-marquee items-center hover:paused">
                <li
                    v-for="(tech, i) in loop"
                    :key="`${tech}-${i}`"
                    class="folio-marquee-item flex cursor-default items-center gap-8 pr-8 font-display text-2xl font-semibold tracking-tight transition-colors duration-200 md:text-4xl"
                    :aria-hidden="i >= techStack.length"
                >
                    <span>{{ tech }}</span>
                    <svg viewBox="0 0 24 24" class="h-5 w-5 text-brand md:h-6 md:w-6" fill="currentColor" aria-hidden="true">
                        <path d="M12 0l2.6 9.4L24 12l-9.4 2.6L12 24l-2.6-9.4L0 12l9.4-2.6z" />
                    </svg>
                </li>
            </ul>
        </div>
    </div>
</template>
