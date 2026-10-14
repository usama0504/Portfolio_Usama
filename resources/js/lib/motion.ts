/**
 * Shared motion presets so every section animates with the same rhythm.
 * Pass `undefined` instead of these when the user prefers reduced motion.
 */
export const easeOutExpo = [0.22, 1, 0.36, 1] as const;

export const stagger = (staggerChildren = 0.1, delayChildren = 0) => ({
    hidden: {},
    visible: { transition: { staggerChildren, delayChildren } },
});

export const fadeUp = {
    hidden: { opacity: 0, y: 32 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.7, ease: easeOutExpo } },
};

export const fadeIn = {
    hidden: { opacity: 0 },
    visible: { opacity: 1, transition: { duration: 0.8, ease: easeOutExpo } },
};

export const slideFrom = (x: number) => ({
    hidden: { opacity: 0, x },
    visible: { opacity: 1, x: 0, transition: { duration: 0.8, ease: easeOutExpo } },
});

export const scaleIn = {
    hidden: { opacity: 0, scale: 0.94, y: 24 },
    visible: { opacity: 1, scale: 1, y: 0, transition: { duration: 0.9, ease: easeOutExpo } },
};

/** Default viewport config for scroll-triggered reveals. */
export const inView = { once: true, margin: '-80px' } as const;
