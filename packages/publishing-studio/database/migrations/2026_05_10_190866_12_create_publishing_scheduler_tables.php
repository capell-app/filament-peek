<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publishing_scheduler_events', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('event_type', 64);
            $table->string('state', 64)->default('scheduled');
            $table->string('source_type', 128);
            $table->unsignedBigInteger('source_id');
            $table->foreignId('workspace_id')->nullable()->constrained('workspaces')->nullOnDelete();
            $table->unsignedBigInteger('site_id')->nullable()->index();
            $table->unsignedBigInteger('owner_id')->nullable()->index();
            $table->string('owner_type', 128)->nullable();
            $table->timestamp('scheduled_for')->index();
            $table->string('display_timezone', 64)->default('UTC');
            $table->string('idempotency_key')->unique();
            $table->unsignedInteger('schedule_version')->default(1);
            $table->string('actor_type', 128)->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('last_succeeded_at')->nullable();
            $table->timestamp('last_failed_at')->nullable();
            $table->unsignedInteger('failure_count')->default(0);
            $table->string('last_failure_class')->nullable();
            $table->text('last_failure_message')->nullable();
            $table->string('skipped_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['state', 'scheduled_for']);
            $table->index(['event_type', 'state', 'scheduled_for']);
            $table->index(['workspace_id', 'event_type', 'scheduled_for'], 'pub_sched_events_workspace_type_for_idx');
            $table->index(['site_id', 'state', 'scheduled_for']);
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('publishing_scheduler_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('scheduler_event_id')->constrained('publishing_scheduler_events')->cascadeOnDelete();
            $table->string('state', 64)->default('pending');
            $table->string('recipient_type', 128);
            $table->unsignedBigInteger('recipient_id');
            $table->string('dedupe_key')->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('snoozed_until')->nullable();
            $table->text('failure_message')->nullable();
            $table->timestamps();

            $table->index(['scheduler_event_id', 'state']);
            $table->index(['recipient_type', 'recipient_id'], 'pub_sched_deliveries_recipient_idx');
        });

        Schema::create('publishing_scheduler_ical_tokens', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('token_hash', 128)->unique();
            $table->string('scope', 32);
            $table->unsignedBigInteger('site_id')->nullable()->index();
            $table->string('owner_type', 128);
            $table->unsignedBigInteger('owner_id');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
            $table->index(['scope', 'site_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publishing_scheduler_ical_tokens');
        Schema::dropIfExists('publishing_scheduler_deliveries');
        Schema::dropIfExists('publishing_scheduler_events');
    }
};
