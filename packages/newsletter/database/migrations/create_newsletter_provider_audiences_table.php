<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_provider_audiences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_connection_id')->constrained('newsletter_provider_connections')->cascadeOnDelete();
            $table->string('name');
            $table->string('remote_id');
            $table->json('settings')->nullable();
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('sync_subscribed_only')->default(true);
            $table->timestamps();

            $table->unique(['provider_connection_id', 'remote_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_provider_audiences');
    }
};
