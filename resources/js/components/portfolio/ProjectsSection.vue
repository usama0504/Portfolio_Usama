<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from 'vue';
import { motion } from 'motion-v';
import { ArrowUpRight, Check, ChevronLeft, ChevronRight, ExternalLink, X } from '@lucide/vue';
import BrandIcon from '@/components/portfolio/BrandIcon.vue';
import { useReducedMotion } from '@/composables/useReducedMotion';
import { projects, visibleSocialLinks } from '@/data/portfolio';
import { fadeUp, inView, stagger } from '@/lib/motion';
import type { Project } from '@/types/portfolio';

const { prefersReducedMotion } = useReducedMotion();
const v = <T,>(variants: T) => (prefersReducedMotion.value ? ({ hidden: {}, visible: {} } as T) : variants);

const githubUrl = visibleSocialLinks.find((l) => l.platform === 'github')?.url;
const statusLabel: Record<string, string> = { live: 'Live', demo: 'Demo project', 'in-progress': 'In progress' };
const pad = (n: number) => String(n + 1).padStart(2, '0');

/* ── Case-study modal ───────────────────────────────────────── */
const activeProject = ref<Project | null>(null);
const galleryIndex = ref(0);
const openProject = (p: Project) => {
    galleryIndex.value = 0;
    activeProject.value = p;
};

// Cover image first, then the extra screenshots.
const images = computed<string[]>(() => {
    const p = activeProject.value;

    if (!p) return [];

    return [p.image, ...(p.gallery ?? [])].filter((src, i, all): src is string => !!src && all.indexOf(src) === i);
});
const step = (by: number) => {
    const n = images.value.length;

    if (n > 1) galleryIndex.value = (galleryIndex.value + by + n) % n;
};
const closeProject = () => (activeProject.value = null);

function onKeydown(e: KeyboardEvent) {
    if (e.key === 'Escape') {
        closeProject();
    } else if (e.key === 'ArrowLeft') {
        step(-1);
    } else if (e.key === 'ArrowRight') {
        step(1);
    }
}

watch(activeProject, (p) => {
    document.body.style.overflow = p ? 'hidden' : '';

    if (p) {
        window.addEventListener('keydown', onKeydown);
    } else {
        window.removeEventListener('keydown', onKeydown);
    }
});

onUnmounted(() => {
    document.body.style.overflow = '';
    window.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <section id="projects" class="folio-section">
        <div class="folio-container">
            <!-- Heading -->
            <motion.div
                :variants="v(stagger(0.1))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="flex flex-col justify-between gap-8 md:flex-row md:items-end"
            >
                <div>
                    <motion.p :variants="v(fadeUp)" class="folio-eyebrow">Selected work</motion.p>
                    <motion.h2 :variants="v(fadeUp)" class="folio-display mt-5 text-[clamp(2.3rem,5vw,4.25rem)] text-ink">
                        Featured <span class="folio-em">projects</span>
                    </motion.h2>
                </div>
            </motion.div>

            <!-- Stacking cards -->
            <div class="mt-16 flex flex-col gap-8 md:mt-20">
                <article
                    v-for="(project, i) in projects"
                    :key="project.slug"
                    class="lg:sticky"
                    :style="{ top: `calc(7rem + ${i * 1.75}rem)` }"
                >
                    <motion.div
                        :variants="v(fadeUp)"
                        initial="hidden"
                        while-in-view="visible"
                        :viewport="inView"
                        class="folio-card grid gap-7 p-4 shadow-[0_-20px_60px_-30px_rgb(0_0_0/0.4)] sm:p-6 md:gap-10 md:p-8 lg:grid-cols-12 lg:p-10"
                    >
                        <!-- Details -->
                        <div class="flex flex-col lg:col-span-5">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-sm text-ink-muted">{{ pad(i) }} / {{ pad(projects.length - 1) }}</span>
                                <span
                                    v-if="project.status"
                                    class="rounded-full bg-brand/12 px-3 py-1 font-mono text-[0.68rem] font-medium tracking-wider text-brand uppercase"
                                >
                                    {{ statusLabel[project.status] }}
                                </span>
                            </div>

                            <p class="mt-6 text-sm font-medium text-ink-muted lg:mt-8">{{ project.category }}</p>
                            <h3 class="folio-display mt-2 text-[clamp(1.9rem,3.4vw,2.9rem)] leading-[1.02] text-ink">
                                {{ project.name }}
                            </h3>
                            <p class="mt-5 text-[0.98rem] leading-relaxed text-ink-soft">{{ project.overview }}</p>

                            <ul class="mt-6 flex flex-wrap gap-2">
                                <li v-for="tech in project.technologies" :key="tech" class="folio-pill">{{ tech }}</li>
                            </ul>

                            <div class="mt-auto pt-8">
                                <button class="folio-btn folio-btn-solid" @click="openProject(project)">
                                    View case study <ArrowUpRight class="folio-btn-icon h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Preview -->
                        <button
                            type="button"
                            class="group relative order-first block text-left lg:order-none lg:col-span-7"
                            :aria-label="`Open case study: ${project.name}`"
                            @click="openProject(project)"
                        >
                            <div class="folio-browser">
                                <div class="folio-browser-bar">
                                    <span class="folio-browser-dot" />
                                    <span class="folio-browser-dot" />
                                    <span class="folio-browser-dot" />
                                </div>
                                <div class="relative aspect-[16/10] overflow-hidden">
                                    <img
                                        v-if="project.image"
                                        :src="project.image"
                                        :alt="`${project.name} interface preview`"
                                        class="h-full w-full object-cover object-top transition-transform duration-[1.2s] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.04]"
                                        loading="lazy"
                                    />
                                </div>
                            </div>
                            <!-- Cursor-style hover badge -->
                            <span
                                class="pointer-events-none absolute top-1/2 left-1/2 flex h-24 w-24 -translate-x-1/2 -translate-y-1/2 scale-50 items-center justify-center rounded-full bg-brand text-sm font-semibold text-brand-ink opacity-0 shadow-2xl transition-all duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-100 group-hover:opacity-100"
                            >
                                View
                            </span>
                        </button>
                    </motion.div>
                </article>
            </div>

            <!-- GitHub CTA -->
            <motion.a
                v-if="githubUrl"
                :href="githubUrl"
                target="_blank"
                rel="noopener noreferrer"
                :variants="v(fadeUp)"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
                class="group mt-10 flex items-center justify-between gap-6 rounded-[1.75rem] border border-dashed border-line-strong p-6 transition-colors hover:border-ink md:p-8"
            >
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-ink text-paper">
                        <BrandIcon platform="github" class="h-5 w-5" />
                    </span>
                    <div>
                        <p class="font-display text-lg font-semibold text-ink">More work is on the way</p>
                        <p class="text-sm text-ink-muted">Experiments and open-source code live on my GitHub.</p>
                    </div>
                </div>
                <ArrowUpRight class="h-6 w-6 shrink-0 text-ink-muted transition-all duration-300 group-hover:rotate-45 group-hover:text-brand" />
            </motion.a>
        </div>
    </section>

    <!-- Case-study modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-200 ease-in"
            leave-to-class="opacity-0"
        >
            <div
                v-if="activeProject"
                class="fixed inset-0 z-80 flex items-end justify-center bg-black/60 backdrop-blur-md sm:items-center sm:p-6"
                role="dialog"
                aria-modal="true"
                :aria-label="activeProject.name"
                @click.self="closeProject"
            >
                <div class="relative max-h-[92vh] w-full overflow-y-auto rounded-t-[2rem] border border-line bg-paper sm:max-w-4xl sm:rounded-[2rem]">
                    <button
                        class="sticky top-4 z-10 float-right mt-4 mr-4 flex h-10 w-10 items-center justify-center rounded-full bg-ink text-paper transition-transform hover:rotate-90"
                        aria-label="Close"
                        @click="closeProject"
                    >
                        <X class="h-4 w-4" />
                    </button>

                    <div class="p-6 sm:p-10">
                        <p class="folio-eyebrow">{{ activeProject.category }}<template v-if="activeProject.status"> · {{ statusLabel[activeProject.status] }}</template></p>
                        <h3 class="folio-display mt-4 text-[clamp(2rem,5vw,3.4rem)] text-ink">{{ activeProject.name }}</h3>
                        <p class="mt-4 max-w-2xl text-base leading-relaxed text-ink-soft">{{ activeProject.overview }}</p>

                        <div v-if="images.length" class="mt-8">
                            <div class="relative overflow-hidden rounded-2xl border border-line bg-surface">
                                <div class="flex aspect-[16/10] items-center justify-center">
                                    <img
                                        :key="galleryIndex"
                                        :src="images[galleryIndex]"
                                        :alt="`${activeProject.name} screenshot ${galleryIndex + 1}`"
                                        class="max-h-full max-w-full object-contain"
                                    />
                                </div>
                                <template v-if="images.length > 1">
                                    <button
                                        type="button"
                                        class="absolute top-1/2 left-3 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-ink/80 text-paper transition hover:bg-ink"
                                        aria-label="Previous screenshot"
                                        @click="step(-1)"
                                    >
                                        <ChevronLeft class="h-5 w-5" />
                                    </button>
                                    <button
                                        type="button"
                                        class="absolute top-1/2 right-3 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-ink/80 text-paper transition hover:bg-ink"
                                        aria-label="Next screenshot"
                                        @click="step(1)"
                                    >
                                        <ChevronRight class="h-5 w-5" />
                                    </button>
                                    <span class="absolute right-3 bottom-3 rounded-full bg-black/60 px-3 py-1 font-mono text-xs text-white">
                                        {{ galleryIndex + 1 }} / {{ images.length }}
                                    </span>
                                </template>
                            </div>
                            <ul v-if="images.length > 1" class="mt-3 flex gap-2 overflow-x-auto pb-2">
                                <li v-for="(src, i) in images" :key="src" class="shrink-0">
                                    <button
                                        type="button"
                                        class="block overflow-hidden rounded-lg border-2 transition"
                                        :class="i === galleryIndex ? 'border-brand' : 'border-line opacity-70 hover:opacity-100'"
                                        :aria-label="`Show screenshot ${i + 1}`"
                                        @click="galleryIndex = i"
                                    >
                                        <img :src="src" alt="" class="h-16 w-28 object-cover object-top" loading="lazy" />
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <dl class="mt-10 grid gap-6 border-y border-line py-6 sm:grid-cols-3">
                            <div>
                                <dt class="font-mono text-xs tracking-widest text-ink-muted uppercase">Role</dt>
                                <dd class="mt-2 text-sm text-ink">{{ activeProject.role ?? 'Full Stack Developer' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="font-mono text-xs tracking-widest text-ink-muted uppercase">Stack</dt>
                                <dd class="mt-2 text-sm text-ink">{{ activeProject.technologies.join(' · ') }}</dd>
                            </div>
                        </dl>

                        <div class="mt-10 grid gap-10 md:grid-cols-2">
                            <div v-if="activeProject.problem">
                                <h4 class="font-display text-xl font-semibold text-ink">The challenge</h4>
                                <p class="mt-3 text-[0.95rem] leading-relaxed text-ink-soft">{{ activeProject.problem }}</p>
                            </div>
                            <div v-if="activeProject.outcome">
                                <h4 class="font-display text-xl font-semibold text-ink">The result</h4>
                                <p class="mt-3 text-[0.95rem] leading-relaxed text-ink-soft">{{ activeProject.outcome }}</p>
                            </div>
                        </div>

                        <div v-if="activeProject.features?.length" class="mt-10">
                            <h4 class="font-display text-xl font-semibold text-ink">Key features</h4>
                            <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                                <li
                                    v-for="f in activeProject.features"
                                    :key="f"
                                    class="flex items-start gap-3 rounded-2xl border border-line bg-surface p-4 text-sm text-ink-soft"
                                >
                                    <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-brand text-brand-ink">
                                        <Check class="h-3 w-3" />
                                    </span>
                                    {{ f }}
                                </li>
                            </ul>
                        </div>

                        <div v-if="activeProject.links.length" class="mt-10 flex flex-wrap gap-3">
                            <a
                                v-for="link in activeProject.links"
                                :key="link.type"
                                :href="link.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="folio-btn folio-btn-outline"
                            >
                                <BrandIcon v-if="link.type === 'repository'" platform="github" class="h-4 w-4" />
                                <ExternalLink v-else class="h-4 w-4" />
                                {{ link.type === 'repository' ? 'Source code' : 'Live site' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
