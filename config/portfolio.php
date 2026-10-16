<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Contact Email
    |--------------------------------------------------------------------------
    |
    | The email address where portfolio contact form submissions are sent.
    | Set this value in your .env file as PORTFOLIO_CONTACT_EMAIL.
    |
    */

    'contact_email' => env('PORTFOLIO_CONTACT_EMAIL'),

    // Used for the page title / description until you save them in the dashboard.
    'default_title' => env('PORTFOLIO_TITLE', 'Muhammad Usama — Full Stack Developer'),
    'default_description' => env('PORTFOLIO_DESCRIPTION', 'Full Stack Developer building scalable, high-performance web applications with Laravel, Vue.js, TypeScript and MySQL.'),
];
