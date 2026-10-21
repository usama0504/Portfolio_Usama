import type { LucideIcon } from '@lucide/vue';

export type SocialPlatform =
    'github' | 'linkedin' | 'twitter' | 'email' | 'other';

export type SocialLink = {
    platform: SocialPlatform;
    url: string;
    label: string;
    icon: LucideIcon;
};

export type NavigationLink = {
    label: string;
    href: string;
    id: string;
};

export type SkillItem = {
    name: string;
    isCore?: boolean;
};

export type SkillCategory = {
    category: string;
    icon: LucideIcon;
    blurb?: string;
    skills: SkillItem[];
};

export type ExperienceEntry = {
    company: string;
    role: string;
    startDate: string;
    endDate: string | null;
    location?: string;
    description: string[];
    technologies: string[];
};

export type ProjectLink = {
    type: 'live' | 'repository';
    url: string;
};

export type ProjectStatus = 'live' | 'demo' | 'in-progress';

export type Project = {
    slug: string;
    name: string;
    category: string;
    status?: ProjectStatus;
    overview: string;
    problem?: string;
    role?: string;
    technologies: string[];
    features?: string[];
    outcome?: string;
    links: ProjectLink[];
    image?: string;
    /** extra screenshots shown in the project popup (after the cover image) */
    gallery?: string[];
};

export type EducationEntry = {
    institution: string;
    qualification: string;
    startDate: string;
    endDate: string;
    description?: string;
};

export type Service = {
    title: string;
    description: string;
    icon: LucideIcon;
};

export type Stat = {
    value: string;
    label: string;
};

export type PersonalInfo = {
    name: string;
    title: string;
    summary: string;
    location?: string;
    timezone?: string;
    email: string;
    availability?: string;
    photo?: string;
    cv?: string;
    /** https://wa.me/… link (empty when no number is set) */
    whatsappUrl?: string;
    socialLinks: SocialLink[];
};

export type ContactFormData = {
    name: string;
    email: string;
    subject: string;
    message: string;
};

/* ── Content as saved by the dashboard (plain JSON, icons referenced by name) ── */

export type ProfileContent = {
    name: string;
    title: string;
    summary: string;
    location: string;
    timezone: string;
    email: string;
    availability: string;
    photo: string;
    cv: string;
    /** digits only, with country code, e.g. 923001234567 */
    whatsapp: string;
    whatsappMessage: string;
    github: string;
    linkedin: string;
};

export type SkillCategoryContent = {
    category: string;
    icon: string;
    blurb: string;
    skills: SkillItem[];
};

export type ServiceContent = {
    title: string;
    description: string;
    icon: string;
};

export type ExperienceContent = Omit<ExperienceEntry, 'endDate'> & { endDate: string | null };

export type Testimonial = {
    name: string;
    role: string;
    quote: string;
    photo?: string;
};

export type Certificate = {
    title: string;
    issuer: string;
    year: string;
    image?: string;
    link?: string;
};

export type SeoContent = {
    title: string;
    description: string;
    image: string;
};

export type SiteContent = {
    profile: ProfileContent;
    seo: SeoContent;
    stats: Stat[];
    techStack: string[];
    skills: SkillCategoryContent[];
    experiences: ExperienceContent[];
    projects: Project[];
    testimonials: Testimonial[];
    certificates: Certificate[];
    services: ServiceContent[];
    education: EducationEntry[];
};
