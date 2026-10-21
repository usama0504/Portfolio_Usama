<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AdminHeader from '@/components/admin/AdminHeader.vue';
import { defaultContent } from '@/data/portfolio';

type Data = {
    views: number;
    visitors: number;
    prevViews: number;
    prevVisitors: number;
    cvDownloads: number;
    whatsappClicks: number;
    series: { date: string; views: number; visitors: number }[];
    projects: { slug: string; count: number }[];
    referrers: { host: string; count: number }[];
    devices: { device: string; count: number }[];
};

const props = defineProps<{ days: number; data: Data | null; projectNames: Record<string, string> }>();

const ranges = [
    { days: 7, label: '7 days' },
    { days: 30, label: '30 days' },
    { days: 90, label: '90 days' },
];

const defaultNames = Object.fromEntries(defaultContent.projects.map((p) => [p.slug, p.name]));
const nameOf = (slug: string) => props.projectNames[slug] ?? defaultNames[slug] ?? slug;

/** "+12%" compared with the previous period of the same length. */
function change(current: number, previous: number): string {
    if (!previous) return current ? 'new' : '';

    const pct = Math.round(((current - previous) / previous) * 100);

    return `${pct > 0 ? '+' : ''}${pct}%`;
}

const cards = computed(() => {
    const d = props.data;

    if (!d) return [];

    return [
        { label: 'Page views', value: d.views, change: change(d.views, d.prevViews) },
        { label: 'Visitors', value: d.visitors, change: change(d.visitors, d.prevVisitors) },
        { label: 'CV downloads', value: d.cvDownloads, change: '' },
        { label: 'WhatsApp clicks', value: d.whatsappClicks, change: '' },
    ];
});

const maxViews = computed(() => Math.max(1, ...(props.data?.series.map((s) => s.views) ?? [1])));
const barHeight = (value: number) => (value > 0 ? Math.max(3, Math.round((value / maxViews.value) * 100)) : 0);

const dayLabel = (date: string) =>
    new Date(`${date}T00:00:00`).toLocaleDateString(undefined, { day: 'numeric', month: 'short' });

const hasVisits = computed(() => (props.data?.views ?? 0) > 0);
const deviceTotal = computed(() => props.data?.devices.reduce((n, d) => n + d.count, 0) || 1);
const maxProject = computed(() => Math.max(1, ...(props.data?.projects.map((p) => p.count) ?? [1])));

const card = 'rounded-3xl border border-line bg-surface p-4 sm:p-6';
</script>

<template>
    <Head title="Analytics" />

    <div class="min-h-screen bg-paper text-ink">
        <AdminHeader active="analytics" />

        <main class="mx-auto max-w-6xl space-y-4 px-4 py-4 sm:space-y-6 sm:py-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-display text-xl font-bold sm:text-2xl">Visitors</h2>
                    <p class="text-sm text-ink-soft">Private statistics — no cookies, no IP addresses are stored.</p>
                </div>
                <div class="flex rounded-full border border-line bg-surface p-1 text-sm">
                    <Link
                        v-for="range in ranges"
                        :key="range.days"
                        :href="`/admin/analytics?days=${range.days}`"
                        preserve-scroll
                        class="rounded-full px-4 py-1.5 font-medium transition"
                        :class="range.days === days ? 'bg-brand text-brand-ink' : 'text-ink-soft hover:text-ink'"
                    >
                        {{ range.label }}
                    </Link>
                </div>
            </div>

            <!-- table missing -->
            <div v-if="!data" class="rounded-3xl border border-red-500/40 bg-red-500/10 p-6 text-sm text-red-500">
                The statistics table does not exist yet. Run <code class="font-mono">php artisan migrate</code> in the project folder, then refresh.
            </div>

            <template v-else>
                <!-- numbers -->
                <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    <div v-for="item in cards" :key="item.label" :class="card">
                        <p class="text-xs font-medium tracking-wide text-ink-muted uppercase">{{ item.label }}</p>
                        <p class="font-display mt-2 text-3xl font-bold sm:text-4xl">{{ item.value }}</p>
                        <p
                            v-if="item.change"
                            class="mt-1 text-xs font-medium"
                            :class="item.change.startsWith('-') ? 'text-red-500' : 'text-brand'"
                        >
                            {{ item.change }} <span class="font-normal text-ink-muted">vs previous {{ days }} days</span>
                        </p>
                    </div>
                </div>

                <!-- chart -->
                <section :class="card">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-display text-lg font-bold">Visits per day</h3>
                        <p class="flex items-center gap-4 text-xs text-ink-muted">
                            <span class="flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-brand/30" /> Page views</span>
                            <span class="flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-brand" /> Visitors</span>
                        </p>
                    </div>

                    <p v-if="!hasVisits" class="mt-6 rounded-2xl border border-dashed border-line-strong p-8 text-center text-sm text-ink-muted">
                        No visits recorded yet. Visits appear here once people open your website. Your own visits while you are signed in
                        to the dashboard, and search-engine robots, are not counted.
                    </p>

                    <template v-else>
                        <div class="mt-6 flex h-44 items-end gap-px sm:gap-0.5">
                            <div
                                v-for="point in data.series"
                                :key="point.date"
                                class="relative flex h-full min-w-0 flex-1 items-end"
                                :title="`${dayLabel(point.date)}: ${point.views} views, ${point.visitors} visitors`"
                            >
                                <div class="absolute inset-x-0 bottom-0 rounded-t-sm bg-brand/30" :style="{ height: barHeight(point.views) + '%' }" />
                                <div class="absolute inset-x-0 bottom-0 rounded-t-sm bg-brand" :style="{ height: barHeight(point.visitors) + '%' }" />
                            </div>
                        </div>
                        <div class="mt-2 flex justify-between text-xs text-ink-muted">
                            <span>{{ dayLabel(data.series[0].date) }}</span>
                            <span>{{ dayLabel(data.series[data.series.length - 1].date) }}</span>
                        </div>
                    </template>
                </section>

                <div class="grid gap-4 sm:gap-6 lg:grid-cols-2">
                    <!-- projects -->
                    <section :class="card">
                        <h3 class="font-display text-lg font-bold">Most opened projects</h3>
                        <p v-if="!data.projects.length" class="mt-4 text-sm text-ink-muted">No project has been opened yet.</p>
                        <ul v-else class="mt-4 space-y-3">
                            <li v-for="project in data.projects" :key="project.slug">
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="truncate">{{ nameOf(project.slug) }}</span>
                                    <span class="shrink-0 font-semibold">{{ project.count }}</span>
                                </div>
                                <div class="mt-1 h-1.5 rounded-full bg-paper-2">
                                    <div class="h-full rounded-full bg-brand" :style="{ width: Math.round((project.count / maxProject) * 100) + '%' }" />
                                </div>
                            </li>
                        </ul>
                    </section>

                    <!-- where from -->
                    <section :class="card">
                        <h3 class="font-display text-lg font-bold">Where visitors come from</h3>
                        <p v-if="!data.referrers.length" class="mt-4 text-sm text-ink-muted">
                            No referrals yet (visits typed in or opened from WhatsApp/Messenger apps show as direct).
                        </p>
                        <ul v-else class="mt-4 divide-y divide-line text-sm">
                            <li v-for="ref in data.referrers" :key="ref.host" class="flex items-center justify-between gap-3 py-2.5">
                                <span class="truncate">{{ ref.host }}</span>
                                <span class="shrink-0 font-semibold">{{ ref.count }}</span>
                            </li>
                        </ul>
                    </section>
                </div>

                <!-- devices -->
                <section v-if="data.devices.length" :class="card">
                    <h3 class="font-display text-lg font-bold">Devices</h3>
                    <ul class="mt-4 grid gap-4 sm:grid-cols-3">
                        <li v-for="item in data.devices" :key="item.device">
                            <div class="flex items-baseline justify-between text-sm">
                                <span class="capitalize">{{ item.device }}</span>
                                <span class="font-semibold">{{ Math.round((item.count / deviceTotal) * 100) }}%</span>
                            </div>
                            <div class="mt-1 h-1.5 rounded-full bg-paper-2">
                                <div class="h-full rounded-full bg-brand" :style="{ width: Math.round((item.count / deviceTotal) * 100) + '%' }" />
                            </div>
                        </li>
                    </ul>
                </section>

                <p class="px-1 text-xs text-ink-muted">
                    "Visitors" counts a person once per day. Numbers appear only for visits to the Laravel website (not the static GitHub Pages copy).
                </p>
            </template>
        </main>
    </div>
</template>
