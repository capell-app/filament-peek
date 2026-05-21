<?php

declare(strict_types=1);

use Capell\Deployments\Data\PullRequestData;
use Capell\Deployments\Data\RepoFile;
use Capell\Deployments\Models\DeploymentConnection;
use Capell\Deployments\Services\GitProvider\GitHubProvider;
use Illuminate\Support\Facades\Http;

it('reads a file from the repo', function (): void {
    $conn = DeploymentConnection::factory()->github()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app',
        'access_token_encrypted' => 'ghp_test',
    ]);

    Http::fake([
        'api.github.com/repos/acme/app/contents/composer.json' => Http::response([
            'sha' => 'abc123',
            'content' => base64_encode('{"name":"acme/app"}') . "\n",
            'encoding' => 'base64',
        ]),
    ]);

    $file = resolve(GitHubProvider::class)->getFile($conn, 'composer.json');

    expect($file->content)->toBe('{"name":"acme/app"}');
    expect($file->sha)->toBe('abc123');
    expect($file)->toBeInstanceOf(RepoFile::class);
});

it('opens a pull request and returns PullRequestData', function (): void {
    $conn = DeploymentConnection::factory()->github()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app', 'default_branch' => 'main',
    ]);

    Http::fake([
        'api.github.com/repos/acme/app/pulls' => Http::response([
            'number' => 42,
            'html_url' => 'https://github.com/acme/app/pull/42',
            'state' => 'open',
            'head' => ['ref' => 'capell-install-foo', 'sha' => 'def456'],
            'base' => ['ref' => 'main'],
            'merged' => false,
        ]),
    ]);

    $pr = resolve(GitHubProvider::class)->openPullRequest(
        $conn,
        'capell-install-foo',
        'Install capell/foo',
        'Auto-generated.',
    );

    expect($pr)->toBeInstanceOf(PullRequestData::class);
    expect($pr->id)->toBe(42);
    expect($pr->url)->toBe('https://github.com/acme/app/pull/42');
    expect($pr->merged)->toBeFalse();
});

it('enables auto-merge via GraphQL', function (): void {
    $conn = DeploymentConnection::factory()->github()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app',
    ]);

    Http::fake([
        'api.github.com/graphql' => Http::response([
            'data' => [
                'repository' => ['pullRequest' => ['id' => 'PR_abc']],
                'enablePullRequestAutoMerge' => ['pullRequest' => ['number' => 42]],
            ],
        ]),
    ]);

    resolve(GitHubProvider::class)->enableAutoMerge($conn, 42);

    Http::assertSent(fn ($req): bool => str_contains((string) $req->body(), 'enablePullRequestAutoMerge'));
});

it('returns pending when check runs are still in progress', function (): void {
    $conn = DeploymentConnection::factory()->github()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app',
    ]);

    Http::fake([
        'api.github.com/repos/acme/app/commits/sha123/check-runs' => Http::response([
            'check_runs' => [
                ['conclusion' => null, 'status' => 'in_progress'],
                ['conclusion' => 'success', 'status' => 'completed'],
            ],
        ]),
    ]);

    $status = resolve(GitHubProvider::class)->getDeployStatus($conn, 'sha123');

    expect($status)->toBe('pending');
});

it('returns failure or success from GitHub check runs', function (array $checkRuns, string $expectedStatus): void {
    $conn = DeploymentConnection::factory()->github()->create([
        'repo_owner' => 'acme',
        'repo_name' => 'app',
    ]);

    Http::fake([
        'api.github.com/repos/acme/app/commits/*/check-runs' => Http::response([
            'check_runs' => $checkRuns,
        ]),
    ]);

    expect(resolve(GitHubProvider::class)->getDeployStatus($conn, 'sha123'))->toBe($expectedStatus);
})->with([
    'failed check run' => [
        [
            ['conclusion' => 'failure', 'status' => 'completed'],
            ['conclusion' => 'success', 'status' => 'completed'],
        ],
        'failure',
    ],
    'completed check runs' => [
        [
            ['conclusion' => 'success', 'status' => 'completed'],
        ],
        'success',
    ],
]);

it('creates branches, closes pull requests, and fetches pull request details on GitHub', function (): void {
    $conn = DeploymentConnection::factory()->github()->create([
        'repo_owner' => 'acme',
        'repo_name' => 'app',
        'default_branch' => 'main',
    ]);

    Http::fake([
        'api.github.com/repos/acme/app/git/refs' => Http::response([], 201),
        'api.github.com/repos/acme/app/pulls/42' => Http::sequence()
            ->push([
                'number' => 42,
                'html_url' => 'https://github.com/acme/app/pull/42',
                'state' => 'open',
                'head' => ['ref' => 'feature/package', 'sha' => 'head-sha'],
                'base' => ['ref' => 'main'],
                'merged' => false,
            ])
            ->push([], 200),
    ]);

    $provider = resolve(GitHubProvider::class);

    $provider->createBranch($conn, 'feature/package', 'base-sha');
    $pullRequest = $provider->getPullRequest($conn, 42);
    $provider->closePullRequest($conn, 42);

    expect($pullRequest->headBranch)->toBe('feature/package')
        ->and($pullRequest->baseBranch)->toBe('main')
        ->and($pullRequest->headSha)->toBe('head-sha');
});

it('commits files to an existing GitHub branch using git objects', function (): void {
    $conn = DeploymentConnection::factory()->github()->create([
        'repo_owner' => 'acme',
        'repo_name' => 'app',
    ]);

    Http::fake([
        'api.github.com/repos/acme/app/branches/4.x' => Http::response([
            'commit' => [
                'sha' => 'latest-sha',
                'commit' => ['tree' => ['sha' => 'tree-sha']],
            ],
        ]),
        'api.github.com/repos/acme/app/git/blobs' => Http::sequence()
            ->push(['sha' => 'blob-one'])
            ->push(['sha' => 'blob-two']),
        'api.github.com/repos/acme/app/git/trees' => Http::response(['sha' => 'new-tree-sha']),
        'api.github.com/repos/acme/app/git/commits' => Http::response(['sha' => 'new-commit-sha']),
        'api.github.com/repos/acme/app/git/refs/heads/4.x' => Http::response([], 200),
    ]);

    $commitSha = resolve(GitHubProvider::class)->commitFiles($conn, '4.x', 'Install package', [
        new RepoFile('composer.json', '{"require":{}}'),
        new RepoFile('composer.lock', '{}'),
    ]);

    expect($commitSha)->toBe('new-commit-sha');

    Http::assertSent(fn ($request): bool => $request->url() === 'https://api.github.com/repos/acme/app/git/trees'
        && $request['base_tree'] === 'tree-sha'
        && $request['tree'][0]['sha'] === 'blob-one'
        && $request['tree'][1]['sha'] === 'blob-two');
});
