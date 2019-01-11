<?php

namespace App\Http\Requests\Web;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public $errorBag = '';
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|min:2|max:30',
            'province' => 'nullable|min:2|max:30',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->errorBag = $validator->errors()->first();
    }
}
