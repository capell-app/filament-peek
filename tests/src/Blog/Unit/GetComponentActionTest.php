<?php

declare(strict_types=1);

use Capell\Blog\Livewire\Page\ArchivePage;
use Capell\Core\Actions\GetComponentClassAction;
use Capell\Tests\Fixtures\Support\Concerns\TestingFrontend;

uses(TestingFrontend::class);

it('returns component class for livewire component', function (): void {
    $component = 'capell-blog::livewire.page.archive';

    $componentClass = GetComponentClassAction::run($component);

    expect($componentClass)
        ->toBe(ArchivePage::class);
});
