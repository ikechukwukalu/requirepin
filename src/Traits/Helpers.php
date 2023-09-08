<?php

namespace Ikechukwukalu\Requirepin\Traits;

use Ikechukwukalu\Requirepin\Services\ThrottleRequestsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Stevebauman\Location\Facades\Location;

trait Helpers {

    public ThrottleRequestsService $throttleRequestsService;

    public function __construct()
    {
        $this->throttleRequestsService = new ThrottleRequestsService(
            config('requirepin.login.max_attempts', 3),
            config('requirepin.login.delay_minutes', 1)
        );
    }

    /**
     * HTTP Response.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $status
     * @param int $status_code
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function httpResponse(Request $request, string $status, int $status_code, $data = null): RedirectResponse|JsonResponse|Response
    {
        if ($this->shouldResponseBeJson($request)) {
            return ResponseFacade::json([
                'status' => $status,
                'status_code' => $status_code,
                'data' => $data
            ], $status_code);
        }

        return back()->with('return_payload', json_encode([
            $status, $status_code, $data]));
    }

    /**
     * Get User IP.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function getUserIp(Request $request): string
    {
        if ($position = Location::get()) {
            return $position->ip;
        }

        $server_keys = [
                        'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR',
                        'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP',
                        'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED',
                        'REMOTE_ADDR'
                    ];

        foreach ($server_keys as $key){
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe

                    if (filter_var($ip, FILTER_VALIDATE_IP,
                        FILTER_FLAG_NO_PRIV_RANGE |
                        FILTER_FLAG_NO_RES_RANGE) !== false
                    ) {
                        return $ip;
                    }
                }
            }
        }

        return $request->ip(); // it will return server ip when no client ip found
    }

    /**
     * Unknown Error Response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function unknownErrorResponse(Request $request): RedirectResponse|JsonResponse|Response
    {
        $data = ['message' =>
        trans('requirepin::general.unknown_error')];

        return $this->httpResponse($request,
            trans('requirepin::general.fail'), 422, $data);
    }

    /**
     * HTTP Response.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $trans
     *
     * @return null
     * @return array
     */
    public function requestAttempts(Request $request, string $trans = 'requirepin::auth.throttle'): ?array
    {
        if ($this->throttleRequestsService->hasTooManyAttempts($request)) {
            $this->throttleRequestsService->_fireLockoutEvent($request);

            return ["message" => trans($trans,
                        ['seconds' =>
                            $this->throttleRequestsService->_limiter()
                            ->availableIn(
                                    $this->throttleRequestsService
                                        ->_throttleKey($request)
                                )
                        ])
                    ];
        }

        $this->throttleRequestsService->incrementAttempts($request);

        return null;
    }

    public function shouldResponseBeJson(Request $request): bool
    {
        return $request->wantsJson() || $request->ajax();
    }

    public function pinRequiredRoute(Request $request): string
    {
        $prefix = explode('/', $request->route()->getPrefix())[0];

        if ($prefix === 'api' || $prefix === 'test') {
            return 'pinRequired';
        }

        return 'pinRequiredWeb';
    }

}
