<?php

declare(strict_types=1);

use Capell\PublicActions\Data\PublicActionDispatchResultData;
use Capell\PublicActions\Data\PublicActionMetadataData;
use Capell\PublicActions\Data\PublicActionPayloadData;
use Capell\PublicActions\Data\PublicActionProviderPresetData;
use Capell\PublicActions\Data\PublicActionResultData;
use Capell\PublicActions\Data\PublicActionSubmissionData;
use Capell\PublicActions\Data\PublicActionZapierSubmissionData;
use Capell\PublicActions\Enums\PublicActionDestinationStatus;
use Capell\PublicActions\Enums\PublicActionDispatchStatus;
use Capell\PublicActions\Enums\PublicActionIntegrationProvider;
use Capell\PublicActions\Enums\PublicActionIntegrationTokenAbility;
use Capell\PublicActions\Enums\PublicActionStatus;
use Capell\PublicActions\Enums\PublicActionSubmissionStatus;

it('keeps public action submission payloads as typed data boundaries', function (): void {
    $payload = new PublicActionPayloadData(['email' => 'ben@example.com']);
    $metadata = new PublicActionMetadataData(
        ipHash: 'hash',
        userAgent: 'Browser',
        url: 'https://example.test/signup',
        referer: 'https://example.test',
        route: 'public-actions.submit',
        siteId: 12,
    );

    $submission = new PublicActionSubmissionData(
        actionKey: 'newsletter.signup',
        payload: $payload,
        metadata: $metadata,
        sourceType: 'form',
        sourceId: '42',
    );

    expect($submission->payload->values)->toBe(['email' => 'ben@example.com'])
        ->and($submission->metadata->siteId)->toBe(12)
        ->and($submission->sourceType)->toBe('form');
});

it('serializes public action result DTOs with snake case mapping', function (): void {
    $result = PublicActionResultData::from([
        'success' => true,
        'redirect_url' => '/thanks',
        'created_model_type' => 'subscriber',
        'created_model_id' => '99',
    ]);
    $dispatch = PublicActionDispatchResultData::from([
        'success' => false,
        'response_status' => 500,
        'response_summary' => 'failed',
        'external_id' => 'ext-1',
        'error_message' => 'Timeout',
    ]);
    $preset = PublicActionProviderPresetData::from([
        'key' => 'webhook',
        'adapter' => 'http',
        'method' => 'POST',
        'expects_json' => true,
    ]);
    $zapier = PublicActionZapierSubmissionData::from([
        'id' => 'sub-1',
        'action_key' => 'newsletter.signup',
        'submitted_at' => '2026-05-20T10:00:00+00:00',
        'payload' => ['email' => 'ben@example.com'],
        'site_name' => 'Main site',
        'source_type' => 'form',
    ]);

    expect($result->redirectUrl)->toBe('/thanks')
        ->and($dispatch->responseStatus)->toBe(500)
        ->and($preset->expectsJson)->toBeTrue()
        ->and($zapier->actionKey)->toBe('newsletter.signup');
});

it('exposes labels for public action workflow enums', function (): void {
    expect(PublicActionDestinationStatus::Active->getLabel())->toBeString()
        ->and(PublicActionDispatchStatus::Retryable->getLabel())->toBeString()
        ->and(PublicActionIntegrationProvider::Zapier->getLabel())->toBeString()
        ->and(PublicActionIntegrationTokenAbility::SubmitActions->getLabel())->toBeString()
        ->and(PublicActionStatus::Archived->getLabel())->toBeString()
        ->and(PublicActionSubmissionStatus::Handled->getLabel())->toBeString();
});
