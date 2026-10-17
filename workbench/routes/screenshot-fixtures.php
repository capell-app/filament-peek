<?php

declare(strict_types=1);

use Capell\Admin\Filament\Resources\Pages\PageResource;
use Capell\Core\Models\Site;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Workbench\App\Actions\AssertScreenshotFixtureEnvironmentAction;

Route::middleware(['web', 'auth'])->get('/screenshot-fixtures/filament-peek/home-page', static function (): RedirectResponse {
    resolve(AssertScreenshotFixtureEnvironmentAction::class)->handle();
    $page = Site::query()->default()->firstOrFail()->pages()
        ->whereHas('blueprint', static fn (Builder $query): Builder => $query->where('key', 'home'))
        ->firstOrFail();
    abort_unless($page->layout_id !== null, 409, 'The screenshot home page needs a renderable layout.');

    return redirect(PageResource::getUrl('edit', ['record' => $page]));
});
