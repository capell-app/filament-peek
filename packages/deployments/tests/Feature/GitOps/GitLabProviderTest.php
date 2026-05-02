<?php

declare(strict_types=1);

use Capell\Deployments\Data\PullRequestData;
use Capell\Deployments\Data\RepoFile;
use Capell\Deployments\Models\DeploymentConnection;
use Capell\Deployments\Services\GitProvider\GitLabProvider;
use Illuminate\Support\Facades\Http;

it('reads a file from GitLab repo', function (): void {
    $conn = DeploymentConnection::factory()->gitlab()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app',
        'access_token_encrypted' => 'glpat_test',
    ]);

    Http::fake([
        'gitlab.com/api/v4/projects/*/repository/files/*' => Http::response([
            'blob_id' => 'abc123',
            'content' => base64_encode('{"name":"acme/app"}'),
            'encoding' => 'base64',
        ]),
    ]);

    $file = resolve(GitLabProvider::class)->getFile($conn, 'composer.json');

    expect($file)->toBeInstanceOf(RepoFile::class);
    expect($file->content)->toBe('{"name":"acme/app"}');
    expect($file->sha)->toBe('abc123');
});

it('opens a merge request on GitLab', function (): void {
    $conn = DeploymentConnection::factory()->gitlab()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app', 'default_branch' => 'main',
    ]);

    Http::fake([
        'gitlab.com/api/v4/projects/*/merge_requests' => Http::response([
            'iid' => 7,
            'web_url' => 'https://gitlab.com/acme/app/-/merge_requests/7',
            'state' => 'opened',
            'source_branch' => 'capell-install-foo',
            'target_branch' => 'main',
            'sha' => 'def789',
        ]),
    ]);

    $pr = resolve(GitLabProvider::class)->openPullRequest(
        $conn,
        'capell-install-foo',
        'Install capell/foo',
        'Auto-generated.',
    );

    expect($pr)->toBeInstanceOf(PullRequestData::class);
    expect($pr->id)->toBe(7);
    expect($pr->merged)->toBeFalse();
});

it('enables auto-merge on GitLab MR', function (): void {
    $conn = DeploymentConnection::factory()->gitlab()->create([
        'repo_owner' => 'acme', 'repo_name' => 'app',
    ]);

    Http::fake([
        'gitlab.com/api/v4/projects/*/merge_requests/*' => Http::response(['iid' => 7]),
    ]);

    resolve(GitLabProvider::class)->enableAutoMerge($conn, 7);

    Http::assertSent(fn ($req): bool => str_contains((string) $req->body(), 'merge_when_pipeline_succeeds'));
});
