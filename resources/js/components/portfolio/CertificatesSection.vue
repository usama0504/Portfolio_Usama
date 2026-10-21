<script setup lang="ts">
import { motion } from 'motion-v';
import { ArrowUpRight, Award } from '@lucide/vue';
import { useReducedMotion } from '@/composables/useReducedMotion';
import { certificates } from '@/data/portfolio';
import { fadeUp, inView, stagger } from '@/lib/motion';

const { prefersReducedMotion } = useReducedMotion();
const v = <T,>(variants: T) => (prefersReducedMotion.value ? ({ hidden: {}, visible: {} } as T) : variants);
</script>

<!-- Hidden until you add certificates in the dashboard. -->
<template>
    <section v-if="certificates.length" id="certificates" class="folio-section border-t border-line">
        <div class="folio-container">
            <motion.div
                :variants="v(stagger(0.1))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="flex flex-col gap-6"
            >
                <motion.p :variants="v(fadeUp)" class="folio-eyebrow">Credentials</motion.p>
                <motion.h2 :variants="v(fadeUp)" class="folio-display text-[clamp(2.2rem,4.6vw,3.9rem)] text-ink">
                    Certificates &amp; <span class="folio-em">achievements</span>
                </motion.h2>
            </motion.div>

            <motion.ul
                :variants="v(stagger(0.08))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="mt-14 grid gap-6 sm:grid-cols-2 md:mt-16 lg:grid-cols-3"
            >
                <motion.li
                    v-for="(item, index) in certificates"
                    :key="index"
                    :variants="v(fadeUp)"
                    class="folio-card flex flex-col overflow-hidden"
                >
                    <component
                        :is="item.link ? 'a' : 'div'"
                        v-if="item.image"
                        :href="item.link"
                        :target="item.link ? '_blank' : undefined"
                        :rel="item.link ? 'noopener' : undefined"
                        class="block aspect-4/3 overflow-hidden border-b border-line bg-paper-2"
                    >
                        <img :src="item.image" :alt="item.title" class="size-full object-cover" loading="lazy" />
                    </component>

                    <div class="flex flex-1 flex-col p-6">
                        <Award v-if="!item.image" class="mb-4 h-7 w-7 text-brand" aria-hidden="true" />
                        <h3 class="font-display text-lg font-bold text-ink">{{ item.title }}</h3>
                        <p v-if="item.issuer || item.year" class="mt-1.5 text-sm text-ink-muted">
                            {{ [item.issuer, item.year].filter(Boolean).join(' · ') }}
                        </p>
                        <a
                            v-if="item.link"
                            :href="item.link"
                            target="_blank"
                            rel="noopener"
                            class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-brand underline-offset-4 hover:underline"
                        >
                            View credential <ArrowUpRight class="h-4 w-4" />
                        </a>
                    </div>
                </motion.li>
            </motion.ul>
        </div>
    </section>
</template>
