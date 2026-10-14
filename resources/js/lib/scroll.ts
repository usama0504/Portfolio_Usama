export function scrollToSection(target: string): void {
    const id = target.replace('#', '');

    if (id === 'top') {
        window.scrollTo({ top: 0, behavior: 'smooth' });

        return;
    }

    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' });
}
