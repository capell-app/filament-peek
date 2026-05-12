<?php

declare(strict_types=1);

use Capell\AccessGate\Support\AccessGateSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        AccessGateSchema::builder()->table('access_gate_registrations', function (Blueprint $table): void {
            $table->index(['access_area_id', 'email_normalized', 'requested_at', 'id'], 'ag_regs_area_email_norm_requested_idx');
            $table->index(['access_area_id', 'user_id', 'requested_at', 'id'], 'ag_regs_area_user_requested_idx');
        });

        AccessGateSchema::builder()->table('access_gate_grants', function (Blueprint $table): void {
            $table->index(['access_area_id', 'registration_id', 'status', 'revoked_at'], 'ag_grants_area_reg_status_idx');
            $table->index(['access_area_id', 'email', 'status', 'revoked_at'], 'ag_grants_area_email_status_idx');
            $table->index(['access_area_id', 'user_id', 'status', 'revoked_at'], 'ag_grants_area_user_status_idx');
        });
    }

    public function down(): void
    {
        AccessGateSchema::builder()->table('access_gate_grants', function (Blueprint $table): void {
            $table->dropIndex('ag_grants_area_user_status_idx');
            $table->dropIndex('ag_grants_area_email_status_idx');
            $table->dropIndex('ag_grants_area_reg_status_idx');
        });

        AccessGateSchema::builder()->table('access_gate_registrations', function (Blueprint $table): void {
            $table->dropIndex('ag_regs_area_user_requested_idx');
            $table->dropIndex('ag_regs_area_email_norm_requested_idx');
        });
    }
};
