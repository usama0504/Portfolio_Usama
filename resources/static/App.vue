<script setup lang="ts">
import PortfolioSections from '@/components/portfolio/PortfolioSections.vue';
import PortfolioLayout from '@/layouts/PortfolioLayout.vue';
</script>

<!-- Static (GitHub Pages) shell: same layout and sections as the Laravel page, no Inertia. -->
<template>
    <PortfolioLayout>
        <PortfolioSections />
    </PortfolioLayout>
</template>
