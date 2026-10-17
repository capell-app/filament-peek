<?php

declare(strict_types=1);

use Capell\Admin\Filament\Resources\Pages\PageResource;
use Capell\Core\Models\Layout;
use Capell\Core\Models\Page;
use Capell\Core\Models\Site;
use Capell\Core\Models\SiteDomain;
use Filament\Facades\Filament;

it('redirects to the default site home page instead of an arbitrary first record', function (): void {
    $previousFlag = getenv('CAPELL_SCREENSHOT_FIXTURE');
    $previousPath = getenv('CAPELL_SCREENSHOT_APP_PATH');
    putenv('CAPELL_SCREENSHOT_FIXTURE=record-state');
    putenv('CAPELL_SCREENSHOT_APP_PATH=' . base_path());
    try {
        require __DIR__ . '/../../workbench/routes/screenshot-fixtures.php';
        $this->actingAs($this->createUserWithRole('super_admin'));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $site = Site::factory()->default()->createOne();
        SiteDomain::factory()->for($site)->createOne(['domain' => 'studio.example']);
        Page::factory()->site($site)->createOne(['name' => 'An unrelated first page']);
        $layout = Layout::factory()->site($site)->createOne();
        $home = Page::factory()->home()->site($site)->layout($layout)->createOne();

        $this->get('/screenshot-fixtures/filament-peek/home-page')
            ->assertRedirect(PageResource::getUrl('edit', ['record' => $home]));
    } finally {
        putenv($previousFlag === false ? 'CAPELL_SCREENSHOT_FIXTURE' : 'CAPELL_SCREENSHOT_FIXTURE=' . $previousFlag);
        putenv($previousPath === false ? 'CAPELL_SCREENSHOT_APP_PATH' : 'CAPELL_SCREENSHOT_APP_PATH=' . $previousPath);
    }
});
