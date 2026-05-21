<?php

declare(strict_types=1);

use Capell\Deployments\Data\PullRequestData;
use Capell\Deployments\Data\RepoFile;
use Capell\Deployments\Models\DeploymentConnection;
use Capell\Deployments\Services\GitProvider\BitbucketProvider;
use Illuminate\Support\Facades\Http;

it('reads a file from Bitbucket repo', function (): void {
    $conn = DeploymentConnection::factory()->bitbucket()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app',
        'access_token_encrypted' => 'bb_test_token',
        'default_branch' => 'main',
    ]);

    Http::fake([
        'api.bitbucket.org/2.0/repositories/acme/app/src/*' => Http::response('{"name":"acme/app"}'),
    ]);

    $file = resolve(BitbucketProvider::class)->getFile($conn, 'composer.json');

    expect($file)->toBeInstanceOf(RepoFile::class);
    expect($file->content)->toBe('{"name":"acme/app"}');
});

it('opens a pull request on Bitbucket', function (): void {
    $conn = DeploymentConnection::factory()->bitbucket()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app', 'default_branch' => 'main',
    ]);

    Http::fake([
        'api.bitbucket.org/2.0/repositories/acme/app/pullrequests' => Http::response([
            'id' => 5,
            'links' => ['html' => ['href' => 'https://bitbucket.org/acme/app/pull-requests/5']],
            'state' => 'OPEN',
            'source' => ['branch' => ['name' => 'capell-install-foo'], 'commit' => ['hash' => 'abc123']],
            'destination' => ['branch' => ['name' => 'main']],
        ]),
    ]);

    $pr = resolve(BitbucketProvider::class)->openPullRequest(
        $conn,
        'capell-install-foo',
        'Install capell/foo',
        'Auto-generated.',
    );

    expect($pr)->toBeInstanceOf(PullRequestData::class);
    expect($pr->id)->toBe(5);
    expect($pr->merged)->toBeFalse();
    expect($pr->state)->toBe('open');
});

it('returns success when all pipeline statuses pass', function (): void {
    $conn = DeploymentConnection::factory()->bitbucket()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app',
    ]);

    Http::fake([
        'api.bitbucket.org/2.0/repositories/acme/app/commit/*/statuses' => Http::response([
            'values' => [
                ['state' => 'SUCCESSFUL'],
                ['state' => 'SUCCESSFUL'],
            ],
        ]),
    ]);

    $status = resolve(BitbucketProvider::class)->getDeployStatus($conn, 'sha456');

    expect($status)->toBe('success');
});

it('returns failure or pending from Bitbucket pipeline statuses', function (array $statuses, string $expectedStatus): void {
    $conn = DeploymentConnection::factory()->bitbucket()->create([
        'repo_owner' => 'acme',
        'repo_name' => 'app',
    ]);

    Http::fake([
        'api.bitbucket.org/2.0/repositories/acme/app/commit/*/statuses' => Http::response([
            'values' => $statuses,
        ]),
    ]);

    expect(resolve(BitbucketProvider::class)->getDeployStatus($conn, 'sha456'))->toBe($expectedStatus);
})->with([
    'failed status' => [
        [
            ['state' => 'SUCCESSFUL'],
            ['state' => 'FAILED'],
        ],
        'failure',
    ],
    'in progress status' => [
        [
            ['state' => 'INPROGRESS'],
        ],
        'pending',
    ],
]);

it('creates branches, closes pull requests, and fetches pull request details on Bitbucket', function (): void {
    $conn = DeploymentConnection::factory()->bitbucket()->create([
        'repo_owner' => 'acme',
        'repo_name' => 'app',
        'default_branch' => 'main',
    ]);

    Http::fake([
        'api.bitbucket.org/2.0/repositories/acme/app/refs/branches' => Http::response([], 201),
        'api.bitbucket.org/2.0/repositories/acme/app/pullrequests/5' => Http::response([
            'id' => 5,
            'links' => ['html' => ['href' => 'https://bitbucket.org/acme/app/pull-requests/5']],
            'state' => 'MERGED',
            'source' => ['branch' => ['name' => 'feature/package'], 'commit' => ['hash' => 'head-sha']],
            'destination' => ['branch' => ['name' => 'main']],
        ]),
        'api.bitbucket.org/2.0/repositories/acme/app/pullrequests/5/decline' => Http::response([], 200),
    ]);

    $provider = resolve(BitbucketProvider::class);

    $provider->createBranch($conn, 'feature/package', 'base-sha');

    $pullRequest = $provider->getPullRequest($conn, 5);
    $provider->closePullRequest($conn, 5);

    expect($pullRequest->state)->toBe('merged')
        ->and($pullRequest->merged)->toBeTrue()
        ->and($pullRequest->headSha)->toBe('head-sha');
});

it('commits files to an existing Bitbucket branch', function (): void {
    $conn = DeploymentConnection::factory()->bitbucket()->create([
        'repo_owner' => 'acme',
        'repo_name' => 'app',
    ]);

    Http::fake([
        'api.bitbucket.org/2.0/repositories/acme/app/src' => Http::response([], 201),
        'api.bitbucket.org/2.0/repositories/acme/app/refs/branches/4.x' => Http::response([
            'target' => ['hash' => 'new-commit-sha'],
        ]),
    ]);

    $commitSha = resolve(BitbucketProvider::class)->commitFiles($conn, '4.x', 'Install package', [
        new RepoFile('composer.json', '{"require":{}}'),
    ]);

    expect($commitSha)->toBe('new-commit-sha');

    Http::assertSent(fn ($request): bool => $request->url() === 'https://api.bitbucket.org/2.0/repositories/acme/app/src'
        && str_contains((string) $request->body(), 'Install package'));
});
