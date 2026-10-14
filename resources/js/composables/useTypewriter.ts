import { onMounted, onUnmounted, ref } from 'vue';

type TypewriterOptions = {
    typeSpeed?: number;
    deleteSpeed?: number;
    holdTime?: number;
    pauseTime?: number;
};

/**
 * Types each phrase left-to-right, holds, erases it, then moves on — forever.
 * Returns the currently visible text. Callers should render the full phrase
 * instead when the user prefers reduced motion.
 */
export function useTypewriter(phrases: string[], options: TypewriterOptions = {}) {
    const { typeSpeed = 75, deleteSpeed = 40, holdTime = 2200, pauseTime = 500 } = options;

    const text = ref('');
    let index = 0;
    let deleting = false;
    let timer: ReturnType<typeof setTimeout> | undefined;

    function tick() {
        const phrase = phrases[index % phrases.length] ?? '';

        if (!deleting) {
            text.value = phrase.slice(0, text.value.length + 1);

            if (text.value === phrase) {
                deleting = true;
                timer = setTimeout(tick, holdTime);

                return;
            }

            timer = setTimeout(tick, typeSpeed);

            return;
        }

        text.value = phrase.slice(0, text.value.length - 1);

        if (text.value === '') {
            deleting = false;
            index++;
            timer = setTimeout(tick, pauseTime);

            return;
        }

        timer = setTimeout(tick, deleteSpeed);
    }

    onMounted(() => {
        timer = setTimeout(tick, 600);
    });
    onUnmounted(() => clearTimeout(timer));

    return text;
}
