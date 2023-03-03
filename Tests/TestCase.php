<?php

namespace Ikechukwukalu\Requirepin\Tests;

use Ikechukwukalu\Requirepin\RequirePinServiceProvider;
use Ikechukwukalu\Requirepin\Controllers\PinController;
use Ikechukwukalu\Requirepin\Controllers\BookController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Stevebauman\Location\LocationServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
      parent::setUp();
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__.'/../src/migrations');
    }

    protected function defineRoutes($router)
    {
        // Define routes.
        $router->get('login', function () {
            return 'login';
        })->name('login');

        $router->get('change/pin', function () {
            return 'changePinView';
        })->name('changePinView');

        $router->post('/test/change/pin', [PinController::class, 'changePin'])
            ->name('changePin');

        $router->post('/test/pin/required/{uuid}', [PinController::class,
            'pinRequired'])->name('pinRequired');

        $router->post('test/v1/sample/books', [BookController::class, 'createBook'])
            ->name('createBookTest')
            ->middleware('require.pin');

        $router->delete('test/v1/sample/books/{id}', [BookController::class, 'deleteBook'])
        ->name('deleteBookTest')
        ->middleware('require.pin');
    }

    protected function getPackageProviders($app): array
    {
        return [RequirePinServiceProvider::class,
                LocationServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app) {
        $app['config']->set('auth.guards.sanctum', [
                        'driver' => 'session',
                        'provider' => 'users',
                    ]);
    }
}
