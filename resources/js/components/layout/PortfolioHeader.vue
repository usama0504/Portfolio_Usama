<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import { motion } from 'motion-v';
import { ArrowUpRight, Menu } from '@lucide/vue';
import MobileNavigation from '@/components/layout/MobileNavigation.vue';
import ThemeToggle from '@/components/layout/ThemeToggle.vue';
import { useActiveSection } from '@/composables/useActiveSection';
import { initials, navigationLinks, personalInfo } from '@/data/portfolio';
import { easeOutExpo } from '@/lib/motion';
import { scrollToSection } from '@/lib/scroll';

const activeSection = useActiveSection(navigationLinks.map((link) => link.id));

/* Hide the bar while scrolling down, bring it back on scroll up. */
const hidden = ref(false);
const scrolled = ref(false);
let lastY = 0;

function onScroll() {
    const y = window.scrollY;
    scrolled.value = y > 24;
    hidden.value = y > 400 && y > lastY;
    lastY = y;
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>

<template>
    <a
        href="#main-content"
        class="fixed top-0 left-0 z-100 -translate-y-full bg-brand px-4 py-2 text-sm font-semibold text-brand-ink focus:translate-y-0"
    >
        Skip to content
    </a>

    <header
        class="fixed inset-x-0 top-0 z-50 px-3 pt-3 transition-transform duration-500 md:pt-5"
        :class="hidden ? 'translate-y-[-120%]' : 'translate-y-0'"
    >
        <motion.nav
            :initial="{ y: -80, opacity: 0 }"
            :animate="{ y: 0, opacity: 1 }"
            :transition="{ duration: 0.8, ease: easeOutExpo, delay: 0.2 }"
            class="mx-auto flex h-14 max-w-5xl items-center justify-between rounded-full border px-2 transition-[background-color,border-color,box-shadow] duration-500"
            :class="scrolled
                ? 'border-line bg-paper/75 shadow-[0_10px_40px_-20px_rgb(0_0_0/0.5)] backdrop-blur-xl'
                : 'border-transparent bg-transparent'"
            aria-label="Main navigation"
        >
            <!-- Logo -->
            <button
                class="group flex items-center gap-2.5 rounded-full py-1 pr-3 pl-1"
                aria-label="Back to top"
                @click="scrollToSection('top')"
            >
                <span
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-ink font-display text-sm font-bold text-paper transition-colors duration-300 group-hover:bg-brand group-hover:text-brand-ink"
                >
                    {{ initials }}
                </span>
                <span class="hidden font-display text-[0.95rem] font-semibold tracking-tight text-ink sm:block">
                    {{ personalInfo.name }}
                </span>
            </button>

            <!-- Desktop links -->
            <ul class="hidden items-center gap-1 md:flex">
                <li v-for="link in navigationLinks" :key="link.id">
                    <button
                        class="relative rounded-full px-4 py-2 text-sm font-medium transition-colors"
                        :class="activeSection === link.id ? 'text-ink' : 'text-ink-muted hover:text-ink'"
                        @click="scrollToSection(link.href)"
                    >
                        <span
                            v-if="activeSection === link.id"
                            class="absolute inset-0 -z-10 rounded-full bg-ink/[0.07]"
                        />
                        {{ link.label }}
                    </button>
                </li>
            </ul>

            <!-- Right -->
            <div class="flex items-center gap-1.5">
                <ThemeToggle />
                <button class="folio-btn folio-btn-solid hidden py-2.5! text-[0.82rem]! md:inline-flex" @click="scrollToSection('contact')">
                    Let's talk <ArrowUpRight class="folio-btn-icon h-3.5 w-3.5" />
                </button>
                <MobileNavigation :active-section="activeSection">
                    <template #trigger>
                        <button
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-ink text-paper md:hidden"
                            aria-label="Open menu"
                        >
                            <Menu class="h-4 w-4" />
                        </button>
                    </template>
                </MobileNavigation>
            </div>
        </motion.nav>
    </header>
</template>
