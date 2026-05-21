<?php

declare(strict_types=1);

use Capell\Core\Models\Page;
use Capell\Core\Models\Translation;
use Capell\Frontend\Contracts\AdminAccessCheckerInterface;
use Capell\FrontendAuthoring\Data\EditableRegionPayloadData;
use Capell\FrontendAuthoring\Health\FrontendAuthoringHealthCheck;
use Capell\FrontendAuthoring\Http\Middleware\PassThroughActivityMiddleware;
use Capell\FrontendAuthoring\Http\Requests\BeaconRequest;
use Capell\FrontendAuthoring\Livewire\EditRegionField;
use Capell\FrontendAuthoring\Support\EditableRegionSigner;
use Capell\Tests\Fixtures\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    Livewire::component('edit-region-field', EditRegionField::class);
});

it('declares beacon validation and passes activity middleware through unchanged', function (): void {
    $request = new BeaconRequest;
    $middleware = new PassThroughActivityMiddleware;
    $httpRequest = Request::create('/beacon', 'POST', ['url' => 'https://example.test/page']);

    $response = $middleware->handle(
        $httpRequest,
        fn (Request $nextRequest): string => 'handled:' . $nextRequest->input('url'),
    );

    expect($request->authorize())->toBeTrue()
        ->and($request->rules())->toBe([
            'url' => ['required', 'url', 'max:2048'],
        ])
        ->and($response)->toBe('handled:https://example.test/page')
        ->and(FrontendAuthoringHealthCheck::compatibleCapellApiVersion())->toBe('^4.0');
});

it('loads current editable values and saves text updates from the livewire editor', function (): void {
    bindEditRegionFieldAdminAccess(true);

    $user = User::factory()->create();
    actingAs($user);

    $translation = createEditRegionFieldTranslation([
        'title' => 'Original title',
        'content' => '<p>Original content</p>',
        'meta' => ['seo' => ['description' => 'Original description']],
    ]);
    $payload = editableRegionFieldPayload($translation, 'title');

    Livewire::test('edit-region-field', ['payload' => $payload])
        ->assertSet('label', 'Editable field')
        ->assertSet('type', 'text')
        ->assertSet('data.value', 'Original title')
        ->set('data.value', 'Updated title')
        ->call('save')
        ->assertSet('savedStatus', 'published')
        ->assertDispatched('capell-authoring-saved');

    expect($translation->fresh()?->title)->toBe('Updated title');
});

it('loads meta values and uses textarea controls for non-text editable regions', function (): void {
    bindEditRegionFieldAdminAccess(true);

    $user = User::factory()->create();
    actingAs($user);

    $translation = createEditRegionFieldTranslation([
        'meta' => ['seo' => ['description' => 'Original description']],
    ]);
    $payload = editableRegionFieldPayload($translation, 'meta.seo.description', type: 'textarea');

    Livewire::test('edit-region-field', ['payload' => $payload])
        ->assertSet('type', 'textarea')
        ->assertSet('data.value', 'Original description')
        ->set('data.value', 'Updated description')
        ->call('save')
        ->assertSet('savedStatus', 'published');

    expect($translation->fresh()?->meta)->toHaveKey('seo.description', 'Updated description');
});

it('rejects non-admin users in the livewire editor', function (): void {
    bindEditRegionFieldAdminAccess(false);

    $user = User::factory()->create();
    actingAs($user);

    $translation = createEditRegionFieldTranslation();
    $payload = editableRegionFieldPayload($translation, 'content', type: 'html');

    Livewire::test('edit-region-field', ['payload' => $payload])
        ->assertForbidden();
});

function bindEditRegionFieldAdminAccess(bool $isAdmin): void
{
    app()->instance(AdminAccessCheckerInterface::class, new readonly class($isAdmin) implements AdminAccessCheckerInterface
    {
        public function __construct(private bool $isAdmin) {}

        public function isAdmin(Authenticatable $user): bool
        {
            return $this->isAdmin;
        }
    });
}

/**
 * @param  array<string, mixed>  $attributes
 */
function createEditRegionFieldTranslation(array $attributes = []): Translation
{
    $page = Page::factory()->create();

    return Translation::factory()
        ->translatable($page)
        ->create([
            'title' => 'Original title',
            'content' => '<p>Original content</p>',
            'meta' => ['seo' => ['description' => 'Original description']],
            ...$attributes,
        ]);
}

function editableRegionFieldPayload(Translation $translation, string $field, string $type = 'text'): string
{
    return resolve(EditableRegionSigner::class)->encode(new EditableRegionPayloadData(
        model: Translation::class,
        recordKey: (int) $translation->getKey(),
        field: $field,
        label: 'Editable field',
        type: $type,
        selector: '[data-editable]',
        currentUrl: 'https://example.test/current',
    ));
}
