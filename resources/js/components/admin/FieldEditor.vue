<script setup lang="ts">
import { computed, ref } from 'vue';
import { blankItem } from '@/admin/schema';
import type { Field } from '@/admin/schema';
import { uploadImage } from '@/admin/upload';
import { asset } from '@/lib/asset';

defineOptions({ name: 'FieldEditor' });

const props = defineProps<{ field: Field; modelValue: any }>();
const emit = defineEmits<{ 'update:modelValue': [value: any] }>();

const input =
    'w-full rounded-xl border border-line-strong bg-paper px-3.5 py-2.5 text-base text-ink outline-none transition placeholder:text-ink-muted focus:border-brand focus:ring-2 focus:ring-brand/30 sm:text-sm';
const smallBtn =
    'rounded-lg border border-line-strong px-3.5 py-2 text-sm font-medium text-ink-soft transition hover:border-brand hover:text-ink disabled:opacity-30 sm:px-2.5 sm:py-1 sm:text-xs';

const update = (value: any) => emit('update:modelValue', value);

const items = computed<any[]>(() => (Array.isArray(props.modelValue) ? props.modelValue : []));
const wide = (f: Field) => ['textarea', 'list', 'strings', 'image', 'object'].includes(f.type);

/* ── object ── */
function setKey(key: string, value: any) {
    update({ ...(props.modelValue ?? {}), [key]: value });
}

/* ── list / strings ── */
function setItem(index: number, value: any) {
    const next = [...items.value];
    next[index] = value;
    update(next);
}
function move(index: number, by: number) {
    const next = [...items.value];
    const target = index + by;
    if (target < 0 || target >= next.length) return;
    [next[index], next[target]] = [next[target], next[index]];
    update(next);
}
function remove(index: number) {
    update(items.value.filter((_, i) => i !== index));
}
function addItem() {
    update([...items.value, props.field.type === 'strings' ? '' : blankItem(props.field.fields ?? [])]);
}
function itemTitle(item: any, index: number): string {
    const key = props.field.itemTitle;
    const text = key ? String(item?.[key] ?? '').trim() : '';
    return text || `Item ${index + 1}`;
}

/* ── image upload ── */
const uploading = ref(false);
const uploadError = ref('');
const preview = computed(() => {
    const value = typeof props.modelValue === 'string' ? props.modelValue : '';
    return value.startsWith('/') && !value.startsWith('//') ? asset(value) : value;
});

async function onFile(event: Event) {
    const element = event.target as HTMLInputElement;
    const file = element.files?.[0];
    if (!file) return;

    uploading.value = true;
    uploadError.value = '';
    try {
        update(await uploadImage(file));
    } catch (error) {
        uploadError.value = error instanceof Error ? error.message : 'Upload failed.';
    } finally {
        uploading.value = false;
        element.value = '';
    }
}
</script>

<template>
    <div>
        <!-- single line text -->
        <label v-if="field.type === 'text'" class="block">
            <span class="mb-1.5 block text-sm font-medium text-ink">{{ field.label }}</span>
            <input
                type="text"
                :class="input"
                :placeholder="field.placeholder"
                :value="modelValue ?? ''"
                @input="update(($event.target as HTMLInputElement).value)"
            />
            <span v-if="field.help" class="mt-1 block text-xs text-ink-muted">{{ field.help }}</span>
        </label>

        <!-- multi-line text -->
        <label v-else-if="field.type === 'textarea'" class="block">
            <span class="mb-1.5 block text-sm font-medium text-ink">{{ field.label }}</span>
            <textarea
                rows="4"
                :class="input"
                :placeholder="field.placeholder"
                :value="modelValue ?? ''"
                @input="update(($event.target as HTMLTextAreaElement).value)"
            />
            <span v-if="field.help" class="mt-1 block text-xs text-ink-muted">{{ field.help }}</span>
        </label>

        <!-- dropdown -->
        <label v-else-if="field.type === 'select'" class="block">
            <span class="mb-1.5 block text-sm font-medium text-ink">{{ field.label }}</span>
            <select :class="input" :value="modelValue ?? ''" @change="update(($event.target as HTMLSelectElement).value)">
                <option v-for="option in field.options" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>
        </label>

        <!-- checkbox -->
        <label v-else-if="field.type === 'bool'" class="flex items-center gap-2.5 py-1 text-sm font-medium text-ink sm:pt-7">
            <input
                type="checkbox"
                class="size-4 accent-brand"
                :checked="!!modelValue"
                @change="update(($event.target as HTMLInputElement).checked)"
            />
            {{ field.label }}
        </label>

        <!-- image -->
        <div v-else-if="field.type === 'image'">
            <span class="mb-1.5 block text-sm font-medium text-ink">{{ field.label }}</span>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                <div
                    class="flex h-36 w-full shrink-0 items-center justify-center overflow-hidden rounded-xl border border-line bg-paper-2 sm:h-28 sm:w-44"
                >
                    <img v-if="preview" :src="preview" alt="" class="h-full w-full object-cover" />
                    <span v-else class="text-xs text-ink-muted">No image</span>
                </div>
                <div class="min-w-0 flex-1 space-y-2">
                    <input
                        type="text"
                        :class="input"
                        placeholder="/images/… or https://…"
                        :value="modelValue ?? ''"
                        @input="update(($event.target as HTMLInputElement).value)"
                    />
                    <div class="flex flex-wrap items-center gap-2">
                        <label :class="[smallBtn, 'cursor-pointer']">
                            {{ uploading ? 'Uploading…' : 'Upload image' }}
                            <input type="file" accept="image/png,image/jpeg,image/webp,image/gif" class="hidden" @change="onFile" />
                        </label>
                        <button v-if="modelValue" type="button" :class="smallBtn" @click="update('')">Remove</button>
                        <span v-if="uploadError" class="text-xs text-red-500">{{ uploadError }}</span>
                    </div>
                    <span v-if="field.help" class="block text-xs text-ink-muted">{{ field.help }}</span>
                </div>
            </div>
        </div>

        <!-- group of fields -->
        <div v-else-if="field.type === 'object'" class="grid gap-4 sm:grid-cols-2">
            <FieldEditor
                v-for="child in field.fields"
                :key="child.key"
                :field="child"
                :model-value="modelValue?.[child.key]"
                :class="wide(child) ? 'sm:col-span-2' : ''"
                @update:model-value="setKey(child.key, $event)"
            />
        </div>

        <!-- list of text lines -->
        <div v-else-if="field.type === 'strings'">
            <span class="mb-1.5 block text-sm font-medium text-ink">{{ field.label }}</span>
            <div class="space-y-2">
                <div v-for="(item, index) in items" :key="index" class="flex flex-col gap-2 sm:flex-row sm:items-start">
                    <textarea
                        v-if="field.multiline"
                        rows="2"
                        :class="input"
                        :value="item"
                        @input="setItem(index, ($event.target as HTMLTextAreaElement).value)"
                    />
                    <input
                        v-else
                        type="text"
                        :class="input"
                        :value="item"
                        @input="setItem(index, ($event.target as HTMLInputElement).value)"
                    />
                    <div class="flex shrink-0 gap-1 sm:pt-1.5">
                        <button type="button" :class="smallBtn" :disabled="index === 0" @click="move(index, -1)">↑</button>
                        <button type="button" :class="smallBtn" :disabled="index === items.length - 1" @click="move(index, 1)">↓</button>
                        <button type="button" :class="smallBtn" @click="remove(index)">✕</button>
                    </div>
                </div>
            </div>
            <button type="button" :class="[smallBtn, 'mt-2']" @click="addItem">+ {{ field.addLabel ?? 'Add' }}</button>
        </div>

        <!-- list of cards -->
        <div v-else-if="field.type === 'list'">
            <span v-if="field.label" class="mb-1.5 block text-sm font-medium text-ink">{{ field.label }}</span>
            <div class="space-y-4">
                <div v-for="(item, index) in items" :key="index" class="rounded-2xl border border-line bg-surface p-3 sm:p-4">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <p class="min-w-0 truncate text-sm font-semibold text-ink">{{ itemTitle(item, index) }}</p>
                        <div class="flex shrink-0 gap-1">
                            <button type="button" :class="smallBtn" :disabled="index === 0" @click="move(index, -1)">↑</button>
                            <button type="button" :class="smallBtn" :disabled="index === items.length - 1" @click="move(index, 1)">↓</button>
                            <button type="button" :class="smallBtn" @click="remove(index)">Delete</button>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <FieldEditor
                            v-for="child in field.fields"
                            :key="child.key"
                            :field="child"
                            :model-value="item?.[child.key]"
                            :class="wide(child) ? 'sm:col-span-2' : ''"
                            @update:model-value="setItem(index, { ...item, [child.key]: $event })"
                        />
                    </div>
                </div>
                <p v-if="!items.length" class="rounded-xl border border-dashed border-line-strong p-4 text-sm text-ink-muted">
                    Nothing here yet.
                </p>
            </div>
            <button type="button" :class="[smallBtn, 'mt-3']" @click="addItem">+ {{ field.addLabel ?? 'Add' }}</button>
        </div>
    </div>
</template>
