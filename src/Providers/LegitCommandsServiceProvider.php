<?php

namespace YOoSlim\LegitArtisanCommands\Providers;

use Illuminate\Support\ServiceProvider;
use YOoSlim\LegitArtisanCommands\Console\Commands\ArtisanAuthenticationCommand;
use YOoSlim\LegitArtisanCommands\Utils\LegitArtisanCommandHandler;

class LegitCommandsServiceProvider extends ServiceProvider
{
	/**
	 * Register any package services.
	 */
	public function register()
	{
		/* */
	}

	/**
	 * Bootstrap any package services.
	 *
	 * @return void
	 */
	public function boot()
	{
        // Publish config file
        $this->publishes([
            __DIR__.'/../../config/legit-artisan-commands.php' => config_path('legit-artisan-commands.php'),
        ]);

		// Load migrations files on app boot
		$this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Load translations files on app boot
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'legit-artisan-commands');

		// Register facades
		$this->app->bind('legitArtisanCommand', function() {
			return new LegitArtisanCommandHandler();
		});

        // Register commands
        if ($this->app->runningInConsole()) {
			$this->commands([
				ArtisanAuthenticationCommand::class,
			]);
		}
	}
}
