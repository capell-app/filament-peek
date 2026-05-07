<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_consent_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('event_type')->index();
            $table->string('subscriber_status')->nullable()->index();
            $table->string('source_type')->nullable()->index();
            $table->string('source_id')->nullable();
            $table->foreignId('provider_connection_id')->nullable()->constrained('newsletter_provider_connections')->nullOnDelete();
            $table->longText('evidence')->nullable();
            $table->longText('metadata')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_consent_events');
    }
};
