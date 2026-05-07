<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_notification_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_occurrence_id')->nullable()->constrained('event_occurrences')->cascadeOnDelete();
            $table->foreignId('event_registration_id')->nullable()->constrained('event_registrations')->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('recipient_email')->nullable();
            $table->string('status')->default('queued')->index();
            $table->timestamp('scheduled_for')->nullable()->index();
            $table->timestamp('sent_at')->nullable();
            $table->text('error')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_notification_logs');
    }
};
