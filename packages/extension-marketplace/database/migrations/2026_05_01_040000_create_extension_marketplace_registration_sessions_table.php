<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extension_marketplace_registration_sessions', function (Blueprint $table): void {
            $table->id();
            $table->string('marketplace_registration_id');
            $table->string('domain')->index();
            $table->string('challenge_id', 80)->unique('cp_mrs_challenge_id_unique');
            $table->text('challenge_token');
            $table->string('verification_url', 512)->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extension_marketplace_registration_sessions');
    }
};
