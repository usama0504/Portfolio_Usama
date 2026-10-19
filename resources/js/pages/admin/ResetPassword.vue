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

    <AuthLayout title="New password" subtitle="Choose a new password for your dashboard.">
        <form class="space-y-5" @submit.prevent="submit">
            <ul
                v-if="problems.length"
                class="space-y-1 rounded-xl border border-red-500/40 bg-red-500/10 p-3.5 text-sm break-words text-red-500"
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
                hint="At least 8 characters."
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
            <p class="text-center text-sm">
                <a href="/admin/login" class="text-ink-soft underline-offset-4 hover:text-ink hover:underline">← Back to sign in</a>
            </p>
        </form>
    </AuthLayout>
</template>
