<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthInput from '@/components/admin/AuthInput.vue';
import AuthLayout from '@/components/admin/AuthLayout.vue';

const form = useForm({ email: '' });
const status = computed(() => (usePage().props as Record<string, unknown>).status as string | null);

function submit() {
    form.post('/admin/forgot-password', { preserveScroll: true });
}
</script>

<template>
    <Head title="Forgot password" />

    <AuthLayout title="Forgot password?" subtitle="Enter your email and we’ll send you a link to reset your password.">
        <form class="space-y-5" @submit.prevent="submit">
            <p v-if="status" class="rounded-xl border border-brand/40 bg-brand/10 p-3.5 text-sm text-ink">{{ status }}</p>

            <AuthInput
                v-model="form.email"
                label="Email"
                type="email"
                icon="mail"
                placeholder="you@example.com"
                autocomplete="username"
                required
                autofocus
                :error="form.errors.email"
            />

            <button
                type="submit"
                :disabled="form.processing"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-4 py-3 text-sm font-semibold text-brand-ink shadow-lg shadow-brand/25 transition hover:opacity-90 disabled:opacity-60"
            >
                <span
                    v-if="form.processing"
                    class="size-4 animate-spin rounded-full border-2 border-brand-ink/30 border-t-brand-ink"
                />
                {{ form.processing ? 'Sending…' : 'Email me a reset link' }}
            </button>

            <div class="border-t border-line pt-4 dark:border-slate-700">
                <a
                    href="/admin/login"
                    class="mx-auto flex w-fit items-center justify-center gap-2 rounded-xl border border-line-strong bg-surface px-4 py-2.5 text-sm font-semibold text-ink-soft shadow-sm transition hover:border-brand hover:bg-brand/10 hover:text-ink focus:outline-none focus:ring-4 focus:ring-brand/20 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-teal-400 dark:hover:bg-teal-400/10 dark:hover:text-white"
                >
                    <span aria-hidden="true">←</span>
                    Back to sign in
                </a>
            </div>
        </form>
    </AuthLayout>
</template>
