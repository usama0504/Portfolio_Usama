<script setup lang="ts">
import AboutSection from '@/components/portfolio/AboutSection.vue';
import ContactSection from '@/components/portfolio/ContactSection.vue';
import HeroSection from '@/components/portfolio/HeroSection.vue';
import JourneySection from '@/components/portfolio/JourneySection.vue';
import ProjectsSection from '@/components/portfolio/ProjectsSection.vue';
import SkillsSection from '@/components/portfolio/SkillsSection.vue';
import TechMarquee from '@/components/portfolio/TechMarquee.vue';
</script>

<!-- Shared by the Laravel/Inertia page and the static GitHub Pages build. -->
<template>
    <HeroSection />
    <TechMarquee />
    <AboutSection />
    <SkillsSection />
    <ProjectsSection />
    <JourneySection />
    <ContactSection />
</template>
