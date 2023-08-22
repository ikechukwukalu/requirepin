<?php

namespace Ikechukwukalu\Requirepin\Facades;

use Illuminate\Support\Facades\Facade;

class PinService extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'PinService';
    }
}
