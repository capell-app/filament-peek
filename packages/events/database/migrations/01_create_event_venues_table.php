<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_venues', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('address_id')->nullable()->index();
            $table->string('name');
            $table->string('line1')->nullable();
            $table->string('line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('map_url')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('status')->default(true);
            $table->userstamps();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['site_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_venues');
    }
};
