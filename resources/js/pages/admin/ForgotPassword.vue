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

    <AuthLayout title="Forgot password?" subtitle="Enter your admin email and we will send you a reset link.">
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

            <p class="text-center text-sm">
                <a href="/admin/login" class="text-ink-soft underline-offset-4 hover:text-ink hover:underline">← Back to sign in</a>
            </p>
        </form>
    </AuthLayout>
</template>
