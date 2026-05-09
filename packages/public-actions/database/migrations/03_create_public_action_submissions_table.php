<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('capell-public-actions.tables.submissions', 'public_action_submissions'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('public_action_id')->constrained(config('capell-public-actions.tables.actions', 'public_actions'))->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('source_type')->nullable();
            $table->string('source_id')->nullable();
            $table->longText('payload');
            $table->json('metadata')->nullable();
            $table->string('status')->index();
            $table->timestamp('submitted_at')->index();
            $table->timestamps();

            $table->index(['public_action_id', 'submitted_at']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('capell-public-actions.tables.submissions', 'public_action_submissions'));
    }
};
