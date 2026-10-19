<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthInput from '@/components/admin/AuthInput.vue';
import AuthLayout from '@/components/admin/AuthLayout.vue';

const form = useForm({ email: '', password: '', remember: false });
const status = computed(() => (usePage().props as Record<string, unknown>).status as string | null);

function submit() {
    form.post('/admin/login', { onFinish: () => form.reset('password') });
}
</script>

<template>
    <Head title="Dashboard login" />

    <AuthLayout title="Welcome back" subtitle="Sign in to manage your portfolio.">
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

            <AuthInput
                v-model="form.password"
                label="Password"
                type="password"
                icon="lock"
                placeholder="Your password"
                autocomplete="current-password"
                required
                :error="form.errors.password"
            >
            </AuthInput>

            <div class="flex items-center justify-between gap-3 -mt-1">
                <label class="flex items-center gap-2.5 text-sm text-ink-soft">
                    <input v-model="form.remember" type="checkbox" class="size-4 rounded accent-brand" />
                    Keep me signed in
                </label>
                <a href="/admin/forgot-password" class="shrink-0 text-sm font-medium text-brand underline-offset-4 hover:underline">
                    Forgot password?
                </a>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-4 py-3 text-sm font-semibold text-brand-ink shadow-lg shadow-brand/25 transition hover:opacity-90 disabled:opacity-60"
            >
                <span
                    v-if="form.processing"
                    class="size-4 animate-spin rounded-full border-2 border-brand-ink/30 border-t-brand-ink"
                />
                {{ form.processing ? 'Signing in…' : 'Sign in' }}
            </button>

            <div class="pt-1 text-center">
                <p class="text-sm text-slate-500">
                    Not signing in?
                    <a href="/" class="ml-1 font-semibold text-brand underline-offset-4 transition hover:underline">
                        Back to site
                    </a>
                </p>
            </div>
        </form>
    </AuthLayout>
</template>
