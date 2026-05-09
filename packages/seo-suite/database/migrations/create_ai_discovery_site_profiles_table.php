<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ai_discovery_site_profiles')) {
            return;
        }

        Schema::create('ai_discovery_site_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->boolean('llms_txt_enabled')->default(true);
            $table->boolean('llms_full_txt_enabled')->default(false);
            $table->boolean('markdown_pages_enabled')->default(true);
            $table->boolean('accept_markdown_enabled')->default(false);
            $table->boolean('default_include_pages')->default(true);
            $table->unsignedSmallInteger('max_full_txt_pages')->default(50);
            $table->unsignedInteger('max_full_txt_bytes')->default(250000);
            $table->unsignedInteger('cache_ttl_seconds')->default(3600);
            $table->string('default_section')->default('Pages');
            $table->text('intro_markdown')->nullable();
            $table->string('status')->default('enabled');
            $table->timestamps();

            $table->unique(['site_id', 'language_id'], 'ai_discovery_site_profile_unique_context');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_discovery_site_profiles');
    }
};
