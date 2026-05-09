<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('capell-public-actions.tables.destinations', 'public_action_destinations'), function (Blueprint $table): void {
            $table->id();
            $table->foreignId('public_action_id')->constrained(config('capell-public-actions.tables.actions', 'public_actions'))->cascadeOnDelete();
            $table->string('adapter')->index();
            $table->string('name');
            $table->string('status')->index();
            $table->longText('endpoint_url')->nullable();
            $table->longText('secret')->nullable();
            $table->longText('headers')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['public_action_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('capell-public-actions.tables.destinations', 'public_action_destinations'));
    }
};
