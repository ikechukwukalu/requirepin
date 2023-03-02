<?php

namespace Ikechukwukalu\Requirepin\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

class CommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_fires_require_pin_commands(): void
    {
        $this->artisan('sample:routes')->assertSuccessful();

        $this->artisan('vendor:publish --tag=rp-config')->assertSuccessful();

        $this->artisan('vendor:publish --tag=rp-migrations')->assertSuccessful();

        $this->artisan('vendor:publish --tag=rp-lang')->assertSuccessful();

        $this->artisan('vendor:publish --tag=rp-views')->assertSuccessful();
    }
}
