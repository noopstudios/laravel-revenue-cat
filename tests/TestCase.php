<?php

namespace NoopStudios\LaravelRevenueCat\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use NoopStudios\LaravelRevenueCat\LaravelRevenueCatServiceProvider;
use NoopStudios\LaravelRevenueCat\Tests\Fixtures\User;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelRevenueCatServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        // Use SQLite in memory for testing
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Package configuration
        config()->set('revenue-cat.api.key', 'test-api-key');
        config()->set('revenue-cat.api.project_id', 'test-project-id');
        config()->set('revenue-cat.webhook.secret', 'test-webhook-secret');
        config()->set('revenue-cat.model.user', User::class);
    }

    protected function createCustomer($description = 'fabio'): User
    {
        return User::create([
            'email' => "{$description}@example.com",
            'name' => 'Fábio Ferreira',
        ]);
    }
}
