<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->char('uuid', 36)->nullable()->index();
            $table->unsignedBigInteger('workspace_id')->default(0)->index();
            $table->unsignedBigInteger('shadowed_by_workspace_id')->default(0)->index();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blueprint_id')->constrained();
            $table->foreignId('layout_id')->constrained();
            $table->foreignId('event_venue_id')->nullable()->constrained('event_venues')->nullOnDelete();
            $table->string('name');
            $table->dateTime('starts_at')->nullable()->index();
            $table->dateTime('ends_at')->nullable();
            $table->string('timezone')->default('UTC');
            $table->boolean('all_day')->default(false);
            $table->string('visibility')->default('public')->index();
            $table->string('location_mode')->default('venue');
            $table->string('booking_mode')->default('disabled');
            $table->string('booking_url')->nullable();
            $table->string('booking_label')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->boolean('waitlist_enabled')->default(true);
            $table->text('recurrence_rule')->nullable();
            $table->json('recurrence')->nullable();
            $table->json('notification_settings')->nullable();
            $table->json('meta')->nullable();
            $table->visibleDates();
            $table->unsignedInteger('order')->default(0);
            $table->userstamps();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['site_id', 'blueprint_id']);
            $table->index(['site_id', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
