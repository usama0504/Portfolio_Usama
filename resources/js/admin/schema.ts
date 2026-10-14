import { iconOptions } from '@/data/portfolio';

export type FieldType =
    | 'text'
    | 'textarea'
    | 'image'
    | 'bool'
    | 'select'
    | 'strings'
    | 'list'
    | 'object';

export type Field = {
    key: string;
    label: string;
    type: FieldType;
    help?: string;
    placeholder?: string;
    /** select */
    options?: { value: string; label: string }[];
    /** object / list: the fields of one item */
    fields?: Field[];
    /** list: which field names an item card */
    itemTitle?: string;
    addLabel?: string;
    /** strings: use a multi-line box for every entry */
    multiline?: boolean;
};

export type Section = {
    id: string;
    label: string;
    description: string;
    field: Field;
};

const icons = iconOptions.map((value) => ({ value, label: value }));

export const sections: Section[] = [
    {
        id: 'profile',
        label: 'Profile',
        description: 'Your name, intro, contact email, photo and social links.',
        field: {
            key: 'profile',
            label: 'Profile',
            type: 'object',
            fields: [
                { key: 'name', label: 'Full name', type: 'text' },
                { key: 'title', label: 'Job title', type: 'text' },
                { key: 'summary', label: 'Short intro', type: 'textarea', help: 'Shown in the hero and in search results.' },
                { key: 'location', label: 'Location', type: 'text' },
                { key: 'timezone', label: 'Time zone', type: 'text', help: 'e.g. Asia/Karachi (used for the local clock).' },
                { key: 'email', label: 'Email', type: 'text', help: 'Messages from the contact form are sent here.' },
                { key: 'availability', label: 'Availability text', type: 'text', placeholder: 'Open to opportunities' },
                { key: 'photo', label: 'Photo', type: 'image', help: 'Portrait photo (4:5 works best). Leave empty to show your initials.' },
                { key: 'github', label: 'GitHub link', type: 'text', placeholder: 'https://github.com/…' },
                { key: 'linkedin', label: 'LinkedIn link', type: 'text', placeholder: 'https://linkedin.com/in/…', help: 'Leave empty to hide the LinkedIn icon.' },
            ],
        },
    },
    {
        id: 'stats',
        label: 'Stats',
        description: 'The big numbers in the hero (keep them honest).',
        field: {
            key: 'stats',
            label: 'Stats',
            type: 'list',
            itemTitle: 'label',
            addLabel: 'Add stat',
            fields: [
                { key: 'value', label: 'Number', type: 'text', placeholder: '10+' },
                { key: 'label', label: 'Label', type: 'text' },
            ],
        },
    },
    {
        id: 'techStack',
        label: 'Tech slider',
        description: 'The scrolling technology names under the hero.',
        field: { key: 'techStack', label: 'Technologies', type: 'strings', addLabel: 'Add technology' },
    },
    {
        id: 'skills',
        label: 'Skills',
        description: 'Skill groups. Tick "Core" for your strongest skills.',
        field: {
            key: 'skills',
            label: 'Skill groups',
            type: 'list',
            itemTitle: 'category',
            addLabel: 'Add skill group',
            fields: [
                { key: 'category', label: 'Group name', type: 'text' },
                { key: 'icon', label: 'Icon', type: 'select', options: icons },
                { key: 'blurb', label: 'One-line description', type: 'text' },
                {
                    key: 'skills',
                    label: 'Skills',
                    type: 'list',
                    itemTitle: 'name',
                    addLabel: 'Add skill',
                    fields: [
                        { key: 'name', label: 'Skill', type: 'text' },
                        { key: 'isCore', label: 'Core skill', type: 'bool' },
                    ],
                },
            ],
        },
    },
    {
        id: 'experiences',
        label: 'Experience',
        description: 'Jobs and internships, newest first.',
        field: {
            key: 'experiences',
            label: 'Experience',
            type: 'list',
            itemTitle: 'company',
            addLabel: 'Add experience',
            fields: [
                { key: 'company', label: 'Company', type: 'text' },
                { key: 'role', label: 'Role', type: 'text' },
                { key: 'startDate', label: 'Start', type: 'text', placeholder: '2026' },
                { key: 'endDate', label: 'End', type: 'text', help: 'Leave empty if you still work here (shows "Present").' },
                { key: 'location', label: 'Location', type: 'text' },
                { key: 'description', label: 'What you did', type: 'strings', multiline: true, addLabel: 'Add point' },
                { key: 'technologies', label: 'Technologies', type: 'strings', addLabel: 'Add technology' },
            ],
        },
    },
    {
        id: 'projects',
        label: 'Projects',
        description: 'Your work. The image is the thumbnail on the project card.',
        field: {
            key: 'projects',
            label: 'Projects',
            type: 'list',
            itemTitle: 'name',
            addLabel: 'Add project',
            fields: [
                { key: 'name', label: 'Project name', type: 'text' },
                { key: 'slug', label: 'Slug', type: 'text', help: 'Lowercase letters, numbers and dashes only, e.g. my-project.' },
                { key: 'category', label: 'Category', type: 'text', placeholder: 'Healthcare' },
                {
                    key: 'status',
                    label: 'Status badge',
                    type: 'select',
                    options: [
                        { value: '', label: 'None' },
                        { value: 'live', label: 'Live' },
                        { value: 'demo', label: 'Demo project' },
                        { value: 'in-progress', label: 'In progress' },
                    ],
                },
                { key: 'image', label: 'Project image', type: 'image', help: '16:10 image works best (e.g. 1600×1000).' },
                { key: 'overview', label: 'Overview', type: 'textarea' },
                { key: 'problem', label: 'Problem it solves', type: 'textarea' },
                { key: 'role', label: 'Your role', type: 'text' },
                { key: 'technologies', label: 'Technologies', type: 'strings', addLabel: 'Add technology' },
                { key: 'features', label: 'Key features', type: 'strings', multiline: true, addLabel: 'Add feature' },
                { key: 'outcome', label: 'Outcome', type: 'textarea' },
                {
                    key: 'links',
                    label: 'Links',
                    type: 'list',
                    itemTitle: 'type',
                    addLabel: 'Add link',
                    fields: [
                        {
                            key: 'type',
                            label: 'Type',
                            type: 'select',
                            options: [
                                { value: 'repository', label: 'Source code (GitHub)' },
                                { value: 'live', label: 'Live website' },
                            ],
                        },
                        { key: 'url', label: 'URL', type: 'text', placeholder: 'https://…' },
                    ],
                },
            ],
        },
    },
    {
        id: 'services',
        label: 'Services',
        description: 'What you offer (About section).',
        field: {
            key: 'services',
            label: 'Services',
            type: 'list',
            itemTitle: 'title',
            addLabel: 'Add service',
            fields: [
                { key: 'title', label: 'Title', type: 'text' },
                { key: 'icon', label: 'Icon', type: 'select', options: icons },
                { key: 'description', label: 'Description', type: 'textarea' },
            ],
        },
    },
    {
        id: 'education',
        label: 'Education',
        description: 'Degrees and colleges, newest first.',
        field: {
            key: 'education',
            label: 'Education',
            type: 'list',
            itemTitle: 'qualification',
            addLabel: 'Add education',
            fields: [
                { key: 'qualification', label: 'Degree / qualification', type: 'text' },
                { key: 'institution', label: 'Institution', type: 'text' },
                { key: 'startDate', label: 'Start year', type: 'text' },
                { key: 'endDate', label: 'End year', type: 'text' },
                { key: 'description', label: 'Details', type: 'textarea' },
            ],
        },
    },
];

/** A fresh, empty item for a list. */
export function blankItem(fields: Field[]): Record<string, unknown> {
    const item: Record<string, unknown> = {};

    for (const field of fields) {
        if (field.type === 'bool') {
            item[field.key] = false;
        } else if (field.type === 'strings' || field.type === 'list') {
            item[field.key] = [];
        } else if (field.type === 'select') {
            item[field.key] = field.options?.[0]?.value ?? '';
        } else if (field.type === 'object') {
            item[field.key] = blankItem(field.fields ?? []);
        } else {
            item[field.key] = '';
        }
    }

    return item;
}
