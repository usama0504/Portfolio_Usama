<script setup lang="ts">
import { motion } from 'motion-v';
import { Briefcase, GraduationCap } from '@lucide/vue';
import { useReducedMotion } from '@/composables/useReducedMotion';
import { education, experiences } from '@/data/portfolio';
import { fadeUp, inView, stagger } from '@/lib/motion';

const { prefersReducedMotion } = useReducedMotion();
const v = <T,>(variants: T) => (prefersReducedMotion.value ? ({ hidden: {}, visible: {} } as T) : variants);
</script>

<template>
    <section id="journey" class="folio-section border-t border-line">
        <div class="folio-container">
            <motion.div
                :variants="v(stagger(0.1))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="flex flex-col gap-6"
            >
                <motion.p :variants="v(fadeUp)" class="folio-eyebrow lg:col-span-3 lg:pt-3">Journey</motion.p>
                <motion.h2 :variants="v(fadeUp)" class="folio-display text-[clamp(2.2rem,4.6vw,3.9rem)] text-ink lg:col-span-9">
                    Experience &amp; <span class="folio-em">education</span>
                </motion.h2>
            </motion.div>

            <div class="mt-16 grid gap-16 md:mt-20 lg:grid-cols-12 lg:gap-10">
                <!-- Experience -->
                <motion.div
                    :variants="v(stagger(0.1))"
                    initial="hidden"
                    while-in-view="visible"
                    :viewport="inView"
                    class="lg:col-span-7"
                >
                    <motion.h3 :variants="v(fadeUp)" class="flex items-center gap-3 font-mono text-xs tracking-widest text-ink-muted uppercase">
                        <Briefcase class="h-4 w-4" /> Work
                    </motion.h3>

                    <ol class="mt-6">
                        <motion.li
                            v-for="exp in experiences"
                            :key="exp.company + exp.role"
                            :variants="v(fadeUp)"
                            class="folio-card p-7 md:p-9"
                        >
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="folio-display text-3xl text-ink">{{ exp.role }}</p>
                                    <p class="mt-2 text-base text-ink-soft">
                                        <span class="font-semibold text-brand">{{ exp.company }}</span>
                                        <template v-if="exp.location"> · {{ exp.location }}</template>
                                    </p>
                                </div>
                                <span class="folio-pill">
                                    <span v-if="!exp.endDate" class="relative flex h-2 w-2">
                                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-70" />
                                        <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500" />
                                    </span>
                                    {{ exp.startDate }} — {{ exp.endDate ?? 'Present' }}
                                </span>
                            </div>

                            <ul class="mt-7 space-y-3">
                                <li
                                    v-for="(item, i) in exp.description"
                                    :key="i"
                                    class="flex gap-4 text-[0.97rem] leading-relaxed text-ink-soft"
                                >
                                    <span class="mt-[0.7em] h-px w-4 shrink-0 bg-brand" />
                                    {{ item }}
                                </li>
                            </ul>

                            <ul class="mt-7 flex flex-wrap gap-2">
                                <li v-for="tech in exp.technologies" :key="tech" class="folio-pill">{{ tech }}</li>
                            </ul>
                        </motion.li>
                    </ol>
                </motion.div>

                <!-- Education -->
                <motion.div
                    :variants="v(stagger(0.1))"
                    initial="hidden"
                    while-in-view="visible"
                    :viewport="inView"
                    class="lg:col-span-5"
                >
                    <motion.h3 :variants="v(fadeUp)" class="flex items-center gap-3 font-mono text-xs tracking-widest text-ink-muted uppercase">
                        <GraduationCap class="h-4 w-4" /> Education
                    </motion.h3>

                    <ol class="relative mt-6 border-l border-line pl-8">
                        <motion.li
                            v-for="entry in education"
                            :key="entry.institution"
                            :variants="v(fadeUp)"
                            class="relative pb-10 last:pb-0"
                        >
                            <span class="absolute top-1.5 -left-[2.4rem] h-3 w-3 rounded-full border-2 border-paper bg-brand ring-1 ring-line" />
                            <p class="font-mono text-xs text-ink-muted">{{ entry.startDate }} — {{ entry.endDate }}</p>
                            <p class="mt-2 font-display text-xl font-semibold tracking-tight text-ink">{{ entry.qualification }}</p>
                            <p class="mt-1 text-sm text-ink-soft">{{ entry.institution }}</p>
                            <p v-if="entry.description" class="mt-3 text-sm leading-relaxed text-ink-muted">{{ entry.description }}</p>
                        </motion.li>
                    </ol>
                </motion.div>
            </div>
        </div>
    </section>
</template>
