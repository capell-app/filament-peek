<?php

declare(strict_types=1);

use Capell\AgentBridge\Support\KnowledgeRepository;
use Capell\AgentBridge\Tools\Knowledge\RecommendPackagesTool;
use Illuminate\Validation\ValidationException;
use Laravel\Mcp\Request;

it('recommends matching packages from the knowledge repository', function (): void {
    app()->setBasePath(getcwd());

    $response = (new RecommendPackagesTool)->handle(
        new Request(['query' => 'seo redirects']),
        new KnowledgeRepository,
    );

    $structuredContent = $response->getStructuredContent();

    expect($structuredContent)
        ->not->toBeNull()
        ->and($structuredContent['query'])->toBe('seo redirects')
        ->and($structuredContent['recommendations'])->not->toBeEmpty();

    $recommendedNames = collect($structuredContent['recommendations'])->pluck('name');

    expect($recommendedNames)
        ->toContain('capell-app/seo-suite')
        ->toContain('capell-app/redirects');
});

it('requires a package recommendation query', function (): void {
    (new RecommendPackagesTool)->handle(
        new Request([]),
        new KnowledgeRepository,
    );
})->throws(ValidationException::class);
