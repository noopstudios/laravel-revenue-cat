<?php

namespace NoopStudios\LaravelRevenueCat\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\File;

class PublishWebhookHandlerCommand extends Command implements Isolatable
{
    protected $signature = 'revenue-cat:publish-webhook-handler';

    protected $description = 'Publish the RevenueCat webhook handler and controller files';

    public function handle(): void
    {
        $handlerPublished = $this->publishWebhookHandler();
        $controllerPublished = $this->publishWebhookController();

        if ($handlerPublished || $controllerPublished) {
            $this->updateConfig();
        }
    }

    protected function publishWebhookHandler(): bool
    {
        $targetPath = app_path('Listeners/HandleRevenueCatWebhook.php');
        $sourcePath = __DIR__.'/../Listeners/HandleRevenueCatWebhook.php';

        if (! File::exists(dirname($targetPath))) {
            File::makeDirectory(dirname($targetPath), 0755, true);
        }

        if (File::exists($targetPath)) {
            if (! $this->confirm('The webhook handler file already exists. Do you want to overwrite it?')) {
                $this->info('Skipping webhook handler publication.');

                return false;
            }
        }

        if (! File::exists($sourcePath)) {
            $this->error('Source webhook handler file not found. Please ensure the package is properly installed.');

            return false;
        }

        $content = File::get($sourcePath);

        $content = str_replace(
            'namespace NoopStudios\LaravelRevenueCat\Listeners;',
            'namespace App\Listeners;',
            $content
        );

        File::put($targetPath, $content);
        $this->info('Webhook handler published successfully!');

        return true;
    }

    protected function publishWebhookController(): bool
    {
        $targetPath = app_path('Http/Controllers/RevenueCat/WebhookController.php');
        $sourcePath = __DIR__.'/../Http/Controllers/WebhookController.php';

        if (! File::exists(dirname($targetPath))) {
            File::makeDirectory(dirname($targetPath), 0755, true);
        }

        if (File::exists($targetPath)) {
            if (! $this->confirm('The webhook controller file already exists. Do you want to overwrite it?')) {
                $this->info('Skipping webhook controller publication.');

                return false;
            }
        }

        if (! File::exists($sourcePath)) {
            $this->error('Source webhook controller file not found. Please ensure the package is properly installed.');

            return false;
        }

        $content = File::get($sourcePath);

        $content = str_replace(
            'namespace NoopStudios\LaravelRevenueCat\Http\Controllers;',
            'namespace App\Http\Controllers\RevenueCat;',
            $content
        );

        File::put($targetPath, $content);
        $this->info('Webhook controller published successfully!');

        return true;
    }

    protected function updateConfig(): void
    {
        $configPath = config_path('revenue-cat.php');

        if (! File::exists($configPath)) {
            $this->warn('Configuration file not found. Please publish the configuration first using:');
            $this->line('php artisan vendor:publish --tag=revenue-cat-config');

            return;
        }

        $config = File::get($configPath);
        $config = str_replace(
            '\\NoopStudios\\LaravelRevenueCat\\Http\\Controllers\\WebhookController::class',
            '\\App\\Http\\Controllers\\RevenueCat\\WebhookController::class',
            $config
        );

        File::put($configPath, $config);
        $this->info('Configuration updated to use the published controller!');

        $this->info('Clearing caches...');
        $this->call('config:clear');
        $this->info('Caches cleared successfully!');
    }
}
