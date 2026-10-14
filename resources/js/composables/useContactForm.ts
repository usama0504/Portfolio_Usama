import { reactive } from 'vue';
import { personalInfo, site } from '@/data/portfolio';
import { xsrfToken } from '@/lib/csrf';

type Field = 'name' | 'email' | 'subject' | 'message';

/**
 * Contact form with two modes:
 *  - Laravel (site.serverContact): posts to /contact → saved in the dashboard inbox and emailed to your Gmail.
 *  - Static hosting (GitHub Pages): emails via FormSubmit (https://formsubmit.co), no backend required.
 *    The very first submission triggers a one-time activation email to the inbox.
 */
const endpoint = () => `https://formsubmit.co/ajax/${personalInfo.email}`;
const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export function useContactForm() {
    const form = reactive({
        name: '',
        email: '',
        subject: '',
        message: '',
        /** Honeypot — real people never fill this hidden field. */
        botcheck: '',
        processing: false,
        errors: {} as Partial<Record<Field | 'form', string>>,
    });

    function validate(): boolean {
        const errors: typeof form.errors = {};

        if (!form.name.trim()) {
            errors.name = 'Please enter your name.';
        }

        if (!emailPattern.test(form.email.trim())) {
            errors.email = 'Please enter a valid email address.';
        }

        if (!form.subject.trim()) {
            errors.subject = 'Please enter a subject.';
        }

        if (form.message.length > 5000) {
            errors.message = 'Message must not exceed 5000 characters.';
        }

        form.errors = errors;

        return Object.keys(errors).length === 0;
    }

    function reset() {
        form.name = '';
        form.email = '';
        form.subject = '';
        form.message = '';
        form.errors = {};
    }

    /** Laravel mode: POST /contact (saved in the inbox + emailed to you). */
    async function sendToServer(): Promise<boolean> {
        const response = await fetch('/contact', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-XSRF-TOKEN': xsrfToken(),
            },
            body: JSON.stringify({
                name: form.name.trim(),
                email: form.email.trim(),
                subject: form.subject.trim(),
                message: form.message.trim(),
                botcheck: form.botcheck,
            }),
        });

        if (response.status === 422) {
            const json = (await response.json().catch(() => ({}))) as { errors?: Record<string, string[]> };
            const errors: typeof form.errors = {};

            for (const [field, messages] of Object.entries(json.errors ?? {})) {
                errors[field as Field] = messages[0];
            }

            form.errors = Object.keys(errors).length ? errors : { form: 'Please check the form and try again.' };

            return false;
        }

        if (response.status === 429) {
            form.errors = { form: 'You have sent a few messages already. Please try again a little later.' };

            return false;
        }

        if (!response.ok) {
            throw new Error('Request failed');
        }

        reset();

        return true;
    }

    /** Static mode: FormSubmit. */
    async function sendToFormSubmit(): Promise<boolean> {
        const response = await fetch(endpoint(), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({
                name: form.name.trim(),
                email: form.email.trim(),
                _subject: `Portfolio Contact: ${form.subject.trim()}`,
                _replyto: form.email.trim(),
                _template: 'table',
                _captcha: 'false',
                subject: form.subject.trim(),
                message: form.message.trim() || '(no message)',
            }),
        });
        const data = (await response.json().catch(() => ({}))) as { success?: string | boolean };

        if (!response.ok || String(data.success) !== 'true') {
            throw new Error('Request failed');
        }

        reset();

        return true;
    }

    /** Resolves true when the message was accepted. */
    async function submit(): Promise<boolean> {
        if (form.botcheck || !validate()) {
            return false;
        }

        form.processing = true;

        try {
            return site.serverContact ? await sendToServer() : await sendToFormSubmit();
        } catch {
            form.errors = { form: `Something went wrong. Please email me directly at ${personalInfo.email}.` };

            return false;
        } finally {
            form.processing = false;
        }
    }

    return { form, submit };
}
