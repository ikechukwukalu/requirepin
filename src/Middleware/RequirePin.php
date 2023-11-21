<?php

namespace Ikechukwukalu\Requirepin\Middleware;

use Closure;
use Ikechukwukalu\Requirepin\Facades\PinService;
use Ikechukwukalu\Requirepin\Traits\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequirePin
{
    use Helpers;

    public function handle(Request $request, Closure $next)
    {
        $pinService = new PinService();

        if (!Auth::guard(config('requirepin.auth_guard', 'web'))->check())
        {
            return $this->httpResponse(
                ...$pinService::pinRequestTerminated($request));
        }

        if ($request->has(config('requirepin.param', '_uuid'))
            && $pinService::isArrestedRequestValid($request))
        {
            return $next($request);
        }

        $pinService::cancelAllOpenArrestedRequests();

        return $pinService::requirePinValidationForRequest($request,
            $this->getUserIp($request));
    }

}
