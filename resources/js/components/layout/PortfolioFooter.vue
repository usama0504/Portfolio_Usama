<script setup lang="ts">
import { ArrowUp, ArrowUpRight } from '@lucide/vue';
import BrandIcon from '@/components/portfolio/BrandIcon.vue';
import { useLocalTime } from '@/composables/useLocalTime';
import { initials, navigationLinks, personalInfo, visibleSocialLinks } from '@/data/portfolio';
import { scrollToSection } from '@/lib/scroll';

const localTime = useLocalTime(personalInfo.timezone);
const year = new Date().getFullYear();
</script>

<template>
    <footer class="bg-paper px-2 pb-2 sm:px-3 sm:pb-3">
        <div class="relative overflow-hidden rounded-[1.75rem] bg-footer text-footer-ink sm:rounded-[2.5rem]">
            <!-- Brand glow -->
            <div
                class="pointer-events-none absolute -top-32 left-1/2 h-64 w-xl max-w-full -translate-x-1/2 rounded-full opacity-25 blur-[100px]"
                style="background: var(--folio-brand)"
                aria-hidden="true"
            />

            <div class="folio-container relative pt-14 md:pt-20">
                <div class="grid gap-12 lg:grid-cols-12 lg:gap-8">
                    <!-- Identity -->
                    <div class="lg:col-span-6">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-footer-ink font-display text-sm font-bold text-footer">
                                {{ initials }}
                            </span>
                            <span class="font-display text-lg font-semibold tracking-tight">{{ personalInfo.name }}</span>
                        </div>
                        <p class="mt-5 max-w-sm text-[0.95rem] leading-relaxed text-footer-ink/65">
                            {{ personalInfo.title }} building thoughtful, fast web products with Laravel &amp; Vue.js.
                        </p>
                        <button
                            class="mt-6 inline-flex items-center gap-2 rounded-full border border-footer-line px-4 py-2 text-sm font-medium transition-colors hover:border-brand hover:bg-brand hover:text-brand-ink"
                            @click="scrollToSection('contact')"
                        >
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-70" />
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400" />
                            </span>
                            {{ personalInfo.availability }}
                            <ArrowUpRight class="h-3.5 w-3.5" />
                        </button>
                    </div>

                    <!-- Sitemap -->
                    <nav aria-label="Footer navigation" class="lg:col-span-3">
                        <p class="font-mono text-xs tracking-widest text-footer-ink/45 uppercase">Sitemap</p>
                        <ul class="mt-5 grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-3 lg:grid-cols-1">
                            <li v-for="link in navigationLinks" :key="link.id">
                                <button
                                    class="folio-link text-[0.95rem] text-footer-ink/75 transition-colors hover:text-footer-ink"
                                    @click="scrollToSection(link.href)"
                                >
                                    {{ link.label }}
                                </button>
                            </li>
                        </ul>
                    </nav>

                    <!-- Elsewhere -->
                    <div class="lg:col-span-3">
                        <p class="font-mono text-xs tracking-widest text-footer-ink/45 uppercase">Elsewhere</p>
                        <ul class="mt-5 flex gap-2">
                            <li v-for="s in visibleSocialLinks" :key="s.platform">
                                <a
                                    :href="s.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    :aria-label="s.label"
                                    class="flex h-11 w-11 items-center justify-center rounded-full border border-footer-line text-footer-ink/80 transition-colors hover:border-footer-ink hover:bg-footer-ink hover:text-footer"
                                >
                                    <BrandIcon :platform="s.platform" class="h-4 w-4" />
                                </a>
                            </li>
                        </ul>
                        <p class="mt-8 font-mono text-xs tracking-widest text-footer-ink/45 uppercase">Local time</p>
                        <p class="mt-2 text-[0.95rem] text-footer-ink/75">
                            <span class="font-mono text-footer-ink">{{ localTime || '--:--' }}</span> · Pakistan (PKT)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Giant wordmark -->
            <div class="relative mt-14 overflow-hidden px-4 select-none md:mt-20" aria-hidden="true">
                <p
                    class="folio-display bg-linear-to-b from-footer-ink/90 to-footer-ink/5 bg-clip-text pb-[0.12em] text-center text-[10.5vw] leading-none whitespace-nowrap text-transparent xl:text-[9rem]"
                >
                    {{ personalInfo.name }}
                </p>
            </div>

            <div class="folio-container relative flex flex-col-reverse items-center justify-between gap-4 border-t border-footer-line py-6 sm:flex-row">
                <p class="text-center text-xs text-footer-ink/50 sm:text-left">
                    &copy; {{ year }} {{ personalInfo.name }}. Designed &amp; built by me.
                </p>
                <button
                    class="group inline-flex items-center gap-2 text-xs font-medium text-footer-ink/70 hover:text-footer-ink"
                    @click="scrollToSection('top')"
                >
                    Back to top
                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-footer-line transition-colors group-hover:border-brand group-hover:bg-brand group-hover:text-brand-ink"
                    >
                        <ArrowUp class="h-3.5 w-3.5" />
                    </span>
                </button>
            </div>
        </div>
    </footer>
</template>
