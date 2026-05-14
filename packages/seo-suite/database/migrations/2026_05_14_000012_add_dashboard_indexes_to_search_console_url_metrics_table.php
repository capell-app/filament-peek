<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('search_console_url_metrics')) {
            return;
        }

        Schema::table('search_console_url_metrics', function (Blueprint $table): void {
            $table->index(['site_id', 'window_end', 'window_start'], 'search_console_metrics_site_window_index');
            $table->index(['site_id', 'window_end', 'window_start', 'click_delta'], 'search_console_metrics_site_window_delta_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('search_console_url_metrics')) {
            return;
        }

        Schema::table('search_console_url_metrics', function (Blueprint $table): void {
            $table->dropIndex('search_console_metrics_site_window_index');
            $table->dropIndex('search_console_metrics_site_window_delta_index');
        });
    }
};
