<?php

declare(strict_types=1);

use Capell\Deployments\Http\Controllers\OAuth\BitbucketCallbackController;
use Capell\Deployments\Http\Controllers\OAuth\GitHubCallbackController;
use Capell\Deployments\Http\Controllers\OAuth\GitLabCallbackController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('capell/oauth')->group(function (): void {
    Route::get('/github/callback', GitHubCallbackController::class)->name('capell-deployments.oauth.github');
    Route::get('/gitlab/callback', GitLabCallbackController::class)->name('capell-deployments.oauth.gitlab');
    Route::get('/bitbucket/callback', BitbucketCallbackController::class)->name('capell-deployments.oauth.bitbucket');
});
