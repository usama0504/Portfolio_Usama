<script setup lang="ts">
import { ref } from 'vue';
import { motion } from 'motion-v';
import { ArrowUpRight, Check, CheckCircle2, Copy } from '@lucide/vue';
import BrandIcon from '@/components/portfolio/BrandIcon.vue';
import { useContactForm } from '@/composables/useContactForm';
import { useReducedMotion } from '@/composables/useReducedMotion';
import { personalInfo, visibleSocialLinks } from '@/data/portfolio';
import { fadeUp, inView, stagger } from '@/lib/motion';

const { prefersReducedMotion } = useReducedMotion();
const v = <T,>(variants: T) => (prefersReducedMotion.value ? ({ hidden: {}, visible: {} } as T) : variants);

const { form, submit: send } = useContactForm();
const submitted = ref(false);

async function submit() {
    submitted.value = await send();
}

const copied = ref(false);

async function copyEmail() {
    try {
        await navigator.clipboard.writeText(personalInfo.email);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch {
        window.location.href = `mailto:${personalInfo.email}`;
    }
}
</script>

<template>
    <section id="contact" class="folio-section border-t border-line bg-paper-2">
        <div class="folio-container">
            <motion.div
                :variants="v(stagger(0.1))"
                initial="hidden"
                while-in-view="visible"
                :viewport="inView"
            >
                <motion.p :variants="v(fadeUp)" class="folio-eyebrow">Contact</motion.p>
                <motion.h2 :variants="v(fadeUp)" class="folio-display mt-5 max-w-5xl text-[clamp(2.4rem,5.6vw,4.75rem)] text-ink">
                    Let's build something <span class="folio-em">great</span> together.
                </motion.h2>
            </motion.div>

            <div class="mt-16 grid gap-12 md:mt-20 lg:grid-cols-12 lg:gap-16">
                <!-- Direct contact -->
                <motion.div
                    :variants="v(stagger(0.08))"
                    initial="hidden"
                    while-in-view="visible"
                    :viewport="inView"
                    class="flex flex-col gap-10 lg:col-span-5"
                >
                    <motion.p :variants="v(fadeUp)" class="max-w-md text-base leading-relaxed text-ink-soft md:text-lg">
                        Have a project in mind, a role to fill, or just want to say hi? Send a message — I usually reply
                        within 24 hours.
                    </motion.p>

                    <motion.div :variants="v(fadeUp)">
                        <p class="font-mono text-xs tracking-widest text-ink-muted uppercase">Email</p>
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <a :href="`mailto:${personalInfo.email}`" class="folio-link font-display text-xl font-semibold tracking-tight break-all text-ink md:text-2xl">
                                {{ personalInfo.email }}
                            </a>
                            <button
                                class="flex h-9 w-9 items-center justify-center rounded-full border border-line text-ink-soft transition-colors hover:border-ink hover:text-ink"
                                :aria-label="copied ? 'Email copied' : 'Copy email address'"
                                @click="copyEmail"
                            >
                                <Check v-if="copied" class="h-4 w-4 text-emerald-500" />
                                <Copy v-else class="h-4 w-4" />
                            </button>
                        </div>
                    </motion.div>

                    <motion.div :variants="v(fadeUp)">
                        <p class="font-mono text-xs tracking-widest text-ink-muted uppercase">Socials</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a
                                v-for="s in visibleSocialLinks"
                                :key="s.platform"
                                :href="s.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="folio-btn folio-btn-outline py-3!"
                            >
                                <BrandIcon :platform="s.platform" class="h-4 w-4" /> {{ s.label }}
                            </a>
                        </div>
                    </motion.div>
                </motion.div>

                <!-- Form -->
                <motion.div
                    :variants="v(fadeUp)"
                    initial="hidden"
                    while-in-view="visible"
                    :viewport="inView"
                    class="folio-card p-6 md:p-10 lg:col-span-7"
                >
                    <div v-if="submitted" class="flex min-h-104 flex-col items-center justify-center gap-4 text-center">
                        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-brand text-brand-ink">
                            <CheckCircle2 class="h-8 w-8" />
                        </span>
                        <h3 class="folio-display text-3xl text-ink">Message sent!</h3>
                        <p class="text-ink-soft">Thanks for reaching out — I'll get back to you soon.</p>
                        <button class="folio-link mt-2 text-sm text-ink-muted" @click="submitted = false">Send another message</button>
                    </div>

                    <form v-else class="space-y-8" novalidate @submit.prevent="submit">
                        <div class="grid gap-8 sm:grid-cols-2">
                            <label class="block">
                                <span class="font-mono text-xs tracking-widest text-ink-muted uppercase">Your name</span>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    autocomplete="name"
                                    placeholder="Jane Doe"
                                    required
                                    class="folio-input"
                                    :aria-invalid="!!form.errors.name"
                                />
                                <span v-if="form.errors.name" class="mt-1 block text-xs text-red-500">{{ form.errors.name }}</span>
                            </label>
                            <label class="block">
                                <span class="font-mono text-xs tracking-widest text-ink-muted uppercase">Email</span>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="jane@company.com"
                                    required
                                    class="folio-input"
                                    :aria-invalid="!!form.errors.email"
                                />
                                <span v-if="form.errors.email" class="mt-1 block text-xs text-red-500">{{ form.errors.email }}</span>
                            </label>
                        </div>
                        <label class="block">
                            <span class="font-mono text-xs tracking-widest text-ink-muted uppercase">Subject</span>
                            <input
                                v-model="form.subject"
                                type="text"
                                placeholder="Project inquiry, job opportunity…"
                                required
                                class="folio-input"
                                :aria-invalid="!!form.errors.subject"
                            />
                            <span v-if="form.errors.subject" class="mt-1 block text-xs text-red-500">{{ form.errors.subject }}</span>
                        </label>
                        <label class="block">
                            <span class="font-mono text-xs tracking-widest text-ink-muted uppercase">
                                Message <span class="tracking-normal normal-case opacity-70">(optional)</span>
                            </span>
                            <textarea
                                v-model="form.message"
                                rows="4"
                                maxlength="5000"
                                placeholder="Tell me a little about what you're building…"
                                class="folio-input resize-none"
                                :aria-invalid="!!form.errors.message"
                            />
                            <span v-if="form.errors.message" class="mt-1 block text-xs text-red-500">{{ form.errors.message }}</span>
                        </label>

                        <!-- Honeypot: hidden from people, catches bots -->
                        <input v-model="form.botcheck" type="text" name="_honey" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true" />

                        <p v-if="form.errors.form" class="rounded-xl border border-red-500/30 bg-red-500/5 px-4 py-3 text-sm text-red-500" role="alert">
                            {{ form.errors.form }}
                        </p>

                        <button type="submit" :disabled="form.processing" class="folio-btn folio-btn-brand w-full py-4! text-base! disabled:opacity-60 sm:w-auto">
                            {{ form.processing ? 'Sending…' : 'Send message' }}
                            <ArrowUpRight v-if="!form.processing" class="folio-btn-icon h-4 w-4" />
                        </button>
                    </form>
                </motion.div>
            </div>
        </div>
    </section>
</template>
