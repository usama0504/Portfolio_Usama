<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailTest extends Command
{
    protected $signature = 'mail:test {to? : Address to send the test to (default: PORTFOLIO_CONTACT_EMAIL)}';

    protected $description = 'Send a test email to check your Gmail / SMTP settings';

    public function handle(): int
    {
        $to = $this->argument('to') ?: config('portfolio.contact_email');

        if (! $to) {
            $this->error('No address. Run: php artisan mail:test yourname@gmail.com');

            return self::FAILURE;
        }

        $mailer = config('mail.default');
        $this->line('Mailer: '.$mailer.' | host: '.config('mail.mailers.smtp.host').' | from: '.config('mail.from.address'));

        if ($mailer === 'log') {
            $this->warn('MAIL_MAILER is "log": emails are only written to storage/logs, not sent. Set MAIL_MAILER=smtp in .env.');
        }

        try {
            Mail::raw('If you can read this, your portfolio email settings work.', function ($message) use ($to) {
                $message->to($to)->subject('Portfolio mail test');
            });
        } catch (\Throwable $e) {
            $this->error('FAILED: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Sent to {$to}. Check your inbox (and the Spam folder).");

        return self::SUCCESS;
    }
}
