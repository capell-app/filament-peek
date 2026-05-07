<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_provider_interest_mappings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_audience_id')->constrained('newsletter_provider_audiences')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->string('remote_interest_id');
            $table->string('remote_interest_type')->nullable();
            $table->string('remote_name')->nullable();
            $table->timestamps();

            $table->unique(['provider_audience_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_provider_interest_mappings');
    }
};
