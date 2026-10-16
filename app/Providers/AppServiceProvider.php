<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // The only login is the dashboard's own /admin/login — switch off the
        // extra routes (/register, /login, /forgot-password …) of the Fortify package.
        \Laravel\Fortify\Fortify::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // The reset link in the email opens the dashboard's own reset page.
        ResetPassword::createUrlUsing(fn ($user, string $token) => route('admin.password.reset', [
            'token' => $token,
            'email' => $user->getEmailForPasswordReset(),
        ]));

        $this->configureRateLimiters();
         if (config('app.env') !== 'local' || request()->header('x-forwarded-proto') == 'https') {
        URL::forceScheme('https');
    }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureRateLimiters(): void
    {
        // Contact form: per visitor (IP), so one person cannot flood your inbox.
        RateLimiter::for('contact', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perDay(20)->by($request->ip()),
            ];
        });

        RateLimiter::for('admin-forgot', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(10)->by($request->ip()),
            ];
        });

        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
