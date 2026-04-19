<?php

declare(strict_types=1);

namespace Capell\Tests\Plugins;

use Capell\Plugins\Actions\InstallPluginAction;
use Capell\Plugins\Enums\CapabilityWarningLevel;
use Capell\Plugins\Enums\LicenseModel;
use Capell\Plugins\Enums\PluginKind;
use Capell\Plugins\Models\MarketplacePlugin;
use Capell\Plugins\Services\AnystackClient;
use Capell\Plugins\Services\ComposerRunner;
use Capell\Tests\Plugins\Unit\StubComposerProcess;
use RuntimeException;
use Symfony\Component\Process\Process;

class InstallPluginActionTest extends PluginsTestCase
{
    /**
     * @var array<int, array<int, string>>
     */
    private array $captured = [];

    /**
     * @var array<int, int>
     */
    private array $exitCodes = [];

    /**
     * @var array<int, string>
     */
    private array $errorOutputs = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->captured = [];
        $this->exitCodes = [];
        $this->errorOutputs = [];
    }

    public function test_install_free_plugin_succeeds(): void
    {
        $plugin = $this->makePlugin([
            'composer_name' => 'vendor/free-plugin',
        ]);

        $action = new InstallPluginAction(
            $this->makeComposerRunner([0]),
            $this->makeAnystackClient(),
        );

        $action->handle($plugin);

        $this->assertTrue($plugin->auditLog()->where('action', 'installed')->exists());
        $this->assertCount(1, $this->captured);
        $this->assertEquals(
            ['composer', 'require', '--no-interaction', '--update-with-all-dependencies', 'vendor/free-plugin:1.0.0'],
            $this->captured[0],
        );
    }

    public function test_install_paid_plugin_with_valid_license_uses_anystack_product_id(): void
    {
        $plugin = $this->makePlugin([
            'composer_name' => 'vendor/paid-plugin',
            'anystack_product_id' => 'prod_xyz',
            'price_once' => 99,
        ]);

        config()->set('capell-plugins.anystack.composer_contact_email', 'unlock');

        $action = new InstallPluginAction(
            $this->makeComposerRunner([0, 0, 0]),
            $this->makeAnystackClient(),
        );

        $action->handle($plugin, 'license_key_123');

        $this->assertTrue($plugin->auditLog()->where('action', 'installed')->exists());
        $this->assertCount(3, $this->captured, 'expected auth + repo + require commands');
        $this->assertContains('http-basic.prod_xyz.composer.sh', $this->captured[0]);
        $this->assertContains('license_key_123', $this->captured[0]);
        $this->assertContains('repositories.anystack-prod_xyz', $this->captured[1]);
    }

    public function test_install_paid_plugin_passes_fingerprint_to_composer(): void
    {
        $plugin = $this->makePlugin([
            'composer_name' => 'vendor/paid-plugin',
            'anystack_product_id' => 'prod_xyz',
            'price_once' => 99,
        ]);

        $action = new InstallPluginAction(
            $this->makeComposerRunner([0, 0, 0]),
            $this->makeAnystackClient(),
        );

        $action->handle($plugin, 'lkey', 'fp123');

        $this->assertContains('lkey:fp123', $this->captured[0]);
    }

    public function test_install_paid_plugin_without_license_key_throws(): void
    {
        $plugin = $this->makePlugin([
            'composer_name' => 'vendor/paid-plugin',
            'price_once' => 99,
        ]);

        $action = new InstallPluginAction(
            $this->makeComposerRunner([]),
            $this->makeAnystackClient(),
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot install paid plugin without license key');

        $action->handle($plugin);
    }

    public function test_composer_config_failure_throws(): void
    {
        $plugin = $this->makePlugin([
            'composer_name' => 'vendor/paid-plugin',
            'anystack_product_id' => 'prod_xyz',
            'price_once' => 99,
        ]);

        $action = new InstallPluginAction(
            $this->makeComposerRunner([1], ['Auth failed']),
            $this->makeAnystackClient(),
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to configure Anystack repository');

        $action->handle($plugin, 'invalid_key');
    }

    public function test_composer_install_failure_logs_and_throws(): void
    {
        $plugin = $this->makePlugin([
            'composer_name' => 'vendor/plugin',
        ]);

        $action = new InstallPluginAction(
            $this->makeComposerRunner([1], ['Package not found error']),
            $this->makeAnystackClient(),
        );

        $this->expectException(RuntimeException::class);

        try {
            $action->handle($plugin);
        } finally {
            $this->assertTrue($plugin->auditLog()->where('action', 'install_failed')->exists());
        }
    }

    public function test_preview_capability_warnings_returns_correct_highest_level(): void
    {
        $plugin = $this->makePlugin([
            'composer_name' => 'vendor/plugin',
            'capabilities' => ['db_schema_changes', 'http_outbound:capell.app', 'reads_secrets'],
        ]);

        $action = new InstallPluginAction(
            $this->makeComposerRunner([]),
            $this->makeAnystackClient(),
        );

        $warnings = $action->previewCapabilityWarnings($plugin);

        $this->assertEquals(CapabilityWarningLevel::Red, $warnings->highestLevel);
        $this->assertCount(3, $warnings->warnings);
    }

    public function test_preview_capability_warnings_empty_returns_green(): void
    {
        $plugin = $this->makePlugin([
            'composer_name' => 'vendor/plugin',
            'capabilities' => [],
        ]);

        $action = new InstallPluginAction(
            $this->makeComposerRunner([]),
            $this->makeAnystackClient(),
        );

        $warnings = $action->previewCapabilityWarnings($plugin);

        $this->assertEquals(CapabilityWarningLevel::Green, $warnings->highestLevel);
        $this->assertCount(0, $warnings->warnings);
    }

    /**
     * @param  array<int, int>  $exitCodes
     * @param  array<int, string>  $errorOutputs
     */
    private function makeComposerRunner(array $exitCodes, array $errorOutputs = []): ComposerRunner
    {
        $this->exitCodes = $exitCodes;
        $this->errorOutputs = $errorOutputs;

        return new ComposerRunner(
            binary: 'composer',
            timeoutSeconds: 30,
            workingDirectory: sys_get_temp_dir(),
            processFactory: function (array $command, string $cwd, int $timeout): Process {
                $this->captured[] = $command;
                $exitCode = array_shift($this->exitCodes) ?? 0;
                $errorOutput = array_shift($this->errorOutputs) ?? '';

                return StubComposerProcess::make($exitCode, '', $errorOutput);
            },
        );
    }

    private function makeAnystackClient(): AnystackClient
    {
        return new AnystackClient(
            baseUrl: 'https://api.anystack.sh',
            apiKey: null,
            timeoutSeconds: 5,
        );
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makePlugin(array $overrides = []): MarketplacePlugin
    {
        return MarketplacePlugin::create(array_merge([
            'name' => 'Test Plugin',
            'slug' => 'test-plugin',
            'description' => 'Test description',
            'composer_name' => 'vendor/plugin',
            'vendor' => 'vendor',
            'kind' => PluginKind::Full,
            'license_model' => LicenseModel::Free,
            'latest_version' => '1.0.0',
        ], $overrides));
    }
}
