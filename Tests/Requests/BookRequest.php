<?php

namespace Ikechukwukalu\Requirepin\Tests\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:6|max:100',
            'isbn' => 'required|min:6|max:100|unique:books',
            'authors' => 'required|min:6|max:1000',
            'country' => 'required|max:100',
            'number_of_pages' => 'required|digits_between:1,5',
            'publisher' => 'required|min:6|max:100',
            'release_date' => 'required|date',
        ];
    }
}
