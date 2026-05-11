<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ai_discovery_page_profiles')) {
            return;
        }

        Schema::create('ai_discovery_page_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->boolean('include_in_ai_index')->default(true);
            $table->string('exclude_reason')->nullable();
            $table->text('summary')->nullable();
            $table->string('section')->default('Pages');
            $table->unsignedSmallInteger('priority')->default(500);
            $table->longText('markdown_override')->nullable();
            $table->longText('generated_markdown')->nullable();
            $table->string('markdown_hash', 64)->nullable();
            $table->timestamp('last_generated_at')->nullable();
            $table->timestamps();

            $table->unique(['page_id', 'site_id', 'language_id'], 'ai_discovery_page_profile_unique_context');
            $table->index(['site_id', 'language_id', 'include_in_ai_index'], 'ai_discovery_page_profile_lookup');
            $table->index(['site_id', 'language_id', 'section', 'priority'], 'ai_discovery_page_profile_ordering');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_discovery_page_profiles');
    }
};
