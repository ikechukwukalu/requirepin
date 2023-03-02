<?php

namespace Ikechukwukalu\Requirepin\Middleware;

use Closure;
use Ikechukwukalu\Requirepin\Models\RequirePin as RequirePinModel;
use Ikechukwukalu\Requirepin\Services\PinService;
use Ikechukwukalu\Requirepin\Traits\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;

class RequirePin
{
    use Helpers;

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $this->httpResponse(
                ...PinService::pinRequestTerminated());
        }

        if ($request->has(config('requirepin.param', '_uuid'))
            && PinService::isArrestedRequestValid($request))
        {
            return $next($request);
        }

        PinService::cancelAllOpenArrestedRequests();

        return PinService::requirePinValidationForRequest($request,
            $this->getUserIp($request));
    }

}
