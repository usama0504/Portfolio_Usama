<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';
import { sections } from '@/admin/schema';
import AdminHeader from '@/components/admin/AdminHeader.vue';
import FieldEditor from '@/components/admin/FieldEditor.vue';
import { defaultContent } from '@/data/portfolio';

const props = defineProps<{ content: Record<string, any> | unknown[] }>();

const clone = <T,>(value: T): T => JSON.parse(JSON.stringify(value));
const stored = computed<Record<string, any>>(() => (Array.isArray(props.content) ? {} : props.content));
const customised = computed(() => new Set(Object.keys(stored.value)));

// One editable copy per section, starting from what is saved (or the defaults).
const model = reactive<Record<string, any>>({});
for (const section of sections) {
    const base = (defaultContent as Record<string, any>)[section.id];
    const saved = stored.value[section.id];
    model[section.id] = clone(section.id === 'profile' || section.id === 'seo' ? { ...base, ...(saved ?? {}) } : (saved ?? base));
}

const currentId = ref(sections[0].id);
const current = computed(() => sections.find((s) => s.id === currentId.value) ?? sections[0]);

const page = usePage();
const errors = computed(() => Object.values((page.props.errors ?? {}) as Record<string, string>));

const saving = ref(false);
const message = ref('');
let timer: ReturnType<typeof setTimeout> | undefined;

function flash(text: string) {
    message.value = text;
    clearTimeout(timer);
    timer = setTimeout(() => (message.value = ''), 5000);
}

function select(id: string) {
    currentId.value = id;
    message.value = '';
}

function save() {
    const id = currentId.value;
    saving.value = true;
    message.value = '';

    router.put(
        `/admin/content/${id}`,
        { data: clone(model[id]) },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => flash('Saved ✓ — your website is updated.'),
            onFinish: () => (saving.value = false),
        },
    );
}

function reset() {
    const id = currentId.value;
    if (!confirm(`Reset "${current.value.label}" to the original default content?`)) return;

    router.delete(`/admin/content/${id}`, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            model[id] = clone((defaultContent as Record<string, any>)[id]);
            flash('Reset to default.');
        },
    });
}

</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-paper text-ink">
        <AdminHeader active="content">
            <!-- phones & tablets: section switcher -->
            <div class="mx-auto max-w-6xl px-4 pb-3 lg:hidden">
                <label class="sr-only" for="section-switcher">Section</label>
                <select
                    id="section-switcher"
                    :value="currentId"
                    class="w-full rounded-xl border border-line-strong bg-surface px-3.5 py-2.5 text-base font-medium text-ink outline-none focus:border-brand"
                    @change="select(($event.target as HTMLSelectElement).value)"
                >
                    <option v-for="section in sections" :key="section.id" :value="section.id">
                        {{ section.label }}{{ customised.has(section.id) ? '  •' : '' }}
                    </option>
                </select>
            </div>
        </AdminHeader>

        <div class="mx-auto grid max-w-6xl gap-4 px-4 py-4 sm:gap-6 sm:py-6 lg:grid-cols-[220px_1fr]">
            <nav class="hidden gap-2 lg:sticky lg:top-24 lg:flex lg:flex-col lg:self-start" aria-label="Sections">
                <button
                    v-for="section in sections"
                    :key="section.id"
                    type="button"
                    class="flex shrink-0 items-center justify-between gap-3 rounded-xl px-4 py-2.5 text-left text-sm font-medium transition"
                    :class="
                        section.id === currentId
                            ? 'bg-brand text-brand-ink'
                            : 'border border-line bg-surface text-ink-soft hover:text-ink'
                    "
                    @click="select(section.id)"
                >
                    {{ section.label }}
                    <span
                        v-if="customised.has(section.id)"
                        class="size-1.5 rounded-full"
                        :class="section.id === currentId ? 'bg-brand-ink' : 'bg-brand'"
                        title="Edited from the dashboard"
                    />
                </button>
            </nav>

            <main class="min-w-0">
                <ul
                    v-if="errors.length"
                    class="mb-4 space-y-1 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-500"
                >
                    <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                </ul>

                <div class="rounded-3xl border border-line bg-surface p-4 sm:p-7">
                    <h2 class="font-display text-xl font-bold sm:text-2xl">{{ current.label }}</h2>
                    <p class="mt-1 mb-6 text-sm text-ink-soft">{{ current.description }}</p>

                    <FieldEditor
                        :key="current.id"
                        :field="{ ...current.field, label: '' }"
                        :model-value="model[current.id]"
                        @update:model-value="model[current.id] = $event"
                    />
                </div>

                <div
                    class="sticky bottom-[max(0.5rem,env(safe-area-inset-bottom))] z-10 mt-4 flex flex-wrap items-center gap-2 rounded-2xl border border-line-strong bg-paper p-3 shadow-lg sm:gap-3 sm:p-4"
                >
                    <button
                        type="button"
                        :disabled="saving"
                        class="flex-1 rounded-full bg-brand px-6 py-3 text-sm font-semibold text-brand-ink transition hover:opacity-90 disabled:opacity-60 sm:flex-none sm:py-2.5"
                        @click="save"
                    >
                        {{ saving ? 'Saving…' : `Save ${current.label}` }}
                    </button>
                    <button
                        v-if="customised.has(current.id)"
                        type="button"
                        class="flex-1 rounded-full border border-line-strong px-4 py-3 text-sm text-ink-soft transition hover:text-ink sm:flex-none sm:py-2.5"
                        @click="reset"
                    >
                        Reset to default
                    </button>
                    <span v-if="message" class="w-full text-sm font-medium text-brand sm:w-auto">{{ message }}</span>
                </div>

            </main>
        </div>
    </div>
</template>
