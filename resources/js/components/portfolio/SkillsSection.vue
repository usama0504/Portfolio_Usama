<script setup lang="ts">
import { motion } from 'motion-v';
import { useReducedMotion } from '@/composables/useReducedMotion';
import { skillCategories } from '@/data/portfolio';
import { fadeUp, inView, stagger } from '@/lib/motion';
import { trackSpotlight } from '@/lib/spotlight';

const { prefersReducedMotion } = useReducedMotion();
const v = <T,>(variants: T) => (prefersReducedMotion.value ? ({ hidden: {}, visible: {} } as T) : variants);
</script>

<template>
    <section id="skills" class="folio-section border-t border-line bg-paper-2">
        <div class="folio-container grid gap-12 lg:grid-cols-12 lg:gap-10">
            <!-- Sticky heading -->
            <motion.div
                :variants="v(stagger(0.1))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="lg:col-span-5"
            >
                <div class="lg:sticky lg:top-32">
                    <motion.p :variants="v(fadeUp)" class="folio-eyebrow">Skills &amp; stack</motion.p>
                    <motion.h2 :variants="v(fadeUp)" class="folio-display mt-5 text-[clamp(2.2rem,4.4vw,3.6rem)] text-ink">
                        The tools I <span class="folio-em">build</span> with
                    </motion.h2>
                    <motion.p :variants="v(fadeUp)" class="mt-6 max-w-md text-base leading-relaxed text-ink-soft">
                        A focused, modern stack for shipping complete products. Highlighted items are the ones I reach for
                        every day.
                    </motion.p>
                </div>
            </motion.div>

            <!-- Category cards -->
            <motion.ul
                :variants="v(stagger(0.08))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="flex flex-col gap-4 lg:col-span-7"
            >
                <motion.li
                    v-for="(group, i) in skillCategories"
                    :key="group.category"
                    :variants="v(fadeUp)"
                    class="folio-card folio-spotlight group p-6 transition-colors duration-300 hover:border-line-strong md:p-8"
                    @pointermove="trackSpotlight"
                >
                    <div class="flex items-start gap-5">
                        <span
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-line bg-paper text-ink transition-colors duration-300 group-hover:border-brand group-hover:bg-brand group-hover:text-brand-ink"
                        >
                            <component :is="group.icon" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-baseline justify-between gap-4">
                                <h3 class="font-display text-xl font-semibold tracking-tight text-ink">{{ group.category }}</h3>
                                <span class="font-mono text-xs text-ink-muted">0{{ i + 1 }}</span>
                            </div>
                            <p v-if="group.blurb" class="mt-1 text-sm text-ink-muted">{{ group.blurb }}</p>
                            <ul class="mt-5 flex flex-wrap gap-2">
                                <li
                                    v-for="skill in group.skills"
                                    :key="skill.name"
                                    class="folio-pill"
                                    :class="skill.isCore ? 'border-ink/25 text-ink' : ''"
                                >
                                    <span v-if="skill.isCore" class="h-1.5 w-1.5 rounded-full bg-brand" />
                                    {{ skill.name }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </motion.li>
            </motion.ul>
        </div>
    </section>
</template>
