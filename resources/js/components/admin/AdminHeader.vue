<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { ChevronDown } from '@lucide/vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';

defineProps<{ active: 'content' | 'messages' | 'profile' }>();

const page = usePage();
const unread = computed(() => Number((page.props as Record<string, unknown>).unreadMessages ?? 0));
const user = computed(() => (page.props as Record<string, unknown>).adminUser as { name: string; email: string } | null);
const initial = computed(() => (user.value?.name?.trim()?.[0] ?? 'A').toUpperCase());

/* ── Admin menu (Profile / Log out) ── */
const open = ref(false);
const menuRoot = ref<HTMLElement | null>(null);

const close = () => (open.value = false);
const logout = () => {
    close();
    router.post('/admin/logout');
};

function onPointerDown(event: PointerEvent) {
    if (open.value && menuRoot.value && !menuRoot.value.contains(event.target as Node)) close();
}
function onKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') close();
}

onMounted(() => {
    document.addEventListener('pointerdown', onPointerDown);
    document.addEventListener('keydown', onKeydown);
});
onUnmounted(() => {
    document.removeEventListener('pointerdown', onPointerDown);
    document.removeEventListener('keydown', onKeydown);
});

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
                class="order-last flex w-full items-center gap-1 rounded-full border border-line bg-surface p-1 sm:order-0 sm:w-auto"
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

                <!-- Admin menu -->
                <div ref="menuRoot" class="relative">
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-full border bg-surface py-1 pr-2.5 pl-1 font-medium transition hover:border-brand sm:pr-3"
                        :class="open || active === 'profile' ? 'border-brand' : 'border-line-strong'"
                        aria-haspopup="menu"
                        :aria-expanded="open"
                        @click="open = !open"
                    >
                        <span class="flex size-8 items-center justify-center rounded-full bg-brand text-sm font-bold text-brand-ink">
                            {{ initial }}
                        </span>
                        <span class="hidden sm:inline">Admin</span>
                        <ChevronDown class="size-4 text-ink-soft transition-transform" :class="open ? 'rotate-180' : ''" />
                    </button>

                    <div
                        v-if="open"
                        role="menu"
                        class="absolute right-0 z-30 mt-2 w-64 overflow-hidden rounded-2xl border border-line bg-surface shadow-xl"
                    >
                        <!-- <div class="border-b border-line px-4 py-3">
                            <p class="truncate text-sm font-semibold text-ink">{{ user?.name || 'Admin' }}</p>
                            <p class="truncate text-xs text-ink-muted">{{ user?.email }}</p>
                        </div> -->
                        <Link
                            href="/admin/profile"
                            role="menuitem"
                            class="block px-4 py-3 text-sm text-ink transition hover:bg-paper-2"
                            @click="close"
                        >
                            Profile
                        </Link>
                        <button
                            type="button"
                            role="menuitem"
                            class="block w-full px-4 py-3 text-left text-sm text-red-500 transition hover:bg-paper-2"
                            @click="logout"
                        >
                            Log out
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <slot />
    </header>
</template>
