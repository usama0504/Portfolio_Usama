import type { Ref } from 'vue';
import { onMounted, onUnmounted, ref } from 'vue';

export function useActiveSection(
    sectionIds: string[],
    options?: { rootMargin?: string; threshold?: number },
): Ref<string> {
    // Empty until a tracked section is actually on screen (e.g. nothing while on the hero).
    const activeSection = ref('');
    let observer: IntersectionObserver | null = null;

    onMounted(() => {
        if (typeof IntersectionObserver === 'undefined') {
            return;
        }

        const visibleSections = new Set<string>();

        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    const id = entry.target.id;

                    if (entry.isIntersecting) {
                        visibleSections.add(id);
                    } else {
                        visibleSections.delete(id);
                    }
                });

                activeSection.value =
                    sectionIds.find((id) => visibleSections.has(id)) ?? '';
            },
            {
                rootMargin: options?.rootMargin ?? '-20% 0px -60% 0px',
                threshold: options?.threshold ?? 0,
            },
        );

        sectionIds.forEach((id) => {
            const element = document.getElementById(id);

            if (element) {
                observer!.observe(element);
            }
        });
    });

    onUnmounted(() => {
        observer?.disconnect();
    });

    return activeSection;
}
