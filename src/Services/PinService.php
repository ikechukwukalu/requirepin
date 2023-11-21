<?php

namespace Ikechukwukalu\Requirepin\Services;

use App\Models\User;
use Ikechukwukalu\Requirepin\Models\TestUser;
use Ikechukwukalu\Requirepin\Models\OldPin;
use Ikechukwukalu\Requirepin\Models\RequirePin;
use Ikechukwukalu\Requirepin\Notifications\PinChange;
use Ikechukwukalu\Requirepin\Requests\ChangePinRequest;
use Ikechukwukalu\Requirepin\Rules\CurrentPin;
use Ikechukwukalu\Requirepin\Services\ThrottleRequestsService;
use Ikechukwukalu\Requirepin\Traits\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class PinService {

    use Helpers;

    private Request $arrestedRequest;
    private array $payload;

    public function __construct()
    {
        $this->throttleRequestsService = new ThrottleRequestsService(
            config('requirepin.max_attempts', 3),
            config('requirepin.delay_minutes', 1)
        );
    }

    /**
     * Handle Pin Change.
     *
     * @param \Ikechukwukalu\Requirepin\Requests\ChangePinRequest $request
     *
     * @return null
     * @return array
     */
    public function handlePinChange(ChangePinRequest $request) : ?array
    {
        $validated = $request->validated();

        if ($user = $this->saveNewPin($validated)) {
            $this->saveOldPin($user, $validated);
            $this->sendPinChangeNotification($user);

            return ['message' => trans('requirepin::pin.changed')];
        }

        return null;
    }

    /**
     * Handle Pin Authentication.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function handlePinRequired(Request $request, string $uuid): JsonResponse|Response|RedirectResponse
    {
        if (!$requirePin = $this->getRequirePin($uuid)) {
            $this->transferSessionsToNewRequest($request);

            return $this->shouldResponseBeJson($request)
                ? $this->httpResponse($request,
                    trans('requirepin::general.fail'), 400,
                    ['message' => trans('requirepin::pin.unknown_error')]
                  )
                : redirect($requirePin->redirect_to)->with('return_payload',
                    session('return_payload'));
        }

        $this->throttleRequestsService->clearAttempts($request);

        $this->updateCurrentRequest($request, $requirePin);
        $response = $this->dispatchArrestedRequest($request,
                    $requirePin, $uuid);
        $this->transferSessionsToNewRequest($request);

        $requirePin->approved_at = now();
        $requirePin->save();

        if (session('return_payload')) {
            return redirect($requirePin->redirect_to)->with('return_payload',
                session('return_payload'));
        }

        return $response;
    }

    /**
     * Pin Request Attempts.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return null
     * @return array
     */
    public function pinRequestAttempts(Request $request, string $uuid): ?array
    {
        $response = $this->requestAttempts($request, 'requirepin::pin.throttle');

        if ($response) {
            $requirePin = $this->getRequirePin($uuid);
            $this->checkMaxTrial($requirePin);
        }

        return $response;
    }

    /**
     * Valid Pin URL.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return null
     * @return array
     */
    public function pinUrlHasValidSignature(Request $request): ?array
    {
        if (!$request->hasValidSignature()) {
            return ['message' =>
                trans('requirepin::pin.expired_url')];
        }

        return null;
    }

    /**
     * Valid UUID For Pin URL.
     *
     * @param string $uuid
     *
     * @return null
     * @return array
     */
    public function pinUrlHasValidUUID(string $uuid): ?array
    {
        if(!$this->getRequirePin($uuid)) {
            return ['message' =>
                trans('requirepin::pin.invalid_url')];
        }

        return null;
    }

    /**
     * Pin Validation.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return null
     * @return array
     */
    public function pinValidation(Request $request): ?array
    {
        $validator = Validator::make($request->all(), [
            config('requirepin.input', '_pin') => ['required', 'string',
            new CurrentPin(config('requirepin.allow_default_pin', false))]
        ]);

        if ($validator->fails()) {
            return ['message' => $validator->errors()->first()];
        }

        return null;
    }

    /**
     * Get RequirePin Model.
     *
     * @param string $uuid
     *
     * @return null
     * @return \Ikechukwukalu\Requirepin\Models\RequirePin
     */
    public function getRequirePin(string $uuid): ?RequirePin
    {
        return RequirePin::where('user_id', Auth::guard(config('requirepin.auth_guard', 'web'))->user()->id)
                        ->where('uuid', $uuid)
                        ->whereNull('approved_at')
                        ->whereNull('cancelled_at')
                        ->first();
    }

    /**
     * Error Response For Pin Authentication.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @param int $status_code
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function errorResponseForPinRequired(Request $request, string $uuid, int $status_code, array $data): JsonResponse|RedirectResponse|Response
    {
        if ($this->shouldResponseBeJson($request)) {
            return $this->httpResponse($request,
                trans('requirepin::general.fail'), $status_code, $data);
        }

        $requirePin = $this->getRequirePin($uuid);

        if (isset($requirePin->pin_validation_url)) {
            return back()->with('pin_validation',
                json_encode([$data['message'],
                $requirePin->pin_validation_url, $status_code]));
        }

        return back()->with('pin_validation',
            json_encode([trans('requirepin::pin.unknown_error'),
            'javascript:void(0)', '500']));
    }

    /**
     * Pin Request Terminated.
     *
     * @return array
     */
    public function pinRequestTerminated(Request $request): array
    {
        return [$request, trans('requirepin::general.fail'), 401,
            ['message' => trans('requirepin::pin.terminated')]];
    }

    /**
     * Error Response For Pin Authentication.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function isArrestedRequestValid(Request $request): bool
    {
        $param = config('requirepin.param', '_uuid');
        $requirePin = RequirePin::where('user_id', Auth::guard(config('requirepin.auth_guard', 'web'))->user()->id)
                        ->where('route_arrested', $request->path())
                        ->where('uuid', $request->{$param})
                        ->whereNull('approved_at')
                        ->whereNull('cancelled_at')
                        ->first();

        if (!isset($requirePin->id)) {
            return false;
        }

        return true;
    }

    /**
     * Cancel Unprocessed Arrested Request.
     *
     * @return void
     */
    public function cancelAllOpenArrestedRequests(): void
    {
        RequirePin::where('user_id', Auth::guard(config('requirepin.auth_guard', 'web'))->user()->id)
            ->whereNull('approved_at')
            ->whereNull('cancelled_at')
            ->update(['cancelled_at' => now()]);
    }

    /**
     * Pin Validation For RequirePin Middleware.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $ip
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    public function requirePinValidationForRequest(Request $request, string $ip): JsonResponse|RedirectResponse|Response
    {
        $arrestRouteData = $this->arrestRequest($request, $ip);
        [$status, $status_code, $data] = $this->pinValidationURL(...$arrestRouteData);

        if ($this->shouldResponseBeJson($request))
        {
            return ResponseFacade::json([
                'status' => $status,
                'status_code' => $status_code,
                'data' => $data
            ]);
        }

        return redirect(route('requirePinView'))->with('pin_validation',
            json_encode([$data['message'], $data['url'], $status_code]));
    }

    /**
     * Arrest Request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $ip
     *
     * @return array
     */
    private function arrestRequest(Request $request, string $ip): array
    {
        $redirect_to = url()->previous() ?? '/';
        $uuid = (string) Str::uuid();
        $expires_at = now()->addSeconds(config('requirepin.duration',
            null));
        $pin_validation_url = URL::temporarySignedRoute(
            $this->pinRequiredRoute($request), $expires_at, ['uuid' => $uuid]);

        RequirePin::create([
            "user_id" => Auth::guard(config('requirepin.auth_guard', 'web'))->user()->id,
            "uuid" => $uuid,
            "ip" => $ip,
            "device" => $request->userAgent(),
            "method" => $request->method(),
            "route_arrested" => $request->path(),
            "payload" => Crypt::encryptString(serialize($request->all())),
            "redirect_to" => $redirect_to,
            "pin_validation_url" => $pin_validation_url,
            "expires_at" => $expires_at
        ]);

        return [$pin_validation_url, $redirect_to];
    }

    /**
     * Pin Validation URL.
     *
     * @param string $url
     * @param null|string $redirect
     *
     * @return array
     */
    private function pinValidationURL(string $url, null|string $redirect): array
    {
        return [trans('requirepin::general.success'), 200,
            [
                'message' => trans('requirepin::pin.require_pin'),
                'url' => $url,
                'redirect' => $redirect
            ]];
    }

    /**
     * Dispatch Arrested Request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Ikechukwukalu\Requirepin\Models\RequirePin $requirePin
     * @param string $uuid
     *
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\RedirectResponse
     * @return \Illuminate\Http\Response
     */
    private function dispatchArrestedRequest(Request $request, RequirePin $requirePin, string $uuid): JsonResponse|RedirectResponse|Response
    {
        $this->arrestedRequest = Request::create($requirePin->route_arrested,
                        $requirePin->method, ['_uuid' => $uuid] + $this->payload);

        if ($this->shouldResponseBeJson($request)) {
            $this->arrestedRequest->headers->set('Accept', 'application/json');
        }

        return Route::dispatch($this->arrestedRequest);
    }

    /**
     * Transfer Sessions To New Request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    private function transferSessionsToNewRequest(Request $request): void
    {
        if ($this->shouldResponseBeJson($request))
        {
            return;
        }

        foreach ($this->arrestedRequest->session()->all() as $key => $session) {
            if (!in_array($key, ['_old_input', '_previous', 'errors'])) {
                continue;
            }

            $request->session()->flash($key, $session);
        }
    }

    /**
     * Update Current Request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Ikechukwukalu\Requirepin\Models\RequirePin $requirePin
     *
     * @return void
     */
    private function updateCurrentRequest(Request $request, RequirePin $requirePin): void
    {
        $this->payload = unserialize(Crypt::decryptString($requirePin->payload));

        $request->merge([
            'expires' => null,
            'signature' => null,
            config('requirepin.input', '_pin') => null
        ]);

        foreach($this->payload as $key => $item) {
            $request->merge([$key => $item]);
        }
    }

    /**
     * Save User's New Pin.
     *
     * @param array $validated
     *
     * @return null
     * @return \App\Models\User
     */
    private function saveNewPin(array $validated)
    {
        $user = Auth::guard(config('requirepin.auth_guard', 'web'))->user();
        $user->pin = Hash::make($validated['pin']);
        $user->default_pin = (string) $validated['pin'] === (string) config('requirepin.default', '0000');

        if ($user->save()) {
            return $user;
        }

        return null;
    }

    /**
     * Save User's Old Pin.
     *
     * @param \App\Models\User $user
     * @param array $validated
     *
     * @return void
     */
    private function saveOldPin(User|TestUser $user, array $validated): void
    {
        OldPin::create([
            'user_id' => $user->id,
            'pin' => Hash::make($validated['current_pin'])
        ]);
    }

    /**
     * Send Pin change Notification.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    private function sendPinChangeNotification(User|TestUser $user): void
    {
        if (config('requirepin.notify.change', true)
            && env('APP_ENV') !== 'package_testing') {
            $user->notify(new PinChange());
        }
    }

    /**
     * Max Route Dispatch For Arrested Request.
     *
     * @param \Ikechukwukalu\Requirepin\Models\RequirePin $requirePin
     *
     * @return void
     */
    private function checkMaxTrial(RequirePin $requirePin): void
    {
        $maxTrial = $requirePin->retry + 1;

        if ($maxTrial >= config('requirepin.max_trial', 3)) {
            $requirePin->cancelled_at = now();
        }

        $requirePin->retry = $maxTrial;
        $requirePin->save();
    }
}
