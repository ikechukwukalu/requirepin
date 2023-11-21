<?php

namespace Ikechukwukalu\Requirepin\Rules;

use Ikechukwukalu\Requirepin\Models\OldPin;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DisallowOldPin implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    private int|bool $checkAll;
    private int $number;
    private $user;

    public function __construct($checkAll = true, $number = 4)
    {
        //
        $this->checkAll = $checkAll;
        $this->number = $number;

        if (is_int($this->checkAll) && !empty($this->checkAll)) {
            $this->number = $checkAll;
        }

        $this->user = Auth::guard(config('requirepin.auth_guard', 'web'))->user();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $oldpins = $this->getOldPins();

        if ((string) $value === (string) config('requirepin.default', '0000'))
        {
            return false;
        }

        if ($oldpins->count() === 0) {
            return !Hash::check($value, $this->user->pin);
        }

        foreach ($oldpins as $oldpin) {
            if (Hash::check($value, $oldpin->pin)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans_choice('requirepin::pin.exists',
            intval(is_int($this->checkAll)),
            ['number' => $this->number]);
    }

    /**
     * Get OldPin Model.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getOldPins(): EloquentCollection
    {
        if ($this->checkAll === true) {
            return OldPin::where('user_id', $this->user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
        }

        return OldPin::where('user_id', $this->user->id)
                ->orderBy('created_at', 'desc')
                ->take($this->number)
                ->get();
    }
}
