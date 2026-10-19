<script setup lang="ts">
import { ArrowLeft } from '@lucide/vue';
import { initials, personalInfo } from '@/data/portfolio';

defineProps<{ title: string; subtitle: string }>();

const panel = {
    background:
        'radial-gradient(900px 520px at 90% -10%, rgba(45,212,191,.38), transparent 60%), radial-gradient(700px 520px at -5% 110%, rgba(13,138,122,.45), transparent 60%), linear-gradient(160deg, #09141e 0%, #0d2a30 100%)',
};
</script>

<template>
    <div
        class="flex min-h-screen items-center justify-center bg-[radial-gradient(ellipse_at_top,#e8f3ff_0%,#f5f8fc_48%,#edf3fa_100%)] px-4 py-6 text-ink transition-colors dark:bg-[radial-gradient(ellipse_at_top,#13222e_0%,#0b121b_52%,#070d14_100%)] dark:text-slate-100 sm:px-6 lg:px-8"
    >
        <section
            class="w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-[0_24px_80px_rgba(15,48,78,0.14)] ring-1 ring-slate-200/80 transition-colors dark:bg-slate-950 dark:shadow-[0_24px_80px_rgba(0,0,0,0.42)] dark:ring-slate-700/80"
        >
            <!-- Mobile header: clean, compact, and intentionally image-free -->
            <header
                class="flex items-center gap-3 border-b border-white/10 px-5 py-4 text-white lg:hidden"
                :style="panel"
            >
                <span
                    class="size-11 shrink-0 overflow-hidden rounded-full bg-[#2dd4bf] p-0.5"
                >
                    <img
                        v-if="personalInfo.photo"
                        :src="personalInfo.photo"
                        :alt="personalInfo.name"
                        class="size-full rounded-full object-cover object-top"
                    />
                    <span
                        v-else
                        class="flex size-full items-center justify-center rounded-full bg-[#09141e] text-sm font-bold"
                    >
                        {{ initials }}
                    </span>
                </span>
                <div class="min-w-0">
                    <p class="truncate font-display font-bold">
                        {{ personalInfo.name }}
                    </p>
                    <!-- <p class="text-xs text-white/70">Dashboard</p> -->
                </div>
            </header>

            <div
                class="grid grid-cols-1 md:min-h-135 md:grid-cols-[0.82fr_1.18fr]"
            >
                <!-- Image only: no name, heading, logo, or overlay text -->
                <aside
                    class="relative hidden min-h-135 overflow-hidden bg-slate-900 md:block"
                >
                    <img
                        v-if="personalInfo.photo"
                        :src="personalInfo.photo"
                        :alt="personalInfo.name || 'Portfolio profile photo'"
                        class="absolute inset-0 h-full w-full object-cover object-center"
                    />
                    <div
                        v-else
                        class="absolute inset-0 bg-linear-to-br from-slate-950 via-sky-950 to-teal-800"
                        aria-hidden="true"
                    ></div>
                </aside>

                <!-- Form panel -->
                <main
                    class="flex items-center justify-center bg-white px-5 py-8 text-slate-900 transition-colors dark:bg-slate-950 dark:text-slate-100 sm:px-10 sm:py-10 lg:px-14"
                >
                    <div class="w-full max-w-md">
                        <h1
                            class="font-display text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl"
                        >
                            {{ title }}
                        </h1>
                        <p
                            class="mt-2 mb-7 text-sm leading-6 text-slate-500 dark:text-slate-300 sm:text-base"
                        >
                            {{ subtitle }}
                        </p>
                        <slot />
                    </div>
                </main>
            </div>
        </section>
    </div>
</template>
