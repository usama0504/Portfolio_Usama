<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthInput from '@/components/admin/AuthInput.vue';
import AuthLayout from '@/components/admin/AuthLayout.vue';

const props = defineProps<{ token: string; email: string }>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const problems = computed(() => Object.values(form.errors));

function submit() {
    form.post('/admin/reset-password', { onFinish: () => form.reset('password', 'password_confirmation') });
}
</script>

<template>
    <Head title="Choose a new password" />

    <AuthLayout title="New password" subtitle="Create a new password to regain access to your dashboard.">
        <form class="space-y-5" @submit.prevent="submit">
            <ul
                v-if="problems.length"
                class="space-y-1 rounded-xl border border-red-500/40 bg-red-500/10 p-3.5 text-sm wrap-break-word text-red-500"
            >
                <li v-for="(problem, index) in problems" :key="index">{{ problem }}</li>
            </ul>

            <AuthInput v-model="form.email" label="Email" type="email" icon="mail" autocomplete="username" required />

            <AuthInput
                v-model="form.password"
                label="New password"
                type="password"
                icon="lock"
                autocomplete="new-password"
                required
                autofocus
                :minlength="8"
            />

            <AuthInput
                v-model="form.password_confirmation"
                label="Confirm new password"
                type="password"
                icon="lock"
                autocomplete="new-password"
                required
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
                {{ form.processing ? 'Saving…' : 'Change password' }}
            </button>

            <p class="text-center text-xs text-ink-muted">
                Link not working?
                <a href="/admin/forgot-password" class="text-brand underline-offset-4 hover:underline">Request a new one</a>
                — only the newest email works.
            </p>
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
