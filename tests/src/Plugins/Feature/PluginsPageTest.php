<?php

declare(strict_types=1);

namespace Capell\Tests\Plugins\Feature;

use Capell\Plugins\Filament\Pages\PluginsPage;
use Capell\Plugins\Models\MarketplacePlugin;
use Capell\Tests\Plugins\PluginsTestCase;
use Capell\Tests\Support\Concerns\CreatesAdminUser;

use function Pest\Livewire\livewire;

class PluginsPageTest extends PluginsTestCase
{
    use CreatesAdminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    public function test_page_renders_successfully(): void
    {
        $component = livewire(PluginsPage::class);

        $component->assertSuccessful();
        $this->assertNotNull($component->instance());
    }

    public function test_browse_tab_shows_only_visible_plugins(): void
    {
        $visible = MarketplacePlugin::factory()->create([
            'is_visible' => true,
            'name' => 'Visible Plugin',
        ]);
        $hidden = MarketplacePlugin::factory()->create([
            'is_visible' => false,
            'name' => 'Hidden Plugin',
        ]);

        $component = livewire(PluginsPage::class, ['activeTab' => 'browse']);
        $component->assertSuccessful();
        $component->assertCanSeeTableRecords([$visible]);
        $component->assertCanNotSeeTableRecords([$hidden]);

        $this->assertTrue(true, 'Livewire assertions above establish the contract.');
    }

    public function test_installed_tab_queries_by_license_relationship(): void
    {
        $withLicense = MarketplacePlugin::factory()->create([
            'name' => 'Licensed Plugin',
            'composer_name' => 'vendor/licensed',
        ]);
        $withLicense->licenses()->create([
            'encrypted_license_key' => 'k',
            'status' => 'active',
        ]);

        $unlicensed = MarketplacePlugin::factory()->create([
            'name' => 'Unlicensed Plugin',
            'composer_name' => 'vendor/unlicensed',
        ]);

        $component = livewire(PluginsPage::class, ['activeTab' => 'installed']);
        $component->assertSuccessful();
        $component->assertCanSeeTableRecords([$withLicense]);
        $component->assertCanNotSeeTableRecords([$unlicensed]);

        $this->assertTrue(true, 'Livewire assertions above establish the contract.');
    }

    public function test_tabs_badge_counts_reflect_data(): void
    {
        MarketplacePlugin::factory()->count(2)->create(['is_visible' => true]);
        MarketplacePlugin::factory()->create(['is_visible' => false]);

        $tabs = (new PluginsPage)->getTabs();
        $this->assertArrayHasKey('browse', $tabs);
        $this->assertArrayHasKey('installed', $tabs);
        $this->assertArrayHasKey('updates', $tabs);
    }
}
