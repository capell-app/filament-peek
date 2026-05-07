<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('email_hash', 64);
            $table->longText('email');
            $table->longText('first_name')->nullable();
            $table->longText('last_name')->nullable();
            $table->longText('profile')->nullable();
            $table->string('status')->index();
            $table->foreignId('source_form_id')->nullable()->constrained('forms')->nullOnDelete();
            $table->string('source_form_handle')->nullable();
            $table->timestamp('pending_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('suppressed_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('complained_at')->nullable();
            $table->timestamps();

            $table->unique(['site_id', 'email_hash']);
            $table->index(['site_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
