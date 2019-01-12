<?php

namespace App\Http\Controllers\Web;

use App\Models\Home;
use App\Models\Pic;
use App\Traits\FileHelper;
use Carbon\Carbon;
use Dotenv\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommonController extends Controller
{
    use FileHelper;

    /**
     * 文件上传
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        return $this->uploadFile($request);
    }


    /**
     * 图片列表
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function list($id)
    {
        if (!Home::find($id)) {
            return $this->failed('任务不存在');
        }
        $list = Pic::where('rid', $id)->get();

        if (!$list) {
            return $this->failed('该机构无图片');
        }

        return $this->success($list);
    }

    /**
     * 删除图片
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $pic = Pic::find($id);
        if (!$pic) {
            return $this->failed('图片不存在');
        }
        if ($pic->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->failed('删除失败');
        }
    }


    public function postUploadPicture(Request $request)
    {
        $file = $request->file('wang-editor-image-file');
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
            $data = [
                'error' => 0,
                'data' => [
                    '/upload/' . $store_result
                ]
            ];
            return $data;
        } else {
            return $this->failed('上传失败');
        }
    }
}
