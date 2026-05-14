<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('document_lifecycle_documents')) {
            return;
        }

        Schema::create('document_lifecycle_documents', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('status', 32)->default('draft')->index();
            $table->nullableMorphs('documentable', 'document_lifecycle_docs_documentable_idx');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_lifecycle_documents');
    }
};
