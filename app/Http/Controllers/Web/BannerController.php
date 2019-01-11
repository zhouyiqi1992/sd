<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\BannerRequest;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Home;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function list(Request $request)
    {
        $size = $request->input('size');
        $name = $request->input('name');
        $banner = Banner::orderBy('created_at', 'desc');
        if (isset($name)) {
            $banner = $banner->where('name', 'like', "%$name%");
        }
        $banner = $banner->paginate($size);
        return $this->success($banner);
    }

    /**
     * 保存、更新
     * @param BannerRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(BannerRequest $request)
    {
        if ($request->input('id')) {
            return $this->bannerUpdate($request);
        } else {
            return $this->bannerSave($request);
        }
    }

    /**
     * 新增Banner
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bannerSave($request)
    {
        if ($request->errorBag) {
            return $this->failed($request->errorBag);
        }
        $url = $request->input('url');
        $urlType = $request->input('urlType');
        $picUrl = $request->input('picUrl');
        $name = $request->input('name');
        $banner = new Banner();
        $banner->add_user_id = $request->get('user')['id'];
        $banner->name = $name;
        $banner->pic_url = $picUrl;
        $banner->status = 1;
        $banner->url_type = $urlType;
        $banner->url = $url;
        if ($urlType == 2) {
            if (!$url) {
                return $this->failed('机构编号不得为空');
            }
            $home = Home::where('home_num', $request->input('url'))->where('public_status', 1)->first();
            if (!$home) {
                return $this->failed('机构编码不存在或该机构已被冻结');
            }
        }

        $banner->save();
        return $this->success($banner);
    }

    /**
     * 更新Banner
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bannerUpdate($request)
    {
        if ($request->errorBag) {
            return $this->failed($request->errorBag);
        }
        $data = $request->all();
        $banner = Banner::find($request->input('id'));
        if (!$banner) {
            return $this->failed('未查询到轮播图信息');
        }
        $banner->update($this->objectKeyCamel2Underline($data));
        return $this->success($banner);
    }

    /**
     * Banner详情
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return $this->failed('未查询到轮播图信息');
        }
        return $this->success($banner);
    }

    /**
     * 删除Banner
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return $this->failed('未查询到轮播图信息');
        }
        $banner->delete();
        return $this->success('删除成功');
    }
}
