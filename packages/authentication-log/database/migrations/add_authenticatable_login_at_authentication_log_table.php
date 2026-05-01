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

        if (Schema::hasIndex($tableName, 'authenticatable_login_at_index')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table): void {
            $table->index(
                ['authenticatable_type', 'authenticatable_id', 'login_at'],
                'authenticatable_login_at_index',
            );
        });
    }

    public function down(): void
    {
        $tableName = config('authentication-log.table_name', 'authentication_log');

        if (! Schema::hasTable($tableName)) {
            return;
        }

        if (! Schema::hasIndex($tableName, 'authenticatable_login_at_index')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table): void {
            $table->dropIndex('authenticatable_login_at_index');
        });
    }
};
