<?php

declare(strict_types=1);

use Capell\PublicActions\Data\PublicActionMetadataData;
use Capell\PublicActions\Data\PublicActionPayloadData;
use Capell\PublicActions\Data\PublicActionSubmissionData;
use Capell\PublicActions\Models\PublicActionDestination;
use Capell\PublicActions\Models\PublicActionSubmission;
use Capell\PublicActions\Support\PublicActionDestinationAdapterRegistry;
use Capell\PublicActions\Support\PublicActionHandlerRegistry;
use Capell\PublicActions\Tests\Fakes\FakePublicActionDestinationAdapter;
use Capell\PublicActions\Tests\Fakes\FakePublicActionHandler;

it('resolves registered public action handlers from objects and classes', function (): void {
    $registry = new PublicActionHandlerRegistry;
    $objectHandler = new FakePublicActionHandler;

    $registry->register('object', $objectHandler);
    $registry->register('class', FakePublicActionHandler::class);

    $submission = new PublicActionSubmissionData(
        actionKey: 'preview-access',
        payload: new PublicActionPayloadData(['email' => 'person@example.test']),
        metadata: new PublicActionMetadataData(ipHash: 'hash'),
    );

    expect($registry->resolve('object'))->toBe($objectHandler)
        ->and($registry->resolve('class'))->toBeInstanceOf(FakePublicActionHandler::class)
        ->and($registry->resolve('missing'))->toBeNull()
        ->and($registry->resolve('class')?->handle($submission)->message)->toBe('preview-access')
        ->and($registry->all())->toHaveKeys(['object', 'class']);
});

it('rejects invalid public action handlers', function (): void {
    $registry = new PublicActionHandlerRegistry;

    expect(fn (): null => $registry->register('invalid', stdClass::class))
        ->toThrow(InvalidArgumentException::class);
});

it('resolves registered public action destination adapters from objects and classes', function (): void {
    $registry = new PublicActionDestinationAdapterRegistry;
    $objectAdapter = new FakePublicActionDestinationAdapter;

    $registry->register('object', $objectAdapter);
    $registry->register('class', FakePublicActionDestinationAdapter::class);

    $destination = PublicActionDestination::factory()->create(['adapter' => 'http_webhook']);
    $submission = PublicActionSubmission::factory()->create();

    expect($registry->resolve('object'))->toBe($objectAdapter)
        ->and($registry->resolve('class'))->toBeInstanceOf(FakePublicActionDestinationAdapter::class)
        ->and($registry->resolve('missing'))->toBeNull()
        ->and($registry->resolve('class')?->dispatch($destination, $submission)->responseStatus)->toBe(202)
        ->and($registry->all())->toHaveKeys(['object', 'class']);
});

it('rejects invalid public action destination adapters', function (): void {
    $registry = new PublicActionDestinationAdapterRegistry;

    expect(fn (): null => $registry->register('invalid', stdClass::class))
        ->toThrow(InvalidArgumentException::class);
});

it('binds registries in the container', function (): void {
    expect(resolve(PublicActionHandlerRegistry::class))->toBeInstanceOf(PublicActionHandlerRegistry::class)
        ->and(resolve(PublicActionDestinationAdapterRegistry::class))->toBeInstanceOf(PublicActionDestinationAdapterRegistry::class)
        ->and(resolve(PublicActionHandlerRegistry::class))->toBe(resolve(PublicActionHandlerRegistry::class))
        ->and(resolve(PublicActionDestinationAdapterRegistry::class))->toBe(resolve(PublicActionDestinationAdapterRegistry::class));
});
