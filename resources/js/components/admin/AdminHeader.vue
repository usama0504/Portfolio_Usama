<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps<{ active: 'content' | 'messages' }>();

const page = usePage();
const unread = computed(() => Number((page.props as Record<string, unknown>).unreadMessages ?? 0));

const logout = () => router.post('/admin/logout');

const tab = 'flex-1 rounded-full px-4 py-1.5 text-center text-sm font-medium transition sm:flex-none';
</script>

<template>
    <header class="sticky top-0 z-20 border-b border-line bg-paper/95 backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-x-3 gap-y-2 px-4 py-3">
            <div class="flex min-w-0 items-center gap-3">
                <span
                    class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-ink text-xs font-bold text-paper"
                >
                    ⚙
                </span>
                <h1 class="font-display truncate text-lg font-bold">Dashboard</h1>
            </div>

            <nav
                class="order-last flex w-full items-center gap-1 rounded-full border border-line bg-surface p-1 sm:order-none sm:w-auto"
                aria-label="Dashboard"
            >
                <Link
                    href="/admin"
                    :class="[tab, active === 'content' ? 'bg-brand text-brand-ink' : 'text-ink-soft hover:text-ink']"
                >
                    Content
                </Link>
                <Link
                    href="/admin/messages"
                    :class="[tab, active === 'messages' ? 'bg-brand text-brand-ink' : 'text-ink-soft hover:text-ink']"
                >
                    Messages
                    <span
                        v-if="unread > 0"
                        class="ml-1 inline-flex min-w-5 items-center justify-center rounded-full bg-red-500 px-1.5 text-xs font-semibold text-white"
                    >
                        {{ unread }}
                    </span>
                </Link>
            </nav>

            <div class="flex shrink-0 items-center gap-2 text-sm">
                <a
                    href="/"
                    target="_blank"
                    rel="noopener"
                    class="rounded-full border border-line-strong px-3.5 py-2 text-ink-soft transition hover:text-ink"
                >
                    <span class="hidden sm:inline">View website</span><span class="sm:hidden">Site</span> ↗
                </a>
                <button
                    type="button"
                    class="rounded-full bg-ink px-3.5 py-2 font-medium text-paper transition hover:opacity-90"
                    @click="logout"
                >
                    Log out
                </button>
            </div>
        </div>

        <slot />
    </header>
</template>
