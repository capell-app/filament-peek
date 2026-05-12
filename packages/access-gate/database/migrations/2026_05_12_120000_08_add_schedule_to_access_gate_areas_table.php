<?php

declare(strict_types=1);

use Capell\AccessGate\Support\AccessGateSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        AccessGateSchema::builder()->table('access_gate_areas', function (Blueprint $table): void {
            $table
                ->dateTime('opens_at')
                ->nullable()
                ->after('status');

            $table
                ->dateTime('closes_at')
                ->nullable()
                ->after('opens_at');
        });
    }

    public function down(): void
    {
        AccessGateSchema::builder()->table('access_gate_areas', function (Blueprint $table): void {
            $table->dropColumn(['opens_at', 'closes_at']);
        });
    }
};
