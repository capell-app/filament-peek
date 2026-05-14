<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publishing_revisions', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('revisionable');
            $table->string('revisionable_uuid')->nullable()->index();
            $table->foreignId('workspace_id')->nullable()->constrained('workspaces')->nullOnDelete();
            $table->foreignId('version_id')->nullable()->constrained('versions')->nullOnDelete();
            $table->unsignedBigInteger('version')->index();
            $table->string('event_type', 32)->index();
            $table->json('before_payload')->nullable();
            $table->json('after_payload')->nullable();
            $table->nullableMorphs('actor');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['revisionable_type', 'revisionable_uuid', 'version'], 'publishing_revisions_type_uuid_version_idx');
            $table->index(['workspace_id', 'event_type']);
            $table->index(['version_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publishing_revisions');
    }
};
