<?php

declare(strict_types=1);

use Capell\PublicActions\Enums\PublicActionStatus;
use Capell\PublicActions\Enums\PublicActionSubmissionStatus;
use Capell\PublicActions\Models\PublicAction;
use Capell\PublicActions\Models\PublicActionSubmission;

it('submits an active public action and redirects with no-store headers', function (): void {
    PublicAction::factory()->create([
        'key' => 'preview-access',
        'handler_key' => 'test.handler',
        'success_redirect_url' => 'https://example.test/thanks',
    ]);

    $response = $this
        ->from('/source-page')
        ->withHeader('User-Agent', 'PublicActionsTest/1.0')
        ->post('/actions/preview-access', [
            'email' => 'person@example.test',
            'source_type' => 'button',
            'source_id' => 'hero',
        ]);

    $response
        ->assertRedirect('https://example.test/thanks')
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertHeader('Pragma', 'no-cache')
        ->assertHeader('Expires', '0');

    $submission = PublicActionSubmission::query()->firstOrFail();

    expect($submission->status)->toBe(PublicActionSubmissionStatus::Handled)
        ->and($submission->payload)->toMatchArray(['email' => 'person@example.test'])
        ->and($submission->source_type)->toBe('button')
        ->and($submission->source_id)->toBe('hero')
        ->and($submission->metadata['ip_hash'] ?? null)->toBe(hash('sha256', '127.0.0.1'))
        ->and($submission->metadata['user_agent'] ?? null)->toBe('PublicActionsTest/1.0')
        ->and($submission->metadata)->not->toHaveKey('ip');
});

it('returns json for json submissions', function (): void {
    PublicAction::factory()->create([
        'key' => 'download-access',
        'handler_key' => 'test.handler',
    ]);

    $response = $this->postJson('/actions/download-access', [
        'email' => 'person@example.test',
    ]);

    $response
        ->assertOk()
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertJson([
            'success' => true,
            'message' => 'download-access',
        ]);
});

it('rejects inactive actions without creating a submission', function (): void {
    PublicAction::factory()->create([
        'key' => 'paused-action',
        'status' => PublicActionStatus::Paused,
        'handler_key' => 'test.handler',
    ]);

    $response = $this->postJson('/actions/paused-action', [
        'email' => 'person@example.test',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['action']);

    expect(PublicActionSubmission::query()->count())->toBe(0);
});

it('returns not found for missing actions', function (): void {
    $this->post('/actions/missing-action', [
        'email' => 'person@example.test',
    ])->assertNotFound();
});

it('marks the submission failed when the handler validation fails', function (): void {
    PublicAction::factory()->create([
        'key' => 'failing-action',
        'handler_key' => 'test.validation-handler',
    ]);

    $response = $this->postJson('/actions/failing-action', [
        'email' => '',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);

    expect(PublicActionSubmission::query()->firstOrFail()->status)->toBe(PublicActionSubmissionStatus::Failed);
});

it('renders an enabled public action page without exposing package internals', function (): void {
    PublicAction::factory()->create([
        'key' => 'page-action',
        'name' => 'Request preview',
        'handler_key' => 'test.handler',
        'payload_schema' => [
            'fields' => [
                ['key' => 'email', 'label' => 'Email address', 'type' => 'email', 'required' => true],
            ],
        ],
        'settings' => ['public_page_enabled' => true],
    ]);

    $response = $this->get('/actions/page-action');

    $response
        ->assertOk()
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertSee('Request preview')
        ->assertSee('Email address')
        ->assertDontSee('test.handler')
        ->assertDontSee('capell-public-actions')
        ->assertDontSee('handler_key');
});

it('does not render disabled public action pages', function (): void {
    PublicAction::factory()->create([
        'key' => 'hidden-page',
        'handler_key' => 'test.handler',
        'settings' => ['public_page_enabled' => false],
    ]);

    $this->get('/actions/hidden-page')->assertNotFound();
});

it('throttles repeated submissions for the same action and email', function (): void {
    PublicAction::factory()->create([
        'key' => 'limited-action',
        'handler_key' => 'test.handler',
    ]);

    foreach (range(1, 12) as $attempt) {
        $this->post('/actions/limited-action', [
            'email' => 'person@example.test',
            'attempt' => $attempt,
        ])->assertRedirect();
    }

    $this->post('/actions/limited-action', [
        'email' => 'person@example.test',
    ])->assertTooManyRequests();
});
