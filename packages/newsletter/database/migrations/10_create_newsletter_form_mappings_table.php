<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_form_mappings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->cascadeOnDelete();
            $table->foreignId('form_id')->nullable()->constrained('forms')->nullOnDelete();
            $table->string('name');
            $table->string('form_handle')->nullable();
            $table->string('email_field');
            $table->string('first_name_field')->nullable();
            $table->string('last_name_field')->nullable();
            $table->string('consent_field')->nullable();
            $table->text('consent_text')->nullable();
            $table->string('consent_version')->nullable();
            $table->json('fixed_tag_ids')->nullable();
            $table->json('field_tag_mappings')->nullable();
            $table->boolean('requires_double_opt_in')->default(true);
            $table->string('confirmation_mode')->default('capell_owned');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['site_id', 'form_handle']);
            $table->index(['site_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_form_mappings');
    }
};
