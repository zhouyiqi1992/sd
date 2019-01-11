<?php

namespace App\Http\Requests\Web;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HomeRequest extends FormRequest
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
        return [
            'name' => 'nullable|min:2|max:30',
            'type' => 'integer',
            'feeMin' => 'integer',
            'feeMax' => 'integer',
            'payStyle' => 'integer',
            'bedspaceMin' => 'integer',
            'bedspaceMax' => 'integer',
            'buildingAreaMin' => 'integer',
            'buildingAreaMax' => 'integer',
            'homeName' => 'max:30',
            'username' => 'max:30',
            'checkUser' => 'max:30',
            'address' => 'min:2|max:256',
            //'medicalService' => 'min:2|max:30',
            //'assessment' => 'min:2|max:30',
            'intro' => 'min:2|max:200',
            'homeNum' => [
                Rule::unique('home', 'home_num')->ignore($id, 'id')
            ]

        ];
    }

    public function attributes()
    {
        return [
            'name' => '机构名称',
            'type' => '类型',
            'intro' => '简介',
            'homeNum' => '机构编码'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->errorBag = $validator->errors()->first();
    }
}
