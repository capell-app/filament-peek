<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deployment_connections', function (Blueprint $table): void {
            $table->id();
            $table->string('provider')->index();
            $table->string('repo_owner');
            $table->string('repo_name');
            $table->string('default_branch')->default('main');
            $table->text('access_token_encrypted');
            $table->text('refresh_token_encrypted')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->string('install_policy')->default('pr_auto_merge');
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['provider', 'repo_owner', 'repo_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deployment_connections');
    }
};
