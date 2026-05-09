<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ai_discovery_crawler_rules')) {
            return;
        }

        Schema::create('ai_discovery_crawler_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained('sites')->cascadeOnDelete();
            $table->string('provider');
            $table->string('user_agent');
            $table->string('purpose')->default('unknown');
            $table->string('directive')->default('disallow');
            $table->string('path')->default('/');
            $table->unsignedSmallInteger('crawl_delay_seconds')->nullable();
            $table->boolean('enabled')->default(true);
            $table->string('source_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'enabled', 'purpose'], 'ai_discovery_crawler_rule_lookup');
            $table->index(['provider', 'user_agent'], 'ai_discovery_crawler_rule_provider_user_agent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_discovery_crawler_rules');
    }
};
