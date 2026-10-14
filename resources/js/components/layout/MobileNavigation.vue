<script setup lang="ts">
import { ArrowUpRight, X } from '@lucide/vue';
import BrandIcon from '@/components/portfolio/BrandIcon.vue';
import ThemeToggle from '@/components/layout/ThemeToggle.vue';
import { Sheet, SheetClose, SheetContent, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { navigationLinks, personalInfo, visibleSocialLinks } from '@/data/portfolio';
import { scrollToSection } from '@/lib/scroll';

defineProps<{ activeSection: string }>();
</script>

<template>
    <Sheet>
        <SheetTrigger :as-child="true">
            <slot name="trigger" />
        </SheetTrigger>
        <SheetContent side="right" class="w-full border-line bg-paper p-0 sm:max-w-sm [&>button]:hidden">
            <SheetTitle class="sr-only">Navigation menu</SheetTitle>

            <div class="flex h-full flex-col px-6 pt-5 pb-8">
                <div class="flex items-center justify-between">
                    <span class="folio-eyebrow">Menu</span>
                    <div class="flex items-center gap-1">
                        <ThemeToggle />
                        <SheetClose as-child>
                            <button class="flex h-10 w-10 items-center justify-center rounded-full bg-ink text-paper" aria-label="Close menu">
                                <X class="h-4 w-4" />
                            </button>
                        </SheetClose>
                    </div>
                </div>

                <nav class="mt-12 flex-1" aria-label="Mobile navigation">
                    <ul class="flex flex-col">
                        <li v-for="(link, i) in navigationLinks" :key="link.id" class="border-b border-line">
                            <SheetClose as-child>
                                <button
                                    class="flex w-full items-baseline gap-4 py-4 text-left"
                                    @click="scrollToSection(link.href)"
                                >
                                    <span class="font-mono text-xs text-ink-muted">0{{ i + 1 }}</span>
                                    <span
                                        class="font-display text-4xl font-semibold tracking-tight transition-colors"
                                        :class="activeSection === link.id ? 'text-brand' : 'text-ink'"
                                    >
                                        {{ link.label }}
                                    </span>
                                </button>
                            </SheetClose>
                        </li>
                    </ul>
                </nav>

                <div class="space-y-5">
                    <a :href="`mailto:${personalInfo.email}`" class="block text-sm text-ink-soft">{{ personalInfo.email }}</a>
                    <div class="flex items-center gap-2">
                        <a
                            v-for="s in visibleSocialLinks"
                            :key="s.platform"
                            :href="s.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            :aria-label="s.label"
                            class="flex h-10 w-10 items-center justify-center rounded-full border border-line text-ink-soft"
                        >
                            <BrandIcon :platform="s.platform" class="h-4 w-4" />
                        </a>
                        <SheetClose as-child>
                            <button class="folio-btn folio-btn-brand ml-auto" @click="scrollToSection('contact')">
                                Let's talk <ArrowUpRight class="h-4 w-4" />
                            </button>
                        </SheetClose>
                    </div>
                </div>
            </div>
        </SheetContent>
    </Sheet>
</template>
