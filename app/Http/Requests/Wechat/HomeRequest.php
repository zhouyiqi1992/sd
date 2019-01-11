<?php

namespace App\Http\Requests\Wechat;


class HomeRequest extends BaseRequest
{

    public function list($data)
    {
        $this->make($data, [
            'name' => 'nullable|max:20',
            'type' => 'nullable|integer',
            'feeMin' => 'nullable|integer',
            'feeMax' => 'nullable|integer',
            'ageMin' => 'nullable|integer',
            'ageMax' => 'nullable|integer',
            'property' => 'nullable|integer',
            'payStyle' => 'nullable|integer',
            'bedspaceMin' => 'nullable|integer',
            'bedspaceMax' => 'nullable|integer',
            'buildingAreaMin' => 'nullable|integer',
            'buildingAreaMax' => 'nullable|integer',
        ], [
            'name.between' => '要查询的内容最多20个字符',
            'type.integer' => '参数格式错误',
            'feeMin.integer' => '参数格式错误',
            'feeMax.integer' => '参数格式错误',
            'ageMin.integer' => '参数格式错误',
            'ageMax.integer' => '参数格式错误',
            'property.integer' => '参数格式错误',
            'payStyle.integer' => '参数格式错误',
            'bedspaceMin.integer' => '参数格式错误',
            'bedspaceMax.integer' => '参数格式错误',
            'buildingAreaMin.integer' => '参数格式错误',
            'buildingAreaMax.integer' => '参数格式错误',
        ]);
    }

    public function commentSave($data)
    {
        $this->make($data, [
            'rate_star' => 'required|integer|between:1,10',
//            'content' => 'required|max:100',
            'hid' => 'required|integer',
        ], [
//            'content.required' => '请填写要评价的内容',
//            'content.max' => '评价的内容最多100个字符',
            'rate_star.required' => '请选择要评价的星数',
            'rate_star.integer' => '参数格式错误',
            'rate_star.between' => '超出评星范围',
            'hid.required' => '请选择要评价的机构',
            'hid.integer' => '参数格式错误',
        ]);
    }

    public function advice($data)
    {
        $this->make($data, [
            'content' => 'required|max:100',
        ], [
            'content.required' => '请填写要评价的内容',
            'content.max' => '评价的内容最多100个字符',
        ]);
    }
}
