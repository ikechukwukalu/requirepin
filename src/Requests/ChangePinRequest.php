<?php

namespace Ikechukwukalu\Requirepin\Requests;

use Ikechukwukalu\Requirepin\Rules\CurrentPin;
use Ikechukwukalu\Requirepin\Rules\DisallowOldPin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePinRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /**
             * You must allow default pin for RULE `CurrentPin`
             * so that the pin can be changed from the default value.
             * In order to do that we set it to receive a boolean
             * value TRUE as a first parameter
             */
            'current_pin' => ['required', 'string', new CurrentPin(true)],
            'pin' => [
                        'required', 'string',
                        'max:' . config('requirepin.max', 4),
                        Password::min(config('requirepin.min', 4))->numbers(),
                        'confirmed',
                        new DisallowOldPin(
                            config('requirepin.check_all', true),
                            config('requirepin.number', 4)
                        )
                    ],
        ];
    }
}
