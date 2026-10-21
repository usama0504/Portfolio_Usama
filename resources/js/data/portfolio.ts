import {
    Code2,
    Database,
    GitBranch,
    Globe,
    Layers,
    LayoutTemplate,
    Server,
    Sparkles,
    Wrench,
} from '@lucide/vue';
import type { LucideIcon } from '@lucide/vue';
import { reactive, ref } from 'vue';
import { asset } from '@/lib/asset';
import type {
    EducationEntry,
    ExperienceEntry,
    NavigationLink,
    PersonalInfo,
    Project,
    Service,
    SiteContent,
    SkillCategory,
    Stat,
    Certificate,
    Testimonial,
} from '@/types/portfolio';

/* ───────────────────────────────────────────────────────────────────────────
 * Default content. This is what the website shows until you save something
 * from the dashboard (/admin). The static GitHub Pages build always uses it.
 * ─────────────────────────────────────────────────────────────────────────── */
export const defaultContent: SiteContent = {
    profile: {
        name: 'Muhammad Usama',
        title: 'Full Stack Developer',
        summary:
            'Full Stack Developer building scalable, high-performance web applications with Laravel, Vue.js, TypeScript and MySQL — from clean REST APIs and solid database design to fast, responsive interfaces.',
        location: 'HaroonAbad, Pakistan',
        timezone: 'Asia/Karachi',
        email: 'usama.webdev54@gmail.com',
        availability: 'Open to opportunities',
        photo: '/me.png',
        cv: '',
        whatsapp: '',
        whatsappMessage: 'Assalam o Alaikum! I saw your portfolio and would like to talk.',
        github: 'https://github.com/usama0504',
        linkedin: '',
    },
    seo: {
        title: '',
        description: '',
        image: '/og-default.png',
    },
    stats: [
        { value: '1+', label: 'Year of professional experience' },
        { value: '2', label: 'Featured full-stack projects' },
        { value: '10+', label: 'Technologies in daily use' },
    ],
    techStack: [
        'Laravel',
        'Vue.js',
        'TypeScript',
        'PHP',
        'Nuxt.js',
        'MySQL',
        'Tailwind CSS',
        'Inertia.js',
        'REST APIs',
        'JavaScript',
        'Git',
        'PHPUnit',
    ],
    skills: [
        {
            category: 'Backend',
            icon: 'server',
            blurb: 'APIs, business logic and auth that hold up in production.',
            skills: [
                { name: 'PHP', isCore: true },
                { name: 'Laravel', isCore: true },
                { name: 'RESTful APIs', isCore: true },
                { name: 'MVC Architecture' },
                { name: 'Authentication & RBAC' },
            ],
        },
        {
            category: 'Frontend',
            icon: 'layout-template',
            blurb: 'Responsive, accessible interfaces that feel fast.',
            skills: [
                { name: 'Vue.js', isCore: true },
                { name: 'TypeScript', isCore: true },
                { name: 'Nuxt.js' },
                { name: 'JavaScript' },
                { name: 'Tailwind CSS' },
                { name: 'HTML & CSS' },
            ],
        },
        {
            category: 'Database',
            icon: 'database',
            blurb: 'Schemas and queries designed to scale.',
            skills: [
                { name: 'MySQL', isCore: true },
                { name: 'Query Optimisation' },
                { name: 'Schema Design' },
                { name: 'Eloquent ORM' },
            ],
        },
        {
            category: 'Tools & Workflow',
            icon: 'wrench',
            blurb: 'The everyday toolkit for shipping reliably.',
            skills: [
                { name: 'Git & GitHub', isCore: true },
                { name: 'PHPUnit' },
                { name: 'Vite' },
                { name: 'Composer' },
                { name: 'NPM' },
            ],
        },
        {
            category: 'Currently Learning',
            icon: 'sparkles',
            blurb: 'Always levelling up.',
            skills: [{ name: 'Inertia.js' }, { name: 'Docker' }],
        },
    ],
    experiences: [
        {
            company: 'NbtHub',
            role: 'Full Stack Developer',
            startDate: '2026',
            endDate: null,
            location: 'HaroonAbad, Pakistan',
            description: [
                'Building scalable, high-performance web applications using PHP (Laravel) and Vue.js.',
                'Designing and developing RESTful APIs and integrating third-party services.',
                'Developing responsive user interfaces with Vue.js, TypeScript, and Tailwind CSS.',
                'Optimizing database queries and designing efficient MySQL database schemas.',
            ],
            technologies: ['Laravel', 'Vue.js', 'TypeScript', 'MySQL', 'Tailwind CSS'],
        },
    ],
    projects: [
        {
            slug: 'hospital-management-system',
            name: 'Hospital Management System',
            category: 'Healthcare',
            image: '/images/projects/hospital-management.png',
            gallery: [
                '/images/projects/gallery/hospital-1-home.webp',
                '/images/projects/gallery/hospital-2-departments-doctors.webp',
                '/images/projects/gallery/hospital-3-news-footer.webp',
                '/images/projects/gallery/hospital-4-doctors.webp',
                '/images/projects/gallery/hospital-5-contact.webp',
                '/images/projects/gallery/hospital-6-booking.webp',
                '/images/projects/gallery/hospital-7-admin-dashboard.webp',
            ],
            overview:
                'A full hospital management web app built with Laravel, Inertia.js and Vue.js. Admins, doctors and receptionists each get their own tools for patients, appointments, prescriptions and billing, plus a public website where patients can browse doctors and book appointments online.',
            problem:
                'Hospitals that run on paper and spreadsheets lose time on double bookings, scattered patient records and manual billing.',
            role: 'Full Stack Developer',
            technologies: [
                'Laravel',
                'Inertia.js',
                'Vue.js',
                'Tailwind CSS',
                'Spatie Permission',
                'FullCalendar',
                'Chart.js',
            ],
            features: [
                'Role-based access for admin, doctor and receptionist, with admin approval for new users',
                'Patient, doctor and department management',
                'Appointment booking with doctor availability, free time slots and a calendar view',
                'Prescriptions with multiple items, and billing with printable receipts',
                'Dashboard with charts and reports',
                'Public website with departments, doctor profiles, reviews, online booking and a contact form',
            ],
            outcome:
                'A working multi-role system that covers the daily hospital workflow, from booking an appointment to the final bill.',
            links: [{ type: 'repository', url: 'https://github.com/usama0504/hospital_management_copy' }],
        },
        {
            slug: 'ecommerce-shop',
            name: 'Ecommerce Shop',
            category: 'E-commerce',
            status: 'demo',
            image: '/images/projects/ecommerce-shop.png',
            gallery: [
                '/images/projects/gallery/shop-1-home.webp',
                '/images/projects/gallery/shop-2-order-tracking.webp',
                '/images/projects/gallery/shop-3-wishlist.webp',
                '/images/projects/gallery/shop-4-sign-up.webp',
                '/images/projects/gallery/shop-5-footer.webp',
            ],
            overview:
                'A single-page online shop built with Vue 3, Vue Router and Tailwind CSS. Products load from a public store API, and shoppers can search, filter, sort, save favourites and manage a cart.',
            problem:
                'A storefront needs to feel instant: browsing, comparing and adding to the cart without page reloads.',
            role: 'Frontend Developer',
            technologies: ['Vue.js', 'Vue Router', 'Tailwind CSS', 'Vite'],
            features: [
                'Product listing with search, category filter and sorting',
                'Product detail pages with customer reviews',
                'Cart (with a slide-in drawer), wishlist and order history',
                'Cart and wishlist saved in the browser, so they survive a refresh',
                'Dark mode and a sign-in page',
            ],
            outcome:
                'A demo storefront showing the complete shopping flow on the client side. Products come from a sample API and sign-in is simulated.',
            links: [{ type: 'repository', url: 'https://github.com/usama0504/Ecommerce-Shop' }],
        },
    ],
    // Nothing invented here: add real ones from the dashboard (the sections stay hidden while empty).
    testimonials: [],
    certificates: [],
    services: [
        {
            title: 'Full-stack web apps',
            description: 'End-to-end products — database, API and interface — built with Laravel and Vue.',
            icon: 'layers',
        },
        {
            title: 'API design & integration',
            description: 'Clean RESTful APIs and reliable third-party integrations.',
            icon: 'server',
        },
        {
            title: 'Frontend engineering',
            description: 'Responsive, accessible interfaces with Vue.js, TypeScript and Tailwind.',
            icon: 'code',
        },
        {
            title: 'Database architecture',
            description: 'MySQL schemas and queries designed for speed and growth.',
            icon: 'database',
        },
    ],
    education: [
        {
            institution: 'The Islamia University of Bahawalpur',
            qualification: 'BS Software Engineering',
            startDate: '2021',
            endDate: '2025',
            description: 'Data structures, algorithms, software engineering, databases and web development.',
        },
        {
            institution: 'Govt Rizvia Post Graduate College Haroonabad',
            qualification: 'FSc Pre-Engineering',
            startDate: '2019',
            endDate: '2021',
            description: 'Pre-engineering studies with a focus on mathematics and physics.',
        },
    ],
};

/** Icon names offered in the dashboard (and what the backend accepts). */
export const iconMap: Record<string, LucideIcon> = {
    server: Server,
    'layout-template': LayoutTemplate,
    database: Database,
    wrench: Wrench,
    layers: Layers,
    code: Code2,
    globe: Globe,
    'git-branch': GitBranch,
    sparkles: Sparkles,
};
export const iconOptions = Object.keys(iconMap);

export const navigationLinks: NavigationLink[] = [
    { label: 'About', href: '#about', id: 'about' },
    { label: 'Skills', href: '#skills', id: 'skills' },
    { label: 'Work', href: '#projects', id: 'projects' },
    { label: 'Journey', href: '#journey', id: 'journey' },
    { label: 'Contact', href: '#contact', id: 'contact' },
];

/* ───────────────────────────────────────────────────────────────────────────
 * Live (reactive) content used by every component. `applyContent()` fills it
 * from the defaults above and from whatever the dashboard saved.
 * ─────────────────────────────────────────────────────────────────────────── */
export const personalInfo = reactive<PersonalInfo>({
    name: '',
    title: '',
    summary: '',
    email: '',
    socialLinks: [],
});
export const visibleSocialLinks = reactive<PersonalInfo['socialLinks']>([]);
export const stats = reactive<Stat[]>([]);
export const techStack = reactive<string[]>([]);
export const skillCategories = reactive<SkillCategory[]>([]);
export const experiences = reactive<ExperienceEntry[]>([]);
export const projects = reactive<Project[]>([]);
export const services = reactive<Service[]>([]);
export const testimonials = reactive<Testimonial[]>([]);
export const certificates = reactive<Certificate[]>([]);
export const education = reactive<EducationEntry[]>([]);
export const pageMetadata = reactive({ title: '', description: '' });
/** serverContact: contact form posts to this Laravel app (inbox + Gmail). Otherwise it uses FormSubmit. */
export const site = reactive({ serverContact: false });
/** Two-letter monogram shown when there is no photo. */
export const initials = ref('');

function replace<T>(target: T[], items: T[]): void {
    target.splice(0, target.length, ...items);
}

/** "/uploads/x.png" → the right URL for this build; full URLs are left alone. */
function img(path?: string | null): string | undefined {
    if (!path) {
        return undefined;
    }

    return path.startsWith('/') && !path.startsWith('//') ? asset(path) : path;
}

/** wa.me needs the number as digits only, with country code. */
function whatsappLink(number?: string | null, message?: string | null): string | undefined {
    const digits = (number ?? '').replace(/\D+/g, '');

    if (digits.length < 8) {
        return undefined;
    }

    const text = (message ?? '').trim();

    return `https://wa.me/${digits}${text ? `?text=${encodeURIComponent(text)}` : ''}`;
}

const blank = (value?: string | null): string | undefined => (value && value.trim() !== '' ? value : undefined);

export function applyContent(raw?: Partial<SiteContent> | unknown[] | null): void {
    const saved = (raw && !Array.isArray(raw) ? raw : {}) as Partial<SiteContent>;
    const pick = <K extends keyof SiteContent>(key: K): SiteContent[K] => (saved[key] ?? defaultContent[key]) as SiteContent[K];

    const profile = { ...defaultContent.profile, ...(saved.profile ?? {}) };

    const links = [
        { platform: 'github' as const, label: 'GitHub', icon: GitBranch, url: profile.github ?? '' },
        { platform: 'linkedin' as const, label: 'LinkedIn', icon: Globe, url: profile.linkedin ?? '' },
    ];

    Object.assign(personalInfo, {
        name: profile.name,
        title: profile.title,
        summary: profile.summary,
        location: blank(profile.location),
        timezone: blank(profile.timezone),
        email: profile.email,
        availability: blank(profile.availability),
        photo: img(profile.photo),
        cv: img(profile.cv),
        whatsappUrl: whatsappLink(profile.whatsapp, profile.whatsappMessage),
        socialLinks: links,
    });
    replace(visibleSocialLinks, links.filter((link) => link.url.trim() !== ''));

    replace(stats, pick('stats').map((s) => ({ value: String(s.value ?? ''), label: String(s.label ?? '') })));
    replace(techStack, pick('techStack').map(String));

    replace(
        skillCategories,
        pick('skills').map((c) => ({
            category: c.category,
            icon: iconMap[c.icon] ?? Code2,
            blurb: blank(c.blurb),
            skills: (c.skills ?? []).map((s) => ({ name: s.name, isCore: !!s.isCore })),
        })),
    );

    replace(
        experiences,
        pick('experiences').map((e) => ({
            company: e.company,
            role: e.role,
            startDate: e.startDate,
            endDate: blank(e.endDate) ?? null,
            location: blank(e.location),
            description: e.description ?? [],
            technologies: e.technologies ?? [],
        })),
    );

    replace(
        projects,
        pick('projects').map((p) => ({
            slug: p.slug,
            name: p.name,
            category: p.category,
            status: blank(p.status) as Project['status'],
            image: img(p.image),
            gallery: (p.gallery ?? []).map((path) => img(path)).filter((path): path is string => !!path),
            overview: p.overview,
            problem: blank(p.problem),
            role: blank(p.role),
            technologies: p.technologies ?? [],
            features: p.features ?? [],
            outcome: blank(p.outcome),
            links: (p.links ?? []).filter((l) => l.url),
        })),
    );

    replace(
        services,
        pick('services').map((s) => ({
            title: s.title,
            description: s.description,
            icon: iconMap[s.icon] ?? Code2,
        })),
    );

    replace(
        testimonials,
        pick('testimonials').map((t) => ({
            name: t.name,
            role: t.role ?? '',
            quote: t.quote,
            photo: img(t.photo),
        })),
    );

    replace(
        certificates,
        pick('certificates').map((c) => ({
            title: c.title,
            issuer: c.issuer ?? '',
            year: c.year ?? '',
            image: img(c.image),
            link: blank(c.link),
        })),
    );

    replace(
        education,
        pick('education').map((e) => ({
            institution: e.institution,
            qualification: e.qualification,
            startDate: e.startDate,
            endDate: e.endDate,
            description: blank(e.description),
        })),
    );

    initials.value = profile.name
        .split(' ')
        .filter(Boolean)
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();

    const seo = { ...defaultContent.seo, ...(saved.seo ?? {}) };
    pageMetadata.title = blank(seo.title) ?? `${profile.name} — ${profile.title}`;
    pageMetadata.description = blank(seo.description) ?? profile.summary;
}

// Defaults first, so the static build (and the first paint) always have content.
applyContent();
