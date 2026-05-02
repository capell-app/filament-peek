<?php

declare(strict_types=1);

use Capell\Deployments\Actions\ConnectDeploymentAction;
use Capell\Deployments\Http\Controllers\OAuth\BitbucketCallbackController;
use Capell\Deployments\Http\Controllers\OAuth\GitHubCallbackController;
use Capell\Deployments\Http\Controllers\OAuth\GitLabCallbackController;

it('ConnectDeploymentAction class exists', function (): void {
    expect(class_exists(ConnectDeploymentAction::class))->toBeTrue();
});

it('GitHub callback controller class exists', function (): void {
    expect(class_exists(GitHubCallbackController::class))->toBeTrue();
});

it('GitLab callback controller class exists', function (): void {
    expect(class_exists(GitLabCallbackController::class))->toBeTrue();
});

it('Bitbucket callback controller class exists', function (): void {
    expect(class_exists(BitbucketCallbackController::class))->toBeTrue();
});
