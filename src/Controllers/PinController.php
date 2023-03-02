<?php

namespace Ikechukwukalu\Requirepin\Controllers;

use Ikechukwukalu\Requirepin\Traits\Helpers;
use Ikechukwukalu\Requirepin\Services\PinService;
use Ikechukwukalu\Requirepin\Requests\ChangePinRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PinController extends Controller
{
    use Helpers;

    private PinService $pinService;

    public function __construct()
    {
        $this->pinService = new PinService;
    }

    /**
     * Change Pin.
     *
     * @param \Ikechukwukalu\Requirepin\Requests\ChangePinRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function changePin(ChangePinRequest $request): JsonResponse|RedirectResponse|Response
    {
        if ($data = $this->pinService->handlePinChange($request))
        {
            return $this->httpResponse($request,
                trans('requirepin::general.success'), 200, $data);
        }

        return $this->unknownErrorResponse($request);
    }

    /**
     * Pin Authentication.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function pinRequired(Request $request, string $uuid): JsonResponse|RedirectResponse|Response
    {
        if ($data = $this->pinService->pinRequestAttempts($request)) {
            return $this->pinService->errorResponseForPinRequired($request,
                $uuid, 500, $data);
        }

        if ($data = $this->pinService->pinUrlHasValidSignature($request)) {
            return $this->pinService->errorResponseForPinRequired($request,
                $uuid, 400, $data);
        }

        if ($data = $this->pinService->pinUrlHasValidUUID($uuid)) {
            return $this->pinService->errorResponseForPinRequired($request,
                $uuid, 401, $data);
        }

        if ($data = $this->pinService->pinValidation($request)) {
            return $this->pinService->errorResponseForPinRequired($request,
                $uuid, 400, $data);
        }

        return $this->pinService->handlePinRequired($request, $uuid);
    }

    /**
     * Change Pin View.
     *
     * @return \Illuminate\View\View
     */
    public function changePinView(): View
    {
        return view('requirepin::pin.changepin');
    }

    /**
     * Require Pin View.
     *
     * @return \Illuminate\View\View
     */
    public function requirePinView(): View
    {
        return view('requirepin::pin.pinrequired');
    }
}
