<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\HomeRequest;
use App\Models\Home;
use App\Models\HomeDevice;
use App\Models\Pic;
use App\Models\UserComment;
use App\Traits\ProvinceHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    use ProvinceHelper;

    /**
     * 机构列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $size = $request->input('size');
        $home = Home::orderBy('province_code', 'asc')
            ->orderBy('city_code', 'asc')
            ->orderBy('area_code', 'asc')
            ->orderBy('created_at', 'desc');
        $set = [
            ['name', 'like', 'name'],
            ['provinceCode', '=', 'province_code'],
            ['cityCode', '=', 'city_code'],
            ['areaCode', '=', 'area_code'],
            ['type', '=', 'type'],
            ['publicStatus', '=', 'public_status'],
            ['beginAt', '>=', 'created_at'],
            ['endAt', '<', 'created_at'],
            ['property', '=', 'property']
        ];
        $deal = [
            'beginAt' => 'date|0',
            'endAt' => 'date|1'
        ];
        $where = $this->assembleSearchKey($request, $set, $deal);
        if (count($where)) {
            $home->where($where);
        }
        return $this->success($home->paginate($size));
    }

    /**
     * 机构详情
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $home = Home::find($id);
        if (!$home) {
            return $this->failed('未查询到机构信息');
        }
        //机构对应设备
        $devices = HomeDevice::where('hid', $id)->select('id', 'name', 'type')->get();
        $shouldType = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5];
        foreach ($devices as $device) {
            if (in_array($device->type, $shouldType)) {
                unset($shouldType[$device->type]);
            }
        }
        foreach ($shouldType as $type) {
            $devices[] = [
                'type' => $type,
                'name' => ''
            ];
        }
        $devices = collect($devices)->sortBy('type')->values();
        //机构对应评论
        $home->device = $devices;
        $home->comment = UserComment::commentInfo()->where('user_comment.hid', $id)->get();
        $home->picList = Pic::where('rid', $id)->select('url')->get();
        return $this->success($home);
    }

    /**
     * 机构删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $home = Home::find($id);
        if (!$home) {
            return $this->failed('未查询到机构信息');
        }
        if ($home->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->failed('删除失败');
        }
    }

    public function save(HomeRequest $homeRequest)
    {
        $id = $homeRequest->input('id');
        if (isset($id)) {
            //修改
            return $this->homeUpdate($homeRequest);
        } else {
            return $this->homeSave($homeRequest);
        }
    }

    /**
     * 新增
     * @param $homeRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function homeSave($homeRequest)
    {
        if ($homeRequest->errorBag) {
            return $this->failed($homeRequest->errorBag);
        }
        $data = $homeRequest->all();
        $userId = $data['user']['id'];
        unset($data['user'], $data['tokenType']);
        $data = $this->objectKeyCamel2Underline($data);
        if (!($data['lng_bd'] ?? 0) || !($data['lat_bd'] ?? 0)) {
            return $this->failed('请填写经纬度');
        }
        $home = Home::create($data);
        $newId = $home->id;

        if (isset($data['device'])) {
            $this->deviceSave($userId, $newId, $data);
        }

        if (isset($data['pic_list'])) {
            $this->picSave($userId, $newId, $data['picList']);
        }
        if ($home->save()) {
            return $this->success($home);
        }
    }


    /**
     * 更新
     *
     * @param HomeRequest $homeRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function homeUpdate($homeRequest)
    {
        if ($homeRequest->errorBag) {
            return $this->failed($homeRequest->errorBag);
        }
        $data = $homeRequest->all();
        $userId = $data['user']['id'];
        $home = Home::find($homeRequest->input('id'));

        if (isset($data['picList'])) {
            $this->picSave($userId, $data['id'], $data['picList']);
        }

        if (isset($data['device'])) {
            //更新机构设备信息
            $this->deviceSave($userId, $data['id'], $data);
        }

        $data = $this->objectKeyCamel2Underline($data);

        if ((1 == ($data['public_status'] ?? 2)) || (1 == $home->public_status)) {
            if ($homeRequest->has('lngBd') || $homeRequest->has('latBd')) {//传了经纬度
                if (!($data['lng_bd'] ?? 0) || !($data['lat_bd'] ?? 0)) {
                    return $this->failed('该机构经纬度未设置,不能开通');
                }
            } else {  //没传经纬度
                if (!$home->lng_bd || !$home->lat_bd) {
                    return $this->failed('该机构经纬度未设置,不能开通');
                }
            }
        }

        if ($home->update($data)) {
            return $this->success($home);
        } else {
            return $this->failed('更新失败');
        }
    }

    public function deviceSave($userId, $homeId, $data)
    {
        //将之前的机构全部删除
        HomeDevice::where('hid', $homeId)->delete();
        $device = $data['device'];
        //新增机构设备
        $insertData = [];
        $dateTime = Carbon::now()->toDateTimeString();
        foreach ($device as $value) {
            if ($value['name']) {
                $insertData[] = [
                    'add_user_id' => $userId,
                    'created_at' => $dateTime,
                    'hid' => $homeId,
                    'name' => $value['name'],
                    'type' => $value['type'],
                ];
            }
        }
        HomeDevice::insert($insertData);
        return HomeDevice::where('hid', $homeId)->get();
    }


    /**
     * 机构图片保存
     * @param $userId
     * @param $rid
     * @param $url
     * @param int $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function picSave($userId, $rid, $url, $type = 1)
    {
        $home = Home::find($rid);
        if (!$home) {
            return $this->failed('机构不存在');
        }

        DB::transaction(function () use ($rid, $url, $userId, $type) {
            //添加图片之前将之前的图片删除
            $pic = Pic::where('rid', $rid)->where('type', $type)->get();
            $picArray = $pic->map(function ($item) {
                return $item->url;
            })->toArray();
            if ($picArray) {
                //删除数据库数据
                Pic::where('rid', $rid)->where('type', $type)->delete();
                //删除文件
                Storage::delete($picArray);
            }
            $data = [];
            //上传多张图片
            $date = Carbon::now()->toDateTimeString();
            foreach ($url as $k => $value) {
                $data[] = [
                    'add_user_id' => $userId,
                    'url' => $value,
                    'rid' => $rid,
                    'type' => $type,
                    'created_at' => $date
                ];
            }
            Pic::insert($data);
        });

        return $this->success(Pic::where('rid', $rid)->where('type', $type)->get());
    }

    /**
     * 批量冻结/解除冻结机构
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function freeze(Request $request)
    {
        $publicStatus = $request->get('publicStatus');
        $id = $request->get('id');
        //如果是批量冻结，那得看看被冻结的机构是否有对应的banner图
        if ($publicStatus == 2) {
            $home = Home::leftJoin('banner as b', 'home.home_num', '=', 'b.url')
                ->whereIn('home.id', $id)
                ->where('home.public_status', 1)
                ->where('b.status', 1)
                ->select('home.name')
                ->get();
            $bannerName = $this->banner($home);
            if ($bannerName != '') {
                return $this->failed($bannerName . "存在对应轮播图，如需冻结请先下架对应轮播图");
            }
            $result = Home::whereIn('id', $id)->update([
                'public_status' => $publicStatus,
                'update_user_id' => $request->get('user')['id'],
                'updated_at' => Carbon::now()->toDateTimeString()
            ]);
        } else {
            $result = Home::whereIn('id', $id)
                ->where('lng_bd', '<>', 0)
                ->where('lat_bd', '<>', 0)
                ->update([
                    'public_status' => $publicStatus,
                    'update_user_id' => $request->get('user')['id'],
                    'updated_at' => Carbon::now()->toDateTimeString()
                ]);
        }


        if ($result) {
            return $this->success('操作成功');
        } else {
            return $this->failed('已全部成功，无需重复操作');
        }
    }

    /**
     * 全部冻结/解除冻结
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function freezeAll(Request $request)
    {
        $provinceCode = $request->input('provinceCode');
        $cityCode = $request->input('cityCode');
        $areaCode = $request->input('areaCode');
        $status = $request->input('status');
        $where = [];
        if (isset($provinceCode)) {
            $where[] = ['province_code', '=', $provinceCode];
        }

        if (isset($cityCode)) {
            $where[] = ['city_code', '=', $cityCode];
        }

        if (isset($areaCode)) {
            $where[] = ['area_code', '=', $areaCode];
        }
        if ($status == 1) {
            $homeBase = Home::where($where)
                ->where('public_status', 2)
                ->where('lng_bd', '<>', 0)
                ->where('lat_bd', '<>', 0)
                ->update([
                    'public_status' => 1
                ]);
            if ($homeBase) {
                return $this->success('操作成功');
            } else {
                return $this->failed('已全部开通，请勿重复操作');
            }
        } else {
            //冻结之前判断是否有与机构相关联的banner，如果有，则该机构无法冻结
            $banner = Home::leftJoin('banner as b', 'home.home_num', '=', 'b.url')
                ->where($where)
                ->where('home.public_status', 1)
                ->where('b.status', 1)
                ->select('home.name')
                ->get();
            if ($this->banner($banner) != '') {
                return $this->failed($this->banner($banner) . "存在对应轮播图，如需冻结请先下架对应轮播图");
            }
            $homeBase = Home::where($where)->where('public_status', 1)->update([
                'public_status' => 2
            ]);
            if ($homeBase) {
                return $this->success('操作成功');
            } else {
                return $this->failed('已全部冻结，请勿重复操作');
            }
        }

    }

    /**
     * 判断是否有与机构相关联的banner，如果有，则该机构无法冻结
     * @param $home
     * @return string
     */
    public function banner($home)
    {
        //判断是否有与机构相关联的banner，如果有，则该机构无法冻结
        $bannerName = '';
        if ($home) {
            foreach ($home as $value) {
                $bannerName .= $value['name'] . '、';
            }
        }
        if ($bannerName != '') {
            $bannerName = mb_substr($bannerName, 0, mb_strlen($bannerName) - 1);
        }
        return $bannerName;
    }

}
