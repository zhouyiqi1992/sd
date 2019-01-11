<?php

namespace App\Http\Requests\Web;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SysUserRequest extends FormRequest
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
        $id = $this->input('id');
        if (isset($id)) {
            return [
                'account' => 'regex:/^1[345678]{1}\d{9}$/',
                'name' => 'min:2|max:15',
                Rule::unique('sys_user')->ignore($id, 'id')
            ];
        } else {
            return [
                'name' => 'min:2|max:15',
                'account' => 'required|max:16|regex:/^1[345678]{1}\d{9}$/|unique:sys_user'
            ];
        }

    }

    protected function failedValidation(Validator $validator)
    {
        $this->errorBag = $validator->errors()->first();
    }
}
