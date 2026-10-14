<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { shortDate } from '@/admin/format';
import AdminHeader from '@/components/admin/AdminHeader.vue';
import MessageThread from '@/components/admin/MessageThread.vue';
import type { Thread } from '@/components/admin/MessageThread.vue';

defineProps<{
    messages: {
        id: number;
        name: string;
        email: string;
        subject: string;
        preview: string;
        unread: boolean;
        replied: boolean;
        created_at: string | null;
    }[];
    selected: Thread | null;
}>();
</script>

<template>
    <Head title="Messages" />

    <div class="min-h-screen bg-paper text-ink">
        <AdminHeader active="messages" />

        <div class="mx-auto grid max-w-6xl gap-4 px-4 py-4 sm:gap-6 sm:py-6 lg:grid-cols-[360px_1fr]">
            <!-- inbox list (on phones it hides while a message is open) -->
            <aside :class="selected ? 'hidden lg:block' : 'block'">
                <p v-if="!messages.length" class="rounded-3xl border border-dashed border-line-strong p-8 text-center text-sm text-ink-muted">
                    No messages yet. When someone uses the contact form on your website, it appears here (and in your Gmail).
                </p>

                <ul v-else class="space-y-2">
                    <li v-for="m in messages" :key="m.id">
                        <Link
                            :href="`/admin/messages/${m.id}`"
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
                            <span
                                v-if="m.replied"
                                class="mt-2 inline-block rounded-full bg-brand/15 px-2 py-0.5 text-xs font-medium text-brand"
                            >
                                Replied
                            </span>
                        </Link>
                    </li>
                </ul>
            </aside>

            <!-- open message -->
            <main :class="selected ? 'block' : 'hidden lg:block'" class="min-w-0">
                <MessageThread v-if="selected" :key="selected.id" :message="selected" />
                <p v-else class="rounded-3xl border border-dashed border-line-strong p-10 text-center text-sm text-ink-muted">
                    Select a message to read it and reply.
                </p>
            </main>
        </div>
    </div>
</template>
