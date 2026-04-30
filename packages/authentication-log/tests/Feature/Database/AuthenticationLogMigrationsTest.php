<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('loads the package-owned authentication log table migration', function (): void {
    expect(Schema::hasTable(config('authentication-log.table_name', 'authentication_log')))->toBeTrue()
        ->and(Schema::hasColumn('authentication_log', 'last_seen_at'))->toBeTrue();
});
