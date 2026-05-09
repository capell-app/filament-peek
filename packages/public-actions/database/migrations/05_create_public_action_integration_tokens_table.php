<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('capell-public-actions.tables.integration_tokens', 'public_action_integration_tokens'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('name');
            $table->string('token_hash')->unique();
            $table->string('provider')->index();
            $table->json('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable()->index();
            $table->timestamps();

            $table->index(['site_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('capell-public-actions.tables.integration_tokens', 'public_action_integration_tokens'));
    }
};
