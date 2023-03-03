<?php

namespace Ikechukwukalu\Requirepin\Tests;

use Ikechukwukalu\Requirepin\RequirePinServiceProvider;
use Ikechukwukalu\Requirepin\Middleware\RequirePin;

class ServiceProviderTest extends TestCase
{
    public function test_merges_config(): void
    {
        static::assertSame(
            $this->app->make('files')
                ->getRequire(RequirePinServiceProvider::CONFIG),
            $this->app->make('config')->get('require-pin')
        );
    }

    public function test_loads_translations(): void
    {
        static::assertArrayHasKey('requirepin',
            $this->app->make('translator')->getLoader()->namespaces());
    }

    public function test_publishes_middleware(): void
    {
        $middleware = $this->app->make('router')->getMiddleware();

        static::assertSame(RequirePin::class, $middleware['require.pin']);
        static::assertArrayHasKey('require.pin', $middleware);
    }

}
