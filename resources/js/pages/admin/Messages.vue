<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Download, Search } from '@lucide/vue';
import { onUnmounted, ref, watch } from 'vue';
import { shortDate } from '@/admin/format';
import { qs } from '@/admin/query';
import AdminHeader from '@/components/admin/AdminHeader.vue';
import MessageThread from '@/components/admin/MessageThread.vue';
import type { Thread } from '@/components/admin/MessageThread.vue';

type Row = {
    id: number;
    name: string;
    email: string;
    subject: string;
    preview: string;
    unread: boolean;
    replied: boolean;
    archived: boolean;
    created_at: string | null;
};

const props = defineProps<{
    messages: { data: Row[]; current_page: number; last_page: number; total: number };
    selected: Thread | null;
    filter: string;
    q: string;
    counts: Record<string, number>;
}>();

const filters = [
    { key: 'inbox', label: 'Inbox' },
    { key: 'unread', label: 'Unread' },
    { key: 'unreplied', label: 'Needs reply' },
    { key: 'replied', label: 'Replied' },
    { key: 'archived', label: 'Archived' },
];

const emptyText: Record<string, string> = {
    inbox: 'No messages yet. When someone uses the contact form on your website, it appears here (and in your Gmail).',
    unread: 'Nothing unread — you are all caught up.',
    unreplied: 'Every message has been answered.',
    replied: 'You have not replied to any message yet.',
    archived: 'The archive is empty.',
};

/* ── search (waits a moment after typing) ── */
const search = ref(props.q);
let timer: ReturnType<typeof setTimeout> | undefined;

watch(search, (value) => {
    clearTimeout(timer);
    timer = setTimeout(() => {
        router.get(
            '/admin/messages',
            { filter: props.filter === 'inbox' ? undefined : props.filter, q: value.trim() || undefined },
            { preserveState: true, preserveScroll: true, replace: true },
        );
    }, 350);
});
onUnmounted(() => clearTimeout(timer));

/* ── links that keep the current tab / search / page ── */
const here = () => ({ filter: props.filter, q: props.q, page: props.messages.current_page });
const tabHref = (key: string) => `/admin/messages${qs({ filter: key, q: props.q })}`;
const rowHref = (id: number) => `/admin/messages/${id}${qs(here())}`;
const pageHref = (page: number) => `/admin/messages${qs({ filter: props.filter, q: props.q, page })}`;
const exportHref = () => `/admin/messages/export${qs({ filter: props.filter, q: props.q })}`;

const markAllRead = () => router.post('/admin/messages/read-all', {}, { preserveScroll: true });

const chip = 'flex shrink-0 items-center gap-1.5 rounded-full border px-3.5 py-1.5 text-sm font-medium transition';
</script>

<template>
    <Head title="Messages" />

    <div class="min-h-screen bg-paper text-ink">
        <AdminHeader active="messages" />

        <div class="mx-auto grid max-w-6xl gap-4 px-4 py-4 sm:gap-6 sm:py-6 lg:grid-cols-[380px_1fr]">
            <!-- inbox list (on phones it hides while a message is open) -->
            <aside :class="selected ? 'hidden lg:block' : 'block'" class="min-w-0">
                <!-- search -->
                <label class="relative block">
                    <span class="sr-only">Search messages</span>
                    <Search class="pointer-events-none absolute top-1/2 left-3.5 size-4.5 -translate-y-1/2 text-ink-muted" />
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Search name, email or message…"
                        class="w-full rounded-xl border border-line-strong bg-surface py-2.5 pr-3.5 pl-11 text-base text-ink outline-none transition placeholder:text-ink-muted focus:border-brand focus:ring-4 focus:ring-brand/15 sm:text-sm"
                    />
                </label>

                <!-- tabs -->
                <nav class="mt-3 -mx-4 flex gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:px-0" aria-label="Message filters">
                    <Link
                        v-for="item in filters"
                        :key="item.key"
                        :href="tabHref(item.key)"
                        preserve-scroll
                        :class="[
                            chip,
                            item.key === filter
                                ? 'border-brand bg-brand text-brand-ink'
                                : 'border-line bg-surface text-ink-soft hover:text-ink',
                        ]"
                    >
                        {{ item.label }}
                        <span
                            class="rounded-full px-1.5 text-xs"
                            :class="item.key === filter ? 'bg-brand-ink/15' : 'bg-paper-2 text-ink-muted'"
                        >
                            {{ counts[item.key] ?? 0 }}
                        </span>
                    </Link>
                </nav>

                <!-- actions -->
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                    <button
                        v-if="(counts.unread ?? 0) > 0"
                        type="button"
                        class="rounded-full border border-line-strong px-3.5 py-1.5 text-ink-soft transition hover:text-ink"
                        @click="markAllRead"
                    >
                        Mark all as read
                    </button>
                    <a
                        v-if="messages.total > 0"
                        :href="exportHref()"
                        class="inline-flex items-center gap-1.5 rounded-full border border-line-strong px-3.5 py-1.5 text-ink-soft transition hover:text-ink"
                    >
                        <Download class="size-4" /> Export CSV
                    </a>
                    <span v-if="q" class="text-xs text-ink-muted">{{ messages.total }} result{{ messages.total === 1 ? '' : 's' }}</span>
                </div>

                <!-- list -->
                <p
                    v-if="!messages.data.length"
                    class="mt-4 rounded-3xl border border-dashed border-line-strong p-8 text-center text-sm text-ink-muted"
                >
                    {{ q ? `No messages match “${q}”.` : (emptyText[filter] ?? emptyText.inbox) }}
                </p>

                <ul v-else class="mt-4 space-y-2">
                    <li v-for="m in messages.data" :key="m.id">
                        <Link
                            :href="rowHref(m.id)"
                            preserve-scroll
                            class="block rounded-2xl border p-3.5 transition"
                            :class="
                                selected?.id === m.id
                                    ? 'border-brand bg-brand/10'
                                    : 'border-line bg-surface hover:border-line-strong'
                            "
                        >
                            <div class="flex items-center justify-between gap-3">
                                <p class="flex min-w-0 items-center gap-2 text-sm" :class="m.unread ? 'font-bold text-ink' : 'text-ink-soft'">
                                    <span v-if="m.unread" class="size-2 shrink-0 rounded-full bg-brand" aria-label="Unread" />
                                    <span class="truncate">{{ m.name }}</span>
                                </p>
                                <span class="shrink-0 text-xs text-ink-muted">{{ shortDate(m.created_at) }}</span>
                            </div>
                            <p class="mt-1 truncate text-sm" :class="m.unread ? 'font-semibold text-ink' : 'text-ink-soft'">
                                {{ m.subject }}
                            </p>
                            <p class="mt-0.5 truncate text-xs text-ink-muted">{{ m.preview || '(no message)' }}</p>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span v-if="m.replied" class="rounded-full bg-brand/15 px-2 py-0.5 text-xs font-medium text-brand">
                                    Replied
                                </span>
                                <span v-else-if="!m.archived" class="rounded-full bg-amber-500/15 px-2 py-0.5 text-xs font-medium text-amber-600">
                                    Needs reply
                                </span>
                                <span v-if="m.archived" class="rounded-full bg-paper-2 px-2 py-0.5 text-xs font-medium text-ink-muted">
                                    Archived
                                </span>
                            </div>
                        </Link>
                    </li>
                </ul>

                <!-- pages -->
                <div v-if="messages.last_page > 1" class="mt-4 flex items-center justify-between text-sm">
                    <Link
                        v-if="messages.current_page > 1"
                        :href="pageHref(messages.current_page - 1)"
                        preserve-scroll
                        class="rounded-full border border-line-strong px-4 py-1.5 text-ink-soft transition hover:text-ink"
                    >
                        ← Newer
                    </Link>
                    <span v-else />
                    <span class="text-xs text-ink-muted">Page {{ messages.current_page }} of {{ messages.last_page }}</span>
                    <Link
                        v-if="messages.current_page < messages.last_page"
                        :href="pageHref(messages.current_page + 1)"
                        preserve-scroll
                        class="rounded-full border border-line-strong px-4 py-1.5 text-ink-soft transition hover:text-ink"
                    >
                        Older →
                    </Link>
                    <span v-else />
                </div>
            </aside>

            <!-- open message -->
            <main :class="selected ? 'block' : 'hidden lg:block'" class="min-w-0">
                <MessageThread v-if="selected" :key="selected.id" :message="selected" :query="qs(here())" />
                <p v-else class="rounded-3xl border border-dashed border-line-strong p-10 text-center text-sm text-ink-muted">
                    Select a message to read it and reply.
                </p>
            </main>
        </div>
    </div>
</template>
