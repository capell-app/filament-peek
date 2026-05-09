<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ai_discovery_snapshots')) {
            return;
        }

        Schema::create('ai_discovery_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('site_domain_id')->nullable()->constrained('site_domains')->nullOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('kind');
            $table->foreignId('page_id')->nullable()->constrained('pages')->cascadeOnDelete();
            $table->string('context_key');
            $table->string('content_hash', 64);
            $table->unsignedInteger('byte_size')->default(0);
            $table->string('cache_key');
            $table->timestamp('generated_at');
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('fresh');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['site_id', 'language_id', 'kind', 'context_key'], 'ai_discovery_snapshot_unique_context');
            $table->index(['site_id', 'language_id', 'kind', 'status'], 'ai_discovery_snapshot_lookup');
            $table->index('expires_at', 'ai_discovery_snapshot_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_discovery_snapshots');
    }
};
