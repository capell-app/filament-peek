<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('capell-public-actions.tables.actions', 'public_actions'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('site_scope_key')->default('global');
            $table->string('key');
            $table->string('name');
            $table->string('status')->index();
            $table->string('handler_key');
            $table->string('success_redirect_url', 2048)->nullable();
            $table->string('failure_redirect_url', 2048)->nullable();
            $table->string('success_message')->nullable();
            $table->string('failure_message')->nullable();
            $table->json('payload_schema')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['site_scope_key', 'key']);
            $table->index(['site_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('capell-public-actions.tables.actions', 'public_actions'));
    }
};
