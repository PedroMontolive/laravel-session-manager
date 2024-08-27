<?php

namespace Pemto\SessionManager;

use Illuminate\Support\ServiceProvider;

class SessionManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->offerPublishing();
        $this->registerCommands();
    }

    /**
     * Setup the resource publishing groups for Horizon.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/session-manager.php' => config_path('session-manager.php'),
            ], 'session-manager-config');
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
        }
    }

     /**
     * Setup the configuration for session-manager.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/session-manager.php', 'session-manager'
        );
    }

}
