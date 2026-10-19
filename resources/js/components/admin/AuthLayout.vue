<script setup lang="ts">
import { ArrowLeft, Check } from '@lucide/vue';
import { initials, personalInfo } from '@/data/portfolio';

defineProps<{ title: string; subtitle: string }>();

const perks = [
    'Edit your projects, skills and education',
    'Upload your CV and project screenshots',
    'Read and reply to contact messages',
];

// Always-dark brand background (looks the same in light and dark mode).
const panel = {
    background:
        'radial-gradient(900px 520px at 90% -10%, rgba(45,212,191,.38), transparent 60%), radial-gradient(700px 520px at -5% 110%, rgba(13,138,122,.45), transparent 60%), linear-gradient(160deg, #09141e 0%, #0d2a30 100%)',
};
</script>

<template>
    <div class="min-h-screen bg-paper text-ink lg:grid lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)]">
        <!-- brand panel (desktop) -->
        <aside class="relative hidden overflow-hidden text-white lg:flex lg:flex-col lg:justify-between lg:p-14" :style="panel">
            <div class="flex items-center gap-3 text-sm font-semibold tracking-wide text-white/80">
                <span class="inline-flex size-9 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20">⚙</span>
                Dashboard
            </div>

            <div>
                <div class="size-36 overflow-hidden rounded-full bg-[#2dd4bf] p-1.5 shadow-2xl shadow-black/40">
                    <img
                        v-if="personalInfo.photo"
                        :src="personalInfo.photo"
                        :alt="personalInfo.name"
                        class="size-full rounded-full object-cover object-top"
                    />
                    <span v-else class="flex size-full items-center justify-center rounded-full bg-[#09141e] text-4xl font-bold">
                        {{ initials }}
                    </span>
                </div>

                <h2 class="font-display mt-8 text-5xl leading-tight font-bold">{{ personalInfo.name }}</h2>
                <p class="mt-2 text-xl font-medium text-[#2dd4bf]">{{ personalInfo.title }}</p>

                <ul class="mt-10 space-y-3 text-white/80">
                    <li v-for="perk in perks" :key="perk" class="flex items-center gap-3">
                        <span class="flex size-6 shrink-0 items-center justify-center rounded-full bg-[#2dd4bf]/20 text-[#2dd4bf]">
                            <Check class="size-3.5" />
                        </span>
                        {{ perk }}
                    </li>
                </ul>
            </div>

            <a href="/" class="inline-flex items-center gap-2 text-sm text-white/60 transition hover:text-white">
                <ArrowLeft class="size-4" /> Back to website
            </a>
        </aside>

        <!-- brand strip (phones & tablets) -->
        <header class="flex items-center gap-3 px-5 py-4 text-white lg:hidden" :style="panel">
            <span class="size-11 shrink-0 overflow-hidden rounded-full bg-[#2dd4bf] p-0.5">
                <img
                    v-if="personalInfo.photo"
                    :src="personalInfo.photo"
                    :alt="personalInfo.name"
                    class="size-full rounded-full object-cover object-top"
                />
                <span v-else class="flex size-full items-center justify-center rounded-full bg-[#09141e] text-sm font-bold">
                    {{ initials }}
                </span>
            </span>
            <div class="min-w-0">
                <p class="font-display truncate font-bold">{{ personalInfo.name }}</p>
                <p class="text-xs text-white/70">Dashboard</p>
            </div>
        </header>

        <!-- form side -->
        <main class="flex items-start justify-center px-5 py-10 sm:px-10 lg:min-h-screen lg:items-center lg:py-16">
            <div class="w-full max-w-md">
                <h1 class="font-display text-3xl font-bold sm:text-4xl">{{ title }}</h1>
                <p class="mt-2 mb-8 text-ink-soft">{{ subtitle }}</p>

                <slot />

                <p class="mt-8 text-center text-sm lg:hidden">
                    <a href="/" class="text-ink-soft underline-offset-4 hover:text-ink hover:underline">← Back to website</a>
                </p>
            </div>
        </main>
    </div>
</template>
