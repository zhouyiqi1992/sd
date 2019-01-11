<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait FileHelper
{
    /**
     * 文件上传
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile($request)
    {
        $file = $request->file('file');
        $rules = ['file' => 'required'];
        $messages = ['file.required' => '请选择要上传的文件'];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }
        //获取上传文件的大小
        $size = $file->getSize();

        if ($size > env('FILE_MAX_SIZE', 3) * 1024 * 1024) {
            return $this->failed('上传文件不能超过' . env('FILE_MAX_SIZE', 3) . 'M');
        }

        //判断文件是否是通过HTTP POST上传的
        $realPath = $file->getRealPath();

        if (!$realPath) {
            return $this->failed('非法操作');
        }
        //创建以当前日期命名的文件夹
        $today = date('Ymd');
        //上传文件
        $store_result = $file->store($today);
        if ($store_result) {
            return $this->success([
                'url' => '/upload/' . $store_result
            ]);
        } else {
            return $this->failed('上传失败');
        }
    }
}