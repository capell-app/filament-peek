<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tablesToUpdate = [
            'pages',
            'navigations',
            'sites',
            'site_domains',
            'types',
            'themes',
            'layouts',
            'languages',
            'translations',
            'page_urls',
            'asset_relations',
        ];

        foreach ($tablesToUpdate as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $table): void {
                if (! Schema::hasColumn($table->getTable(), 'workspace_id')) {
                    $table->unsignedBigInteger('workspace_id')->default(0)->index();
                }
                if (! Schema::hasColumn($table->getTable(), 'shadowed_by_workspace_id')) {
                    $table->unsignedBigInteger('shadowed_by_workspace_id')->default(0)->index();
                }
            });
        }
    }

    public function down(): void
    {
        $tablesToUpdate = [
            'pages',
            'navigations',
            'sites',
            'site_domains',
            'types',
            'themes',
            'layouts',
            'languages',
            'translations',
            'page_urls',
            'asset_relations',
        ];

        foreach ($tablesToUpdate as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $table): void {
                if (Schema::hasColumn($table->getTable(), 'workspace_id')) {
                    $table->dropIndex([sprintf('%s_workspace_id_index', $table->getTable())]);
                    $table->dropColumn('workspace_id');
                }
                if (Schema::hasColumn($table->getTable(), 'shadowed_by_workspace_id')) {
                    $table->dropIndex([sprintf('%s_shadowed_by_workspace_id_index', $table->getTable())]);
                    $table->dropColumn('shadowed_by_workspace_id');
                }
            });
        }
    }
};
