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
