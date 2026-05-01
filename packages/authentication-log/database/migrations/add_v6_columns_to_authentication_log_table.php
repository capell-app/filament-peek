<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('authentication-log.table_name', 'authentication_log');

        if (! Schema::hasTable($tableName)) {
            return;
        }

        if (! Schema::hasColumn($tableName, 'device_id')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('device_id')->nullable()->index();
            });
        }

        if (! Schema::hasColumn($tableName, 'device_name')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('device_name')->nullable();
            });
        }

        if (! Schema::hasColumn($tableName, 'is_trusted')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->boolean('is_trusted')->default(false);
            });
        }

        if (! Schema::hasColumn($tableName, 'last_activity_at')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->timestamp('last_activity_at')->nullable();
            });
        }

        if (! Schema::hasColumn($tableName, 'is_suspicious')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->boolean('is_suspicious')->default(false);
            });
        }

        if (! Schema::hasColumn($tableName, 'suspicious_reason')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('suspicious_reason')->nullable();
            });
        }
    }

    public function down(): void
    {
        $tableName = config('authentication-log.table_name', 'authentication_log');

        if (! Schema::hasTable($tableName)) {
            return;
        }

        foreach ([
            'device_id',
            'device_name',
            'is_trusted',
            'last_activity_at',
            'is_suspicious',
            'suspicious_reason',
        ] as $columnName) {
            if (! Schema::hasColumn($tableName, $columnName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($columnName): void {
                $table->dropColumn($columnName);
            });
        }
    }
};
