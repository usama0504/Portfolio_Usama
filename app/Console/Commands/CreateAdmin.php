<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email? : Login email}';

    protected $description = 'Create the dashboard admin (or reset its password)';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Admin email');
        $password = $this->secret('Password (min 8 characters)');
        $confirm = $this->secret('Confirm password');

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => ['required', 'email'], 'password' => ['required', 'string', 'min:8']],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        if ($password !== $confirm) {
            $this->error('The passwords do not match.');

            return self::FAILURE;
        }

        // The User model hashes the password automatically ("hashed" cast).
        User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Admin', 'password' => $password],
        );

        $this->info("Admin ready. Log in at /admin with {$email}");

        return self::SUCCESS;
    }
}
