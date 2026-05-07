<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_segments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->string('name');
            $table->string('handle');
            $table->string('type')->index();
            $table->json('filters')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['site_id', 'handle']);
        });

        Schema::create('newsletter_segment_subscriber', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('newsletter_segment_id')->constrained('newsletter_segments')->cascadeOnDelete();
            $table->foreignId('newsletter_subscriber_id')->constrained('newsletter_subscribers')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['newsletter_segment_id', 'newsletter_subscriber_id'], 'newsletter_segment_subscriber_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_segment_subscriber');
        Schema::dropIfExists('newsletter_segments');
    }
};
