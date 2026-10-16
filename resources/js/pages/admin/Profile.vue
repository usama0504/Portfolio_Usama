<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { formatDate } from '@/admin/format';
import AdminHeader from '@/components/admin/AdminHeader.vue';

const props = defineProps<{ account: { name: string; email: string; created_at: string | null } }>();

const status = computed(() => (usePage().props as Record<string, unknown>).status as string | null);

/* ── name & email ── */
const details = useForm({ name: props.account.name, email: props.account.email, current_password: '' });
const emailChanged = computed(() => details.email.trim() !== props.account.email);

function saveDetails() {
    details.put('/admin/profile', {
        preserveScroll: true,
        onSuccess: () => details.reset('current_password'),
    });
}

/* ── password ── */
const password = useForm({ current_password: '', password: '', password_confirmation: '' });

function savePassword() {
    password.put('/admin/password', {
        preserveScroll: true,
        onSuccess: () => password.reset(),
    });
}

const input =
    'w-full rounded-xl border border-line-strong bg-paper px-3.5 py-2.5 text-base text-ink outline-none transition placeholder:text-ink-muted focus:border-brand focus:ring-2 focus:ring-brand/30 sm:text-sm';
const card = 'rounded-3xl border border-line bg-surface p-4 sm:p-6';
const button =
    'rounded-full bg-brand px-6 py-3 text-sm font-semibold text-brand-ink transition hover:opacity-90 disabled:opacity-60 sm:py-2.5';
</script>

<template>
    <Head title="Profile" />

    <div class="min-h-screen bg-paper text-ink">
        <AdminHeader active="profile" />

        <main class="mx-auto max-w-2xl space-y-4 px-4 py-4 sm:space-y-6 sm:py-6">
            <p v-if="status" class="rounded-2xl border border-brand/40 bg-brand/10 p-4 text-sm font-medium text-ink">
                {{ status }}
            </p>

            <!-- who you are -->
            <section :class="[card, 'flex items-center gap-4']">
                <span
                    class="flex size-16 shrink-0 items-center justify-center rounded-full bg-brand text-2xl font-bold text-brand-ink"
                >
                    {{ (account.name?.trim()?.[0] ?? 'A').toUpperCase() }}
                </span>
                <div class="min-w-0">
                    <h2 class="font-display truncate text-xl font-bold sm:text-2xl">{{ account.name }}</h2>
                    <p class="truncate text-sm text-ink-soft">{{ account.email }}</p>
                    <p v-if="account.created_at" class="mt-0.5 text-xs text-ink-muted">
                        Admin since {{ formatDate(account.created_at) }}
                    </p>
                </div>
            </section>

            <!-- details -->
            <form :class="card" @submit.prevent="saveDetails">
                <h3 class="font-display text-lg font-bold">Your details</h3>
                <p class="mt-1 mb-5 text-sm text-ink-soft">The email is what you sign in with and where reset links are sent.</p>

                <div class="space-y-4">
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium">Name</span>
                        <input v-model="details.name" type="text" autocomplete="name" required :class="input" />
                        <span v-if="details.errors.name" class="mt-1 block text-xs wrap-break-word text-red-500">{{ details.errors.name }}</span>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium">Email</span>
                        <input v-model="details.email" type="email" autocomplete="username" required :class="input" />
                        <span v-if="details.errors.email" class="mt-1 block text-xs wrap-break-word text-red-500">{{ details.errors.email }}</span>
                    </label>

                    <label v-if="emailChanged" class="block">
                        <span class="mb-1.5 block text-sm font-medium">Current password</span>
                        <input v-model="details.current_password" type="password" autocomplete="current-password" :class="input" />
                        <span class="mt-1 block text-xs text-ink-muted">Needed to change your email.</span>
                        <span v-if="details.errors.current_password" class="mt-1 block text-xs wrap-break-word text-red-500">
                            {{ details.errors.current_password }}
                        </span>
                    </label>
                </div>

                <button type="submit" :disabled="details.processing" :class="[button, 'mt-5 w-full sm:w-auto']">
                    {{ details.processing ? 'Saving…' : 'Save details' }}
                </button>
            </form>

            <!-- password -->
            <form :class="card" @submit.prevent="savePassword">
                <h3 class="font-display text-lg font-bold">Change password</h3>
                <p class="mt-1 mb-5 text-sm text-ink-soft">Use at least 8 characters.</p>

                <div class="space-y-4">
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium">Current password</span>
                        <input v-model="password.current_password" type="password" autocomplete="current-password" required :class="input" />
                        <span v-if="password.errors.current_password" class="mt-1 block text-xs wrap-break-word text-red-500">
                            {{ password.errors.current_password }}
                        </span>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium">New password</span>
                        <input v-model="password.password" type="password" autocomplete="new-password" required minlength="8" :class="input" />
                        <span v-if="password.errors.password" class="mt-1 block text-xs wrap-break-word text-red-500">{{ password.errors.password }}</span>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-sm font-medium">Confirm new password</span>
                        <input v-model="password.password_confirmation" type="password" autocomplete="new-password" required :class="input" />
                    </label>
                </div>

                <button type="submit" :disabled="password.processing" :class="[button, 'mt-5 w-full sm:w-auto']">
                    {{ password.processing ? 'Saving…' : 'Change password' }}
                </button>
            </form>
        </main>
    </div>
</template>
