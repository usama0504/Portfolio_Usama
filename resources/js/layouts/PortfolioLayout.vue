<script setup lang="ts">
import { useScroll, useSpring, motion } from 'motion-v';
import PortfolioFooter from '@/components/layout/PortfolioFooter.vue';
import PortfolioHeader from '@/components/layout/PortfolioHeader.vue';

const { scrollYProgress } = useScroll();
const progress = useSpring(scrollYProgress, { stiffness: 120, damping: 30, restDelta: 0.001 });
</script>

<template>
    <div class="flex min-h-screen flex-col bg-paper text-ink">
        <!-- Reading progress -->
        <motion.div
            class="fixed inset-x-0 top-0 z-70 h-0.5 origin-left bg-brand"
            :style="{ scaleX: progress }"
            aria-hidden="true"
        />
        <div class="folio-grain" aria-hidden="true" />

        <PortfolioHeader />
        <main id="main-content" class="flex-1">
            <slot />
        </main>
        <PortfolioFooter />
    </div>
</template>
