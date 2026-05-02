<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extension_marketplace_instances', function (Blueprint $table): void {
            $table->id();
            $table->uuid('instance_id')->unique();
            $table->text('signing_secret_encrypted');
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extension_marketplace_instances');
    }
};
