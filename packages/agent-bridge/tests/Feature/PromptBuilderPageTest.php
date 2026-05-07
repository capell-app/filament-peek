<?php

declare(strict_types=1);

use Capell\AgentBridge\Filament\Pages\CapellAgentBridgePromptBuilderPage;

it('builds an agent bridge prompt from the submitted intent', function (): void {
    $prompt = promptFromState([
        'goal' => 'create a draft landing page',
        'area' => 'pages',
        'operation' => 'create',
        'safety' => 'preview_first',
        'target' => 'Spring campaign page',
        'constraints' => 'Use the default campaign layout',
        'success_criteria' => 'A draft exists but is not published',
    ]);

    expect($prompt)
        ->toContain('create a draft landing page')
        ->toContain('Area: pages')
        ->toContain('Operation: create')
        ->toContain('Safety mode: preview_first')
        ->toContain('Spring campaign page')
        ->toContain('Use the default campaign layout')
        ->toContain('A draft exists but is not published');
});

it('uses safe defaults when optional prompt context is omitted', function (): void {
    $prompt = promptFromState([
        'goal' => 'inspect stale cache state',
        'area' => 'cache',
        'operation' => 'inspect',
        'safety' => 'read_only',
        'target' => '',
        'constraints' => '',
        'success_criteria' => '',
    ]);

    expect($prompt)
        ->toContain('Target/context:')
        ->toContain('Not provided.')
        ->toContain('Use Capell package boundaries, policies, and preview-first workflow.')
        ->toContain('Explain what changed or why no change is needed.');
});

/**
 * @param  array<string, mixed>  $state
 */
function promptFromState(array $state): string
{
    $method = new ReflectionMethod(CapellAgentBridgePromptBuilderPage::class, 'promptFromState');

    return (string) $method->invoke(new CapellAgentBridgePromptBuilderPage, $state);
}
