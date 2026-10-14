/**
 * Pointer handler for `.folio-spotlight` cards: feeds the cursor position
 * into --mx / --my so the CSS radial glow follows the mouse.
 */
export function trackSpotlight(event: PointerEvent): void {
    const el = event.currentTarget as HTMLElement;
    const rect = el.getBoundingClientRect();
    el.style.setProperty('--mx', `${event.clientX - rect.left}px`);
    el.style.setProperty('--my', `${event.clientY - rect.top}px`);
}
