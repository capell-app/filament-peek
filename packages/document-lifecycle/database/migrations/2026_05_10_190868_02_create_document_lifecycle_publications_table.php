<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('document_lifecycle_publications')) {
            return;
        }

        Schema::create('document_lifecycle_publications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('document_id')->constrained('document_lifecycle_documents')->cascadeOnDelete();
            $table->unsignedBigInteger('published_revision_id')->nullable()->index();
            $table->string('version_label');
            $table->string('content_hash', 64);
            $table->nullableMorphs('published_actor', 'document_lifecycle_publications_actor_idx');
            $table->timestamp('published_at')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['document_id', 'version_label']);
            $table->index(['document_id', 'published_at']);
            $table->index(['document_id', 'content_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_lifecycle_publications');
    }
};
