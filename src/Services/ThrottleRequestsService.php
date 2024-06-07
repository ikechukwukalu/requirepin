<?php

namespace Ikechukwukalu\Requirepin\Services;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class ThrottleRequestsService {
    use AuthenticatesUsers;

    public $maxAttempts = 5; // change to the max attempt you want.
    public $decayMinutes = 1;

    public function __construct(int $maxAttempts = 5, int $delayMinutes = 1)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decayMinutes = $delayMinutes;
    }

    public function hasTooManyAttempts (Request $request)
    {
        return $this->hasTooManyLoginAttempts($request);
    }

    public function incrementAttempts (Request $request)
    {
        return $this->incrementLoginAttempts($request);
    }

    public function clearAttempts (Request $request)
    {
        return $this->clearLoginAttempts($request);
    }

    public function _fireLockoutEvent (Request $request)
    {
        return $this->fireLockoutEvent($request);
    }

    public function _limiter ()
    {
        return $this->limiter();
    }

    public function _throttleKey (Request $request)
    {
        return $this->throttleKey($request);
    }
}
