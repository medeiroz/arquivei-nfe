<?php

namespace MedeirosDev\Arquivei\Frameworks\Laravel;

use Illuminate\Support\ServiceProvider;
use MedeirosDev\Arquivei\Arquivei;

class ArquiveiServiceProvider extends ServiceProvider
{
    /**
     * Register our packages services.
     */
    public function register()
    {
        $this->app->bind(Arquivei::class, function () {
            return new Arquivei();
        });
    }

    /**
     * Boot our packages services
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.arquivei.php', 'arquivei');

        $this->publishes([
            __DIR__ . '/config.arquivei.php' => config_path('arquivei.php'),
        ], 'config');
    }

}
