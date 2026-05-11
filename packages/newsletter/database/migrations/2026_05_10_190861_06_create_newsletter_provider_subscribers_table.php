<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_provider_subscribers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->cascadeOnDelete();
            $table->foreignId('provider_audience_id')->constrained('newsletter_provider_audiences')->cascadeOnDelete();
            $table->string('remote_id')->nullable();
            $table->string('remote_status')->nullable()->index();
            $table->timestamp('synced_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['subscriber_id', 'provider_audience_id'], 'newsletter_provider_subscriber_unique');
            $table->index(['provider_audience_id', 'remote_id'], 'newsletter_provider_subscriber_remote_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_provider_subscribers');
    }
};
