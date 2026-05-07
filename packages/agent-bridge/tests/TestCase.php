<?php

declare(strict_types=1);

namespace Capell\AgentBridge\Tests;

use Capell\AgentBridge\Providers\AgentBridgeServiceProvider;
use Capell\AgentBridge\Tests\Fixtures\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Laravel\Mcp\Server\McpServiceProvider as LaravelMcpServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Relation::morphMap([
            'agent-bridge_user' => User::class,
        ]);
    }

    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        $providers = [
            AgentBridgeServiceProvider::class,
        ];

        if (class_exists(LaravelMcpServiceProvider::class)) {
            array_unshift($providers, LaravelMcpServiceProvider::class);
        }

        return $providers;
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
}
