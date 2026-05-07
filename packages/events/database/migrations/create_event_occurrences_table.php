<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_occurrences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_venue_id')->nullable()->constrained('event_venues')->nullOnDelete();
            $table->string('occurrence_key')->index();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->nullable()->index();
            $table->string('timezone')->default('UTC');
            $table->boolean('all_day')->default(false);
            $table->string('status')->default('scheduled')->index();
            $table->string('visibility')->default('public')->index();
            $table->string('location_mode')->default('venue');
            $table->string('booking_mode')->default('disabled');
            $table->string('booking_url')->nullable();
            $table->string('booking_label')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->unsignedInteger('registration_count')->default(0);
            $table->boolean('waitlist_enabled')->default(true);
            $table->boolean('is_override')->default(false);
            $table->json('override_data')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'occurrence_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_occurrences');
    }
};
