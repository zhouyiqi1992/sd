<?php

namespace App\Http\Requests\Wechat;

use Illuminate\Support\Facades\Validator;

class BaseRequest
{
    //验证是否通过
    public $flag;

    //错误返回信息
    public $msg = '';

    /**
     * 统一验证
     *
     * @param $data //要验证的数据
     * @param $rule //验证规则
     */
    public function make($data, $rule, $msg)
    {
        $validator = Validator::make($data, $rule, $msg);
        $this->msg = $validator->errors()->first();
        $this->flag = $validator->fails();
    }
}
