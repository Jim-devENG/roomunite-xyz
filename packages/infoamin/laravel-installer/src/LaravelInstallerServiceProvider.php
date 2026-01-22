<?php

namespace Infoamin\Installer;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class LaravelInstallerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishFiles();
    }

    /**
     * Bootstrap the application events.
     *
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $path = __DIR__ . '/../';
        $this->loadViewsFrom($path . 'resources/views', 'installer');
        $this->loadTranslationsFrom($path . 'resources/lang', 'installer');
        
        // Load routes if they exist
        if (file_exists(__DIR__ . '/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }
    }

    /**
     * Publish config file for the installer.
     *
     * @return void
     */
    protected function publishFiles()
    {
        $this->publishes([
            __DIR__ . '/../config/installer.php' => config_path('installer.php'),
        ], 'installer_config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/installer'),
        ], 'installer_views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang'),
        ], 'installer_lang');
    }
}

