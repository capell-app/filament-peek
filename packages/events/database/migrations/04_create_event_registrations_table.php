<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_occurrence_id')->constrained('event_occurrences')->cascadeOnDelete();
            $table->unsignedBigInteger('form_submission_id')->nullable()->index();
            $table->string('status')->default('pending')->index();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('waitlist_position')->nullable();
            $table->json('payload')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('registered_at')->nullable()->index();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['event_occurrence_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
