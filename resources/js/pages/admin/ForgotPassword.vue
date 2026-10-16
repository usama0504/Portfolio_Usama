<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const form = useForm({ email: '' });
const status = computed(() => (usePage().props as Record<string, unknown>).status as string | null);

function submit() {
    form.post('/admin/forgot-password', { preserveScroll: true });
}

const input =
    'w-full rounded-xl border border-line-strong bg-paper px-3.5 py-2.5 text-base text-ink outline-none transition placeholder:text-ink-muted focus:border-brand focus:ring-2 focus:ring-brand/30 sm:text-sm';
</script>

<template>
    <Head title="Forgot password" />

    <div class="flex min-h-screen items-center justify-center bg-paper px-4 py-12 text-ink">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="inline-flex size-12 items-center justify-center rounded-full bg-ink text-sm font-bold text-paper">
                    ⚙
                </span>
                <h1 class="font-display mt-4 text-2xl font-bold">Forgot password?</h1>
                <p class="mt-1 text-sm text-ink-soft">Enter your admin email and we will send you a reset link.</p>
            </div>

            <form class="space-y-4 rounded-3xl border border-line bg-surface p-5 shadow-sm sm:p-6" @submit.prevent="submit">
                <p v-if="status" class="rounded-xl border border-brand/40 bg-brand/10 p-3 text-sm text-ink">{{ status }}</p>

                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium">Email</span>
                    <input v-model="form.email" type="email" autocomplete="username" required autofocus :class="input" />
                    <span v-if="form.errors.email" class="mt-1 block text-xs break-words text-red-500">{{ form.errors.email }}</span>
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-full bg-brand px-4 py-2.5 text-sm font-semibold text-brand-ink transition hover:opacity-90 disabled:opacity-60"
                >
                    {{ form.processing ? 'Sending…' : 'Email me a reset link' }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm">
                <a href="/admin/login" class="text-ink-soft underline-offset-4 hover:text-ink hover:underline">← Back to sign in</a>
            </p>
        </div>
    </div>
</template>
