<?php

namespace App\Http\Requests\Web;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BannerRequest extends FormRequest
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
                'name' => 'min:2|max:301',
                'picUrl' => 'required',
                'urlType' => 'required',
                Rule::unique('Banner')->ignore($id, 'id')
            ];
        } else {
            return [
                'name' => 'min:2|max:301',
                'picUrl' => 'required',
                'urlType' => 'required',
            ];
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $this->errorBag = $validator->errors()->first();
    }
}
