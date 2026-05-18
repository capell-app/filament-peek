<?php

declare(strict_types=1);

use Capell\PublishingStudio\Facades\CapellPublishingStudio;
use Capell\PublishingStudio\Tests\Fixtures\Autoload\AnotherTestSubscriber;
use Capell\PublishingStudio\Tests\Fixtures\Autoload\TestSubscriber;

test('can register subscriber via facade', function (): void {
    $subscriberClass = TestSubscriber::class;

    CapellPublishingStudio::subscribe($subscriberClass);

    expect(CapellPublishingStudio::hasSubscriber($subscriberClass))->toBeTrue();
});

test('can retrieve registered subscribers', function (): void {
    $subscriber1 = TestSubscriber::class;
    $subscriber2 = AnotherTestSubscriber::class;

    CapellPublishingStudio::subscribe($subscriber1);
    CapellPublishingStudio::subscribe($subscriber2);

    $subscribers = CapellPublishingStudio::getSubscribers();

    expect($subscribers)->toContain($subscriber1, $subscriber2);
});

test('does not register duplicate subscribers', function (): void {
    $subscriberClass = TestSubscriber::class;

    CapellPublishingStudio::subscribe($subscriberClass);
    CapellPublishingStudio::subscribe($subscriberClass);

    $subscribers = CapellPublishingStudio::getSubscribers();

    expect($subscribers)->toHaveCount(1);
});
