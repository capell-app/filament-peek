<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('authentication-log.table_name', 'authentication_log');

        if (! Schema::hasTable($tableName)) {
            return;
        }

        if (! Schema::hasColumn($tableName, 'last_seen_at')) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->timestamp('last_seen_at')->nullable();
            });
        }

        DB::table($tableName)->update([
            'last_seen_at' => DB::raw('CASE WHEN login_at > logout_at THEN login_at ELSE logout_at END'),
        ]);
    }

    public function down(): void
    {
        $tableName = config('authentication-log.table_name', 'authentication_log');

        if (! Schema::hasTable($tableName)) {
            return;
        }

        if (! Schema::hasColumn($tableName, 'last_seen_at')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table): void {
            $table->dropColumn('last_seen_at');
        });
    }
};
