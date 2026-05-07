<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_sync_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscriber_id')->nullable()->constrained('newsletter_subscribers')->nullOnDelete();
            $table->foreignId('provider_connection_id')->nullable()->constrained('newsletter_provider_connections')->nullOnDelete();
            $table->foreignId('provider_audience_id')->nullable()->constrained('newsletter_provider_audiences')->nullOnDelete();
            $table->string('operation')->index();
            $table->string('sync_status')->index();
            $table->string('payload_hash', 64)->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('next_retry_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_sync_attempts');
    }
};
