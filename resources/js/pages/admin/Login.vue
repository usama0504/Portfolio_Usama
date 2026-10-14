<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({ email: '', password: '', remember: false });

function submit() {
    form.post('/admin/login', { onFinish: () => form.reset('password') });
}

const input =
    'w-full rounded-xl border border-line-strong bg-paper px-3.5 py-2.5 text-base text-ink outline-none transition placeholder:text-ink-muted focus:border-brand focus:ring-2 focus:ring-brand/30 sm:text-sm';
</script>

<template>
    <Head title="Dashboard login" />

    <div class="flex min-h-screen items-center justify-center bg-paper px-4 py-12 text-ink">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <span class="inline-flex size-12 items-center justify-center rounded-full bg-ink text-sm font-bold text-paper">
                    ⚙
                </span>
                <h1 class="font-display mt-4 text-2xl font-bold">Dashboard</h1>
                <p class="mt-1 text-sm text-ink-soft">Sign in to edit your portfolio.</p>
            </div>

            <form class="space-y-4 rounded-3xl border border-line bg-surface p-5 shadow-sm sm:p-6" @submit.prevent="submit">
                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium">Email</span>
                    <input v-model="form.email" type="email" autocomplete="username" required autofocus :class="input" />
                    <span v-if="form.errors.email" class="mt-1 block text-xs text-red-500">{{ form.errors.email }}</span>
                </label>

                <label class="block">
                    <span class="mb-1.5 block text-sm font-medium">Password</span>
                    <input v-model="form.password" type="password" autocomplete="current-password" required :class="input" />
                    <span v-if="form.errors.password" class="mt-1 block text-xs text-red-500">{{ form.errors.password }}</span>
                </label>

                <label class="flex items-center gap-2 text-sm text-ink-soft">
                    <input v-model="form.remember" type="checkbox" class="size-4 accent-brand" />
                    Keep me signed in
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-full bg-brand px-4 py-2.5 text-sm font-semibold text-brand-ink transition hover:opacity-90 disabled:opacity-60"
                >
                    {{ form.processing ? 'Signing in…' : 'Sign in' }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm">
                <a href="/" class="text-ink-soft underline-offset-4 hover:text-ink hover:underline">← Back to website</a>
            </p>
        </div>
    </div>
</template>
