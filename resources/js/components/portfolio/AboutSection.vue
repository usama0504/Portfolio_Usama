<script setup lang="ts">
import { motion } from 'motion-v';
import { ArrowUpRight, Briefcase, Clock3, GraduationCap } from '@lucide/vue';
import { useLocalTime } from '@/composables/useLocalTime';
import { useReducedMotion } from '@/composables/useReducedMotion';
import { education, experiences, personalInfo, services } from '@/data/portfolio';
import { fadeUp, inView, stagger } from '@/lib/motion';
import { scrollToSection } from '@/lib/scroll';
import { trackSpotlight } from '@/lib/spotlight';

const { prefersReducedMotion } = useReducedMotion();
const v = <T,>(variants: T) => (prefersReducedMotion.value ? ({ hidden: {}, visible: {} } as T) : variants);
const localTime = useLocalTime(personalInfo.timezone);
const current = experiences.find((e) => e.endDate === null) ?? experiences[0];
const degree = education[0];
</script>

<template>
    <section id="about" class="folio-section">
        <div class="folio-container">
            <!-- Statement -->
            <motion.div
                :variants="v(stagger(0.1))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="flex flex-col gap-6"
            >
                <motion.p :variants="v(fadeUp)" class="folio-eyebrow">About me</motion.p>
                <motion.h2
                    :variants="v(fadeUp)"
                    class="folio-display text-[clamp(1.75rem,3.6vw,3rem)] leading-[1.08] text-ink max-w-4xl"
                >
                    I'm a developer who sweats the <span class="folio-em">details</span> — clean code, fast pages and
                    interfaces that simply make sense.
                </motion.h2>
            </motion.div>

            <!-- Bento -->
            <motion.div
                :variants="v(stagger(0.08))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="mt-16 grid gap-4 md:mt-20 md:grid-cols-2 lg:grid-cols-3"
            >
                <!-- Bio -->
                <motion.article
                    :variants="v(fadeUp)"
                    class="folio-card folio-spotlight flex flex-col justify-between p-7 md:col-span-2 md:p-9"
                    @pointermove="trackSpotlight"
                >
                    <div class="space-y-4 text-base leading-relaxed text-ink-soft md:text-[1.05rem]">
                        <p>
                            I'm <span class="font-medium text-ink">{{ personalInfo.name }}</span>, a full-stack developer from
                            {{ personalInfo.location }}. I turn ideas into complete products — designing the database,
                            writing the API and crafting the interface on top.
                        </p>
                        <p>
                            My go-to stack is Laravel and Vue.js with TypeScript and Tailwind CSS. I care about
                            maintainable code, honest performance and the small UX details that make software feel good.
                        </p>
                    </div>
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <button class="folio-btn folio-btn-solid" @click="scrollToSection('contact')">
                            Work with me <ArrowUpRight class="folio-btn-icon h-4 w-4" />
                        </button>
                        <span v-if="degree" class="folio-pill">
                            <GraduationCap class="h-3.5 w-3.5" /> {{ degree.qualification }} · {{ degree.endDate }}
                        </span>
                    </div>
                </motion.article>

                <!-- Local time -->
                <motion.article
                    :variants="v(fadeUp)"
                    class="folio-card folio-spotlight flex flex-col justify-between p-7"
                    @pointermove="trackSpotlight"
                >
                    <div class="flex items-center justify-between">
                        <p class="font-mono text-xs tracking-widest text-ink-muted uppercase">Based in</p>
                        <Clock3 class="h-4 w-4 text-ink-muted" />
                    </div>
                    <div class="mt-10">
                        <p class="folio-display text-6xl text-ink">{{ localTime || '--:--' }}</p>
                        <p class="mt-3 text-sm text-ink-soft">{{ personalInfo.location }}</p>
                        <p class="text-xs text-ink-muted">PKT · UTC+5 · Open to remote work</p>
                    </div>
                </motion.article>

                <!-- Currently -->
                <motion.article
                    v-if="current"
                    :variants="v(fadeUp)"
                    class="folio-card flex min-h-80 flex-col justify-between border-transparent bg-inverse p-7 text-inverse-ink"
                >
                    <!-- Decorative backdrop icon -->
                    <Briefcase class="pointer-events-none absolute -right-8 -bottom-8 h-44 w-44 opacity-[0.06]" stroke-width="1.25" aria-hidden="true" />

                    <div class="relative flex items-center justify-between">
                        <p class="font-mono text-xs tracking-widest uppercase opacity-60">Currently</p>
                        <span class="inline-flex items-center gap-2 rounded-full bg-inverse-ink/10 px-2.5 py-1 text-[0.7rem] font-medium">
                            <span class="relative flex h-1.5 w-1.5">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75" />
                                <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-400" />
                            </span>
                            Now
                        </span>
                    </div>

                    <div class="relative mt-10">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand font-display text-xl font-bold text-brand-ink">
                            {{ current.company.charAt(0) }}
                        </span>
                        <p class="folio-display mt-5 text-[1.75rem] leading-tight">{{ current.role }}</p>
                        <p class="mt-2 text-sm opacity-70">
                            at <span class="font-semibold opacity-100">{{ current.company }}</span> · {{ current.location }} ·
                            since {{ current.startDate }}
                        </p>
                        <ul class="mt-5 flex flex-wrap gap-1.5">
                            <li
                                v-for="tech in current.technologies.slice(0, 4)"
                                :key="tech"
                                class="rounded-full border border-inverse-ink/15 px-2.5 py-1 text-[0.72rem] opacity-80"
                            >
                                {{ tech }}
                            </li>
                        </ul>
                    </div>
                </motion.article>

                <!-- What I do -->
                <motion.article
                    :variants="v(fadeUp)"
                    class="folio-card p-7 md:col-span-2 md:p-9"
                >
                    <p class="font-mono text-xs tracking-widest text-ink-muted uppercase">What I do</p>
                    <ul class="mt-6 grid gap-x-8 gap-y-6 sm:grid-cols-2">
                        <li v-for="s in services" :key="s.title" class="flex gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand/10 text-brand">
                                <component :is="s.icon" class="h-[1.1rem] w-[1.1rem]" />
                            </span>
                            <div>
                                <p class="font-display text-base font-semibold text-ink">{{ s.title }}</p>
                                <p class="mt-1 text-sm leading-relaxed text-ink-muted">{{ s.description }}</p>
                            </div>
                        </li>
                    </ul>
                </motion.article>
            </motion.div>
        </div>
    </section>
</template>
