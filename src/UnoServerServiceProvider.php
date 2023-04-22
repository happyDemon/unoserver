<?php

namespace HappyDemon\UnoServer;

use HappyDemon\UnoServer\Commands\UnoServerGenerateCommand;
use Illuminate\Support\ServiceProvider;

class UnoServerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/unoserver.php', 'unoserver');

        // Register the service the package provides.
        $this->app->singleton(UnoServerFactory::class, function ($app) {
            return new UnoServerFactory();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['unoserver'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes(
            [
                __DIR__ . '/../config/unoserver.php' => config_path('unoserver.php'),
            ],
            ['unoserver', 'unoserver.config', 'config']
        );

        $this->publishes(
            [
                __DIR__ . '/../platforms/mac' => base_path('platforms/mac'),
                __DIR__ . '/../platforms/ubuntu' => base_path('platforms/ubuntu'),
                __DIR__ . '/../platforms/sail' => base_path('platforms/sail'),
            ],
            ['unoserver', 'unoserver.platforms', 'platforms']
        );

        // Registering package commands.
        $this->commands([
            UnoServerGenerateCommand::class,
        ]);
    }
}
