<script setup lang="ts">
import { motion } from 'motion-v';
import { Quote } from '@lucide/vue';
import { useReducedMotion } from '@/composables/useReducedMotion';
import { testimonials } from '@/data/portfolio';
import { fadeUp, inView, stagger } from '@/lib/motion';

const { prefersReducedMotion } = useReducedMotion();
const v = <T,>(variants: T) => (prefersReducedMotion.value ? ({ hidden: {}, visible: {} } as T) : variants);

const initial = (name: string) => name.trim().charAt(0).toUpperCase() || '?';
</script>

<!-- Hidden until you add testimonials in the dashboard. -->
<template>
    <section v-if="testimonials.length" id="testimonials" class="folio-section border-t border-line">
        <div class="folio-container">
            <motion.div
                :variants="v(stagger(0.1))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="flex flex-col gap-6"
            >
                <motion.p :variants="v(fadeUp)" class="folio-eyebrow">Kind words</motion.p>
                <motion.h2 :variants="v(fadeUp)" class="folio-display text-[clamp(2.2rem,4.6vw,3.9rem)] text-ink">
                    What people <span class="folio-em">say</span>
                </motion.h2>
            </motion.div>

            <motion.ul
                :variants="v(stagger(0.1))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="mt-14 grid gap-6 md:mt-16 md:grid-cols-2 lg:grid-cols-3"
            >
                <motion.li
                    v-for="(item, index) in testimonials"
                    :key="index"
                    :variants="v(fadeUp)"
                    class="folio-card flex flex-col p-7"
                >
                    <Quote class="h-8 w-8 text-brand" aria-hidden="true" />
                    <blockquote class="mt-5 flex-1 text-[0.97rem] leading-relaxed text-ink-soft">“{{ item.quote }}”</blockquote>
                    <div class="mt-7 flex items-center gap-3">
                        <img
                            v-if="item.photo"
                            :src="item.photo"
                            :alt="item.name"
                            class="size-11 shrink-0 rounded-full object-cover"
                            loading="lazy"
                        />
                        <span
                            v-else
                            class="flex size-11 shrink-0 items-center justify-center rounded-full bg-brand text-sm font-bold text-brand-ink"
                        >
                            {{ initial(item.name) }}
                        </span>
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-ink">{{ item.name }}</p>
                            <p v-if="item.role" class="truncate text-sm text-ink-muted">{{ item.role }}</p>
                        </div>
                    </div>
                </motion.li>
            </motion.ul>
        </div>
    </section>
</template>
