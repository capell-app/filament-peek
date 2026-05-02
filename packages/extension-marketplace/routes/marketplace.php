<?php

declare(strict_types=1);

use Capell\ExtensionMarketplace\Http\Controllers\MarketplaceChallengeController;
use Illuminate\Support\Facades\Route;

Route::get('/.well-known/capell/marketplace/{challengeId}', MarketplaceChallengeController::class)
    ->where('challengeId', 'chal_[A-Za-z0-9]+')
    ->name('capell-extension-marketplace.marketplace.challenge');
