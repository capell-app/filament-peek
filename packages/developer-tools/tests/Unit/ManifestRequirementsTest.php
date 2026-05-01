<?php

declare(strict_types=1);

describe('developer-tools capell.json manifest', function (): void {
    it('declares admin and console package metadata', function (): void {
        $manifest = json_decode(
            file_get_contents(__DIR__ . '/../../capell.json'),
            associative: true,
        );

        expect($manifest)
            ->toMatchArray([
                'name' => 'capell-app/developer-tools',
                'kind' => 'package',
                'capell-version' => '^4.0',
            ])
            ->and($manifest['contexts'])->toContain('admin')
            ->and($manifest['providers']['shared'])->toContain('Capell\\DeveloperTools\\Providers\\DeveloperToolsServiceProvider')
            ->and($manifest['providers']['admin'])->toContain('Capell\\DeveloperTools\\Providers\\AdminServiceProvider');
    });
});
