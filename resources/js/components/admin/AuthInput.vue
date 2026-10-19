<script setup lang="ts">
import { Eye, EyeOff, Lock, Mail } from '@lucide/vue';
import { computed, ref } from 'vue';

const props = defineProps<{
    label: string;
    type?: 'text' | 'email' | 'password';
    icon?: 'mail' | 'lock';
    error?: string;
    hint?: string;
    placeholder?: string;
    autocomplete?: string;
    required?: boolean;
    autofocus?: boolean;
    minlength?: number;
}>();

const model = defineModel<string>({ default: '' });

const reveal = ref(false);
const inputType = computed(() => (props.type === 'password' && reveal.value ? 'text' : (props.type ?? 'text')));
</script>

<template>
    <label class="block">
        <span class="mb-1.5 flex items-center justify-between gap-3 text-sm font-medium text-ink">
            <span>{{ label }}</span>
            <slot name="aside" />
        </span>

        <span class="relative block">
            <component
                :is="icon === 'mail' ? Mail : Lock"
                v-if="icon"
                class="pointer-events-none absolute top-1/2 left-3.5 size-[18px] -translate-y-1/2 text-ink-muted"
            />
            <input
                v-model="model"
                :type="inputType"
                :placeholder="placeholder"
                :autocomplete="autocomplete"
                :required="required"
                :autofocus="autofocus"
                :minlength="minlength"
                class="w-full rounded-xl border bg-surface py-3 text-base text-ink outline-none transition placeholder:text-ink-muted focus:border-brand focus:ring-4 focus:ring-brand/15 sm:text-sm"
                :class="[
                    icon ? 'pl-11' : 'pl-3.5',
                    type === 'password' ? 'pr-12' : 'pr-3.5',
                    error ? 'border-red-500' : 'border-line-strong',
                ]"
            />
            <button
                v-if="type === 'password'"
                type="button"
                class="absolute top-1/2 right-1.5 -translate-y-1/2 rounded-lg p-2 text-ink-muted transition hover:text-ink"
                :aria-label="reveal ? 'Hide password' : 'Show password'"
                @click="reveal = !reveal"
            >
                <component :is="reveal ? EyeOff : Eye" class="size-[18px]" />
            </button>
        </span>

        <span v-if="error" class="mt-1.5 block text-xs break-words text-red-500">{{ error }}</span>
        <span v-else-if="hint" class="mt-1.5 block text-xs text-ink-muted">{{ hint }}</span>
    </label>
</template>
