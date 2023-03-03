<?php

namespace Ikechukwukalu\Requirepin;

use Ikechukwukalu\Requirepin\Middleware\RequirePin;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

use Ikechukwukalu\Requirepin\Console\Commands\SampleRoutesCommand;

class RequirePinServiceProvider extends ServiceProvider
{

    public const LANG = __DIR__.'/lang';
    public const DB = __DIR__.'/migrations';
    public const VIEW = __DIR__.'/views';
    public const CONFIG = __DIR__.'/config/requirepin.php';
    public const ROUTE_API = __DIR__.'/routes/api.php';
    public const ROUTE_WEB = __DIR__.'/routes/web.php';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SampleRoutesCommand::class
            ]);
        }

        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('require.pin', RequirePin::class);

        Route::middleware('api')->prefix('api')->group(function () {
            $this->loadRoutesFrom(self::ROUTE_API);
        });

        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(self::ROUTE_WEB);
        });

        $this->loadMigrationsFrom(self::DB);
        $this->loadViewsFrom(self::VIEW, 'requirepin');
        $this->loadTranslationsFrom(self::LANG, 'requirepin');

        $this->publishes([
            self::CONFIG => config_path('requirepin.php'),
        ], 'rp-config');
        $this->publishes([
            self::DB => database_path('migrations'),
        ], 'rp-migrations');
        $this->publishes([
            self::LANG => lang_path('vendor/requirepin'),
        ], 'rp-lang');
        $this->publishes([
            self::VIEW => resource_path('views/vendor/requirepin'),
        ], 'rp-views');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG, 'require-pin'
        );

        $this->app->make(\Ikechukwukalu\Requirepin\Controllers\PinController::class);
        $this->app->make(\Ikechukwukalu\Requirepin\Controllers\BookController::class);
    }
}
