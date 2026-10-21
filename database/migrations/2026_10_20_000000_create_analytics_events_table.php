<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Privacy-friendly visit statistics: no cookies, no IP addresses.
     * "visitor" is a one-way hash that changes every day.
     */
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30);          // pageview, project_view, cv_download, whatsapp_click
            $table->string('label', 80)->nullable(); // e.g. the project slug
            $table->string('visitor', 40)->nullable();
            $table->string('device', 10)->nullable(); // mobile, tablet, desktop
            $table->string('referrer', 120)->nullable(); // host the visitor came from
            $table->timestamp('created_at')->useCurrent();

            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
