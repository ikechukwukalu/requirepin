<?php

namespace Ikechukwukalu\Requirepin;

use Config;
use Ikechukwukalu\Requirepin\Services\PinService;
use Ikechukwukalu\Requirepin\Services\ThrottleRequestsService;
use Ikechukwukalu\Requirepin\Middleware\RequirePin;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

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
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('require.pin', RequirePin::class);

        Route::middleware('api')->prefix('api')->group(function () {
            $this->loadRoutesFrom(static::ROUTE_API);
        });

        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(static::ROUTE_WEB);
        });

        $this->loadMigrationsFrom(static::DB);
        $this->loadViewsFrom(static::VIEW, 'requirepin');
        $this->loadTranslationsFrom(static::LANG, 'requirepin');

        $this->publishes([
            static::CONFIG => config_path('requirepin.php'),
        ], 'rp-config');
        $this->publishes([
            static::DB => database_path('migrations'),
        ], 'rp-migrations');
        $this->publishes([
            static::LANG => lang_path('vendor/requirepin'),
        ], 'rp-lang');
        $this->publishes([
            static::VIEW => resource_path('views/vendor/requirepin'),
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
            static::CONFIG, 'require-pin'
        );

        $this->app->make(\Ikechukwukalu\Requirepin\Controllers\PinController::class);

        $this->app->bind(ThrottleRequestsService::class, function (Application $app) {
            return new ThrottleRequestsService(
                config('sanctumauthstarter.login.maxAttempts', 3),
                config('sanctumauthstarter.login.delayMinutes', 1)
            );
        });

        $this->app->bind('PinService', PinService::class);

        $appConfig = Config::get('app');
        $packageFacades = [
            'PinService' => \Ikechukwukalu\Clamavfileupload\Facades\Foundation\PinService::class,
        ];
        $appConfig['aliases'] = array_merge($appConfig['aliases'], $packageFacades);
        Config::set('app', $appConfig);
    }
}
