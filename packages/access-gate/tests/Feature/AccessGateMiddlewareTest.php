<?php

declare(strict_types=1);

use Capell\AccessGate\Actions\CreateAccessGateBrowserTokenAction;
use Capell\AccessGate\Actions\CreateAccessGateClaimTokenAction;
use Capell\AccessGate\Actions\CreateAccessGateGrantAction;
use Capell\AccessGate\Contracts\RegistrationField;
use Capell\AccessGate\Data\RegistrationFieldValue;
use Capell\AccessGate\Enums\BrowserTokenStatus;
use Capell\AccessGate\Enums\GrantSubjectType;
use Capell\AccessGate\Enums\IdentityMode;
use Capell\AccessGate\Models\Area;
use Capell\AccessGate\Models\BrowserToken;
use Capell\AccessGate\Models\Grant;
use Capell\AccessGate\Models\Registration;
use Capell\AccessGate\Notifications\AccessRequestReceivedNotification;
use Capell\AccessGate\Support\RegistrationFieldRegistry;
use Capell\AccessGate\Tests\TestCase;
use Illuminate\Foundation\Auth\User as AuthenticatableUser;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

uses(TestCase::class);

it('blocks protected content before the route renders', function (): void {
    $rendered = false;

    Area::factory()->create([
        'key' => 'preview',
        'identity_mode' => IdentityMode::Hybrid,
    ]);

    Route::middleware('access-gate:preview')->get('/access-gate-test/protected', function () use (&$rendered): string {
        $rendered = true;

        return 'secret';
    });

    $this->get('/access-gate-test/protected')
        ->assertRedirect(route('capell-access-gate.request', [
            'area' => 'preview',
            'redirect' => 'http://localhost/access-gate-test/protected',
        ]));

    expect($rendered)->toBeFalse();
});

it('allows guest browser tokens and marks protected responses private', function (): void {
    $area = Area::factory()->create([
        'key' => 'preview',
        'identity_mode' => IdentityMode::Hybrid,
    ]);

    $grant = Grant::factory()->for($area, 'area')->create();
    $issuedToken = app(CreateAccessGateBrowserTokenAction::class)->handle($grant);

    Route::middleware('access-gate:preview')->get('/access-gate-test/guest', fn (): string => 'secret');

    $this
        ->withUnencryptedCookie(config('access-gate.cookies.browser_token.name'), $issuedToken->plainTextToken)
        ->get('/access-gate-test/guest')
        ->assertOk()
        ->assertSee('secret')
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertHeader('Pragma', 'no-cache')
        ->assertHeader('Expires', '0');
});

it('rejects revoked browser tokens', function (): void {
    $area = Area::factory()->create([
        'key' => 'preview',
        'identity_mode' => IdentityMode::Hybrid,
    ]);

    $grant = Grant::factory()->for($area, 'area')->create();
    $issuedToken = app(CreateAccessGateBrowserTokenAction::class)->handle($grant);
    $issuedToken->token->forceFill([
        'status' => BrowserTokenStatus::Revoked,
        'revoked_at' => now(),
    ])->save();

    Route::middleware('access-gate:preview')->get('/access-gate-test/revoked', fn (): string => 'secret');

    $this
        ->withUnencryptedCookie(config('access-gate.cookies.browser_token.name'), $issuedToken->plainTextToken)
        ->get('/access-gate-test/revoked')
        ->assertRedirect(route('capell-access-gate.request', [
            'area' => 'preview',
            'redirect' => 'http://localhost/access-gate-test/revoked',
        ]));
});

it('allows authenticated user grants', function (): void {
    $area = Area::factory()->create([
        'key' => 'preview',
        'identity_mode' => IdentityMode::Authenticated,
    ]);

    $user = new AccessGateTestUser;
    $user->forceFill(['id' => 123]);

    app(CreateAccessGateGrantAction::class)->handle(
        area: $area,
        subjectType: GrantSubjectType::User,
        userId: 123,
    );

    Route::middleware('access-gate:preview')->get('/access-gate-test/authenticated', fn (): string => 'secret');

    $this
        ->actingAs($user)
        ->get('/access-gate-test/authenticated')
        ->assertOk()
        ->assertSee('secret');
});

it('renders and stores configured public request fields', function (): void {
    Notification::fake();

    $area = Area::factory()->create([
        'key' => 'preview',
        'name' => 'Preview',
    ]);

    app(RegistrationFieldRegistry::class)->register(new PublicRequestGithubField);

    $this->get(route('capell-access-gate.request', ['area' => $area->key]))
        ->assertOk()
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertSee('GitHub username');

    $this->post(route('capell-access-gate.request.store', ['area' => $area->key]), [
        'email' => 'mona@example.test',
        'github_username' => 'octocat',
        'requested_url' => 'https://example.test/preview',
    ])->assertRedirect(route('capell-access-gate.request', ['area' => $area->key]));

    $registration = Registration::query()->where('email_normalized', 'mona@example.test')->firstOrFail();

    expect($registration->field_values['github_username']['value'])->toBe('octocat');
    Notification::assertSentOnDemand(AccessRequestReceivedNotification::class);
});

it('claims access with a one-time token and stores the browser token cookie', function (): void {
    $area = Area::factory()->create([
        'claim_url_hosts' => ['example.test'],
    ]);
    $registration = Registration::factory()
        ->for($area, 'area')
        ->create(['requested_url' => 'https://example.test/preview']);
    $grant = Grant::factory()
        ->for($area, 'area')
        ->for($registration, 'registration')
        ->create();
    $issuedClaimToken = app(CreateAccessGateClaimTokenAction::class)->handle($grant);

    $this->get(route('capell-access-gate.claim', ['token' => $issuedClaimToken->plainTextToken]))
        ->assertRedirect('https://example.test/preview')
        ->assertCookie(config('access-gate.cookies.browser_token.name'));
});

it('does not redirect claimed users to untrusted requested urls', function (): void {
    $area = Area::factory()->create([
        'claim_url_hosts' => ['example.test'],
    ]);
    $registration = Registration::factory()
        ->for($area, 'area')
        ->create(['requested_url' => 'https://attacker.test/preview']);
    $grant = Grant::factory()
        ->for($area, 'area')
        ->for($registration, 'registration')
        ->create();
    $issuedClaimToken = app(CreateAccessGateClaimTokenAction::class)->handle($grant);

    $this->get(route('capell-access-gate.claim', ['token' => $issuedClaimToken->plainTextToken]))
        ->assertRedirect(url('/'));
});

it('revokes the local browser token on access gate logout', function (): void {
    $area = Area::factory()->create([
        'key' => 'preview',
    ]);
    $grant = Grant::factory()->for($area, 'area')->create();
    $issuedToken = app(CreateAccessGateBrowserTokenAction::class)->handle($grant);

    $this
        ->withCookie(config('access-gate.cookies.browser_token.name'), $issuedToken->plainTextToken)
        ->post(route('capell-access-gate.logout', ['area' => $area->key]))
        ->assertRedirect(route('capell-access-gate.request', ['area' => $area->key]))
        ->assertCookieExpired(config('access-gate.cookies.browser_token.name'));

    expect(BrowserToken::query()->firstOrFail()->status)->toBe(BrowserTokenStatus::Revoked);
});

final class AccessGateTestUser extends AuthenticatableUser
{
    protected $guarded = [];
}

final class PublicRequestGithubField implements RegistrationField
{
    public function key(): string
    {
        return 'github_username';
    }

    public function label(): string
    {
        return 'GitHub username';
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function validate(array $input): RegistrationFieldValue
    {
        $validated = Validator::make($input, [
            'github_username' => ['required', 'string'],
        ])->validate();

        return new RegistrationFieldValue(
            key: $this->key(),
            value: strtolower((string) $validated['github_username']),
        );
    }
}
