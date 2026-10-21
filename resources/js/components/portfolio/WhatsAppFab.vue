<script setup lang="ts">
import { MessageCircle } from '@lucide/vue';
import { personalInfo } from '@/data/portfolio';
import { track } from '@/lib/track';
</script>

<!-- Floating "chat on WhatsApp" button — shown only when a number is saved in the dashboard. -->
<template>
    <a
        v-if="personalInfo.whatsappUrl"
        :href="personalInfo.whatsappUrl"
        target="_blank"
        rel="noopener"
        aria-label="Chat on WhatsApp"
        class="group fixed right-4 bottom-4 z-40 flex items-center rounded-full bg-[#25D366] p-3.5 text-white shadow-lg shadow-black/30 transition hover:scale-105 sm:right-6 sm:bottom-6"
        @click="track('whatsapp_click', 'floating')"
    >
        <MessageCircle class="size-6" />
        <span
            class="hidden max-w-0 overflow-hidden text-sm font-semibold whitespace-nowrap transition-all duration-300 group-hover:ml-2 group-hover:max-w-40 sm:inline"
        >
            WhatsApp
        </span>
    </a>
</template>
