<?php

declare(strict_types=1);

it('runs monitor AI usage command successfully', function (): void {
    $this->artisan('capell-admin:monitor-ai-usage')
        ->assertExitCode(0);
});
