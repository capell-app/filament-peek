<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_provider_connections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('name');
            $table->string('provider')->index();
            $table->string('auth_type')->index();
            $table->longText('credentials')->nullable();
            $table->longText('oauth_tokens')->nullable();
            $table->longText('webhook_secret')->nullable();
            $table->boolean('is_enabled')->default(true)->index();
            $table->timestamps();

            $table->index(['site_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_provider_connections');
    }
};
