<?php

declare(strict_types=1);

use Capell\Core\Enums\ContentGraph\ContentGraphEdgeKind;
use Capell\Core\Enums\ContentGraph\ContentGraphEdgeStrength;
use Capell\Core\Models\ContentGraphEdge;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Page;
use Capell\Diagnostics\Actions\Dashboard\BuildContentGraphHealthAction;

it('summarizes stale and high impact graph edges', function (): void {
    $layout = Layout::factory()->create();
    $page = Page::factory()->create();

    ContentGraphEdge::query()->create([
        'source_type' => Page::class,
        'source_id' => $page->id,
        'target_type' => Layout::class,
        'target_id' => $layout->id,
        'kind' => ContentGraphEdgeKind::UsesLayout,
        'strength' => ContentGraphEdgeStrength::Strong,
        'source_package' => 'capell-app/core',
    ]);

    ContentGraphEdge::query()->create([
        'source_type' => Page::class,
        'source_id' => 999999,
        'target_type' => Layout::class,
        'target_id' => $layout->id,
        'kind' => ContentGraphEdgeKind::UsesLayout,
        'strength' => ContentGraphEdgeStrength::Strong,
        'source_package' => 'capell-app/core',
    ]);

    $health = BuildContentGraphHealthAction::run();

    expect($health->totalEdges)->toBe(2)
        ->and($health->staleSourceEdges)->toBe(1)
        ->and($health->staleTargetEdges)->toBe(0)
        ->and($health->highImpactTargets)->not->toBeEmpty()
        ->and($health->highImpactTargets[0]['count'])->toBe(2);
});
