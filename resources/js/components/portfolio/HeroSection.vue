<script setup lang="ts">
import { ref } from 'vue';
import { motion, useScroll, useTransform } from 'motion-v';
import { ArrowDownRight, ArrowUpRight, Download, MapPin } from '@lucide/vue';
import BrandIcon from '@/components/portfolio/BrandIcon.vue';
import { useReducedMotion } from '@/composables/useReducedMotion';
import { useTypewriter } from '@/composables/useTypewriter';
import { initials, personalInfo, stats, visibleSocialLinks } from '@/data/portfolio';
import { easeOutExpo, fadeUp, stagger } from '@/lib/motion';
import { scrollToSection } from '@/lib/scroll';

const { prefersReducedMotion } = useReducedMotion();
const v = <T,>(variants: T) => (prefersReducedMotion.value ? ({ hidden: {}, visible: {} } as T) : variants);
/* Headline lines — `em` marks the italic serif highlight. */
const lines: { text: string; em?: string }[] = [
    { text: 'Full-stack developer' },
    { text: 'building ', em: 'thoughtful' },
    { text: 'web products.' },
];

const lineReveal = {
    hidden: { y: '110%' },
    visible: { y: '0%', transition: { duration: 1, ease: easeOutExpo } },
};

/* Gentle parallax on the portrait */
const heroRef = ref<HTMLElement | null>(null);
const { scrollYProgress } = useScroll({ target: heroRef, offset: ['start start', 'end start'] });
const photoY = useTransform(scrollYProgress, [0, 1], [0, 50]);

const badgeText = 'OPEN TO WORK • FULL STACK DEVELOPER • ';

/* Greeting types itself out, holds, erases and repeats. */
const greeting = `Hello, I'm ${personalInfo.name}`;
const typed = useTypewriter([greeting]);
</script>

<template>
    <section id="hero" ref="heroRef" class="relative flex min-h-svh flex-col overflow-hidden pt-28 pb-10 md:pt-32 md:pb-12">
        <!-- Soft brand glow -->
        <div
            class="pointer-events-none absolute -top-40 right-[-10%] h-[520px] w-[520px] rounded-full opacity-[0.18] blur-[120px]"
            style="background: var(--folio-brand)"
        />

        <div class="folio-container relative flex flex-1 flex-col">
            <div class="grid flex-1 items-center gap-14 lg:grid-cols-12 lg:gap-12">
                <!-- Headline + copy -->
                <div class="lg:col-span-7">
                    <motion.div
                        :variants="v(stagger(0.08, 0.1))"
                        initial="hidden"
                        animate="visible"
                        class="flex flex-col items-start gap-5"
                    >
                        <motion.span :variants="v(fadeUp)" class="folio-pill">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-70" />
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500" />
                            </span>
                            {{ personalInfo.availability }}
                        </motion.span>

                        <!-- Typewriter greeting -->
                        <motion.p
                            :variants="v(fadeUp)"
                            class="flex min-h-[1.6em] items-center font-mono text-base text-ink-soft md:text-lg"
                            :aria-label="greeting"
                        >
                            <span class="mr-2.5 text-brand" aria-hidden="true">&gt;</span>
                            <span aria-hidden="true">{{ prefersReducedMotion ? greeting : typed }}</span>
                            <span
                                class="ml-0.5 inline-block h-[1.15em] w-[0.55em] translate-y-[0.05em] folio-caret bg-brand"
                                aria-hidden="true"
                            />
                        </motion.p>
                    </motion.div>

                    <motion.h1
                        :variants="v(stagger(0.12, 0.25))"
                        initial="hidden"
                        animate="visible"
                        class="folio-display mt-7 text-[clamp(2.1rem,5.4vw,4.75rem)] text-ink"
                    >
                        <span v-for="line in lines" :key="line.text" class="block overflow-hidden pb-[0.08em]">
                            <motion.span :variants="v(lineReveal)" class="block">
                                {{ line.text }}<span v-if="line.em" class="folio-em pr-[0.05em]">{{ line.em }}</span>
                            </motion.span>
                        </span>
                    </motion.h1>

                    <motion.div
                        :variants="v(stagger(0.1, 0.7))"
                        initial="hidden"
                        animate="visible"
                        class="mt-7 flex flex-col gap-8"
                    >
                        <motion.p :variants="v(fadeUp)" class="max-w-lg text-base leading-relaxed text-ink-soft md:text-[1.075rem]">
                            I design and build fast, reliable web apps with
                            <span class="text-ink">Laravel</span>, <span class="text-ink">Vue.js</span> and
                            <span class="text-ink">TypeScript</span> — from clean APIs and solid databases to interfaces
                            people enjoy using.
                        </motion.p>

                        <motion.div :variants="v(fadeUp)" class="flex flex-wrap items-center gap-3">
                            <button class="folio-btn folio-btn-solid" @click="scrollToSection('projects')">
                                See my work <ArrowDownRight class="folio-btn-icon h-4 w-4" />
                            </button>
                            <button class="folio-btn folio-btn-outline" @click="scrollToSection('contact')">
                                Get in touch
                            </button>
                            <a
                                v-if="personalInfo.cv"
                                :href="personalInfo.cv"
                                class="folio-btn folio-btn-outline"
                                target="_blank"
                                rel="noopener"
                                download
                            >
                                Download CV <Download class="folio-btn-icon h-4 w-4" />
                            </a>
                        </motion.div>
                    </motion.div>
                </div>

                <!-- Portrait -->
                <motion.div
                    :initial="prefersReducedMotion ? false : { opacity: 0, scale: 0.92, rotate: -2 }"
                    :animate="{ opacity: 1, scale: 1, rotate: 0 }"
                    :transition="{ duration: 1.1, ease: easeOutExpo, delay: 0.4 }"
                    class="relative mx-auto w-full max-w-[21rem] lg:col-span-5 lg:mr-0 lg:max-w-[25rem]"
                >
                    <motion.div :style="prefersReducedMotion ? undefined : { y: photoY }" class="relative">
                        <div class="folio-card aspect-[4/5] rounded-[2rem] p-2">
                            <img
                                v-if="personalInfo.photo"
                                :src="personalInfo.photo"
                                :alt="`${personalInfo.name} — ${personalInfo.title}`"
                                class="h-full w-full rounded-[1.5rem] object-cover object-top"
                                fetchpriority="high"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center rounded-[1.5rem] bg-ink font-display text-8xl font-bold text-paper"
                                role="img"
                                :aria-label="`${personalInfo.name} — ${personalInfo.title}`"
                            >
                                {{ initials }}
                            </div>
                            <div
                                class="absolute inset-x-5 bottom-5 flex items-center justify-between rounded-2xl border border-white/15 bg-black/45 px-4 py-3 text-white backdrop-blur-md"
                            >
                                <div>
                                    <p class="text-sm font-semibold">{{ personalInfo.name }}</p>
                                    <p class="text-xs text-white/70">{{ personalInfo.title }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 text-xs text-white/80">
                                    <MapPin class="h-3.5 w-3.5" /> Pakistan
                                </span>
                            </div>
                        </div>

                        <!-- Rotating badge -->
                        <button
                            class="group absolute -top-8 -left-8 hidden h-28 w-28 items-center justify-center rounded-full bg-brand text-brand-ink shadow-xl sm:flex"
                            aria-label="Scroll to projects"
                            @click="scrollToSection('projects')"
                        >
                            <svg viewBox="0 0 100 100" class="absolute inset-0 h-full w-full animate-spin-slow" aria-hidden="true">
                                <defs>
                                    <path id="badge-circle" d="M50 50 m-38 0 a38 38 0 1 1 76 0 a38 38 0 1 1 -76 0" />
                                </defs>
                                <text class="fill-current font-mono text-[8.6px] font-medium tracking-[0.12em]">
                                    <textPath href="#badge-circle">{{ badgeText }}</textPath>
                                </text>
                            </svg>
                            <ArrowUpRight class="h-6 w-6 transition-transform duration-300 group-hover:rotate-45" />
                        </button>
                    </motion.div>
                </motion.div>
            </div>

            <!-- Stats + socials -->
            <motion.div
                :variants="v(stagger(0.08, 0.9))"
                initial="hidden"
                animate="visible"
                class="mt-14 grid gap-6 border-t border-line pt-8 sm:grid-cols-2 lg:grid-cols-4"
            >
                <motion.div v-for="stat in stats" :key="stat.label" :variants="v(fadeUp)" class="flex items-baseline gap-3">
                    <span class="folio-display text-4xl text-ink md:text-5xl">{{ stat.value }}</span>
                    <span class="max-w-[9rem] text-sm leading-snug text-ink-muted">{{ stat.label }}</span>
                </motion.div>
                <motion.div :variants="v(fadeUp)" class="flex items-center gap-2 lg:justify-end">
                    <a
                        v-for="s in visibleSocialLinks"
                        :key="s.platform"
                        :href="s.url"
                        target="_blank"
                        rel="noopener noreferrer"
                        :aria-label="s.label"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-line text-ink-soft transition-colors hover:border-ink hover:bg-ink hover:text-paper"
                    >
                        <BrandIcon :platform="s.platform" class="h-4 w-4" />
                    </a>
                </motion.div>
            </motion.div>
        </div>
    </section>
</template>
