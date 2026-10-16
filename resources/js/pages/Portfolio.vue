<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, onUnmounted, watch } from 'vue';
import PortfolioSections from '@/components/portfolio/PortfolioSections.vue';
import { applyContent, pageMetadata, personalInfo, site, visibleSocialLinks } from '@/data/portfolio';
import type { SiteContent } from '@/types/portfolio';

// Content saved from the dashboard (empty until something is saved).
const props = defineProps<{ content?: Partial<SiteContent> | unknown[]; contactMode?: string }>();

// Runs before the sections are created, so they render the saved content.
applyContent(props.content);
site.serverContact = props.contactMode === 'server';
watch(() => props.content, (content) => applyContent(content));

let scriptElement: HTMLScriptElement | null = null;

onMounted(() => {
    scriptElement = document.createElement('script');
    scriptElement.type = 'application/ld+json';
    scriptElement.textContent = JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'Person',
        name: personalInfo.name,
        jobTitle: personalInfo.title,
        description: personalInfo.summary,
        email: personalInfo.email,
        url: window.location.origin,
        sameAs: visibleSocialLinks.map((link) => link.url),
    });
    document.head.appendChild(scriptElement);
});

onUnmounted(() => {
    if (scriptElement && scriptElement.parentNode) {
        scriptElement.parentNode.removeChild(scriptElement);
    }
});
</script>

<template>
    <Head :title="pageMetadata.title" />

    <PortfolioSections />
</template>
