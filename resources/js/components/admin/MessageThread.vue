<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3';
import { formatDate } from '@/admin/format';

export type Thread = {
    id: number;
    name: string;
    email: string;
    subject: string;
    body: string | null;
    created_at: string | null;
    replies: { id: number; body: string; created_at: string | null }[];
};

const props = defineProps<{ message: Thread }>();

const form = useForm({ body: '' });

function send() {
    form.post(`/admin/messages/${props.message.id}/reply`, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function remove() {
    if (!confirm('Delete this message and your replies to it?')) return;

    router.delete(`/admin/messages/${props.message.id}`);
}
</script>

<template>
    <article class="rounded-3xl border border-line bg-surface p-4 sm:p-6">
        <Link href="/admin/messages" class="mb-4 inline-block text-sm font-medium text-brand lg:hidden">← Inbox</Link>

        <h2 class="font-display text-xl font-bold break-words sm:text-2xl">{{ message.subject }}</h2>
        <p class="mt-2 text-sm break-words text-ink-soft">
            <span class="font-semibold text-ink">{{ message.name }}</span>
            ·
            <a :href="`mailto:${message.email}`" class="underline underline-offset-2 hover:text-ink">{{ message.email }}</a>
        </p>
        <p class="mt-0.5 text-xs text-ink-muted">{{ formatDate(message.created_at) }}</p>

        <div class="mt-5 rounded-2xl bg-paper p-4 text-sm leading-relaxed break-words whitespace-pre-wrap text-ink">
            {{ message.body || '(no message)' }}
        </div>

        <div v-for="reply in message.replies" :key="reply.id" class="mt-4 rounded-2xl border border-brand/40 bg-brand/5 p-4">
            <p class="mb-2 text-xs font-semibold text-brand">You replied · {{ formatDate(reply.created_at) }}</p>
            <p class="text-sm leading-relaxed break-words whitespace-pre-wrap text-ink">{{ reply.body }}</p>
        </div>

        <form class="mt-6 border-t border-line pt-5" @submit.prevent="send">
            <label class="block">
                <span class="mb-1.5 block text-sm font-medium text-ink">Reply to {{ message.name }}</span>
                <textarea
                    v-model="form.body"
                    rows="6"
                    placeholder="Write your reply…"
                    class="w-full rounded-xl border border-line-strong bg-paper px-3.5 py-2.5 text-base text-ink outline-none transition placeholder:text-ink-muted focus:border-brand focus:ring-2 focus:ring-brand/30 sm:text-sm"
                />
            </label>
            <p v-if="form.errors.body" class="mt-2 rounded-xl border border-red-500/40 bg-red-500/10 p-3 text-sm break-words text-red-500">
                {{ form.errors.body }}
            </p>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <button
                    type="submit"
                    :disabled="form.processing || !form.body.trim()"
                    class="flex-1 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-brand-ink transition hover:opacity-90 disabled:opacity-50 sm:flex-none sm:py-2.5"
                >
                    {{ form.processing ? 'Sending…' : 'Send reply' }}
                </button>
                <button
                    type="button"
                    class="flex-1 rounded-full border border-line-strong px-4 py-3 text-sm text-ink-soft transition hover:text-red-500 sm:flex-none sm:py-2.5"
                    @click="remove"
                >
                    Delete
                </button>
            </div>
            <p class="mt-3 text-xs text-ink-muted">Sent from your Gmail to {{ message.email }}. Their answer arrives in your Gmail inbox.</p>
        </form>
    </article>
</template>
