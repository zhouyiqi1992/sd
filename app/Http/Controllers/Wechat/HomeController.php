<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wechat\HomeRequest;
use App\Models\Banner;
use App\Models\Home;
use App\Models\HomeDevice;
use App\Models\Pic;
use App\Models\UserBrowse;
use App\Models\UserComment;
use App\Traits\DistanceHelper;
use App\Traits\PaginateHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{

    use DistanceHelper, PaginateHelper;

    protected $home;

    protected $validate;

    public function __construct(Home $home, HomeRequest $validate)
    {
        $this->home = $home;
        $this->validate = $validate;
    }


    public function banner()
    {
        $banners = Banner::where('status', 1)
            ->select('pic_url', 'url_type', 'url')
            ->orderBy('id', 'desc')
            ->get();
        return $this->success($banners);
    }

    /**
     * 地图查询
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listall(Request $request)
    {
        return $this->all($request, true);
    }

    /**
     * 列表查询
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $size = $request->get('size', 10);
        $page = $request->get('page', 1);
        return $this->all($request, false, $size, $page);
    }

    /**
     * 机构通用信息
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function basic(Request $request, $id)
    {
        $homeBasic = $this->home->sqlHeader()
            ->where('home.id', $id)
            ->addSelect(
                'home.name',
                'home.phone',
                'home.address',
//                'home.founding_date',
                DB::raw("DATE_FORMAT(home.founding_date, '%Y-%m-%d') as foundingDate"),
                'home.bedspace',
                'home.rate_star'
//            'home.lng_bd',
//            'home.lat_bd'
            )->first();

        if (!$homeBasic) {
            return $this->failed('该机构不存在');
        }

        $lng = $request->get('lngBd', 0);
        $lat = $request->get('latBd', 0);
        $homeBasic->distance = ($lat && $lng) ? $this->getDistance($lng, $lat, $homeBasic->longitude, $homeBasic->latitude) : 0; //有待维护

        $homeBasic->rate_star /= 5;
        $homeBasic->picUrl = Pic::where('rid', $id)->value('url');

        $homeBasic->commentCount = UserComment::getCount($homeBasic->id);
        return $this->success($homeBasic);
    }

    /**
     * 获取机构详细信息
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request, $id)
    {
//        $size = $request->get('size', 10);
        $homeBasic = $this->home->sqlHeader()
            ->where('home.id', $id)
            ->addSelect(
                'home.name',
                'home.phone',
                'home.address',
                'home.bedspace',
//            'home.founding_date',
                'home.province_code',
                DB::raw("DATE_FORMAT(home.founding_date, '%Y-%m-%d') as foundingDate"),
                'home.fee_min',
                'home.fee_max',
                'home.bedspace',
                'home.type',
                'home.address',
                'home.location_bd',
                'home.building_area',
                'home.building_type',
                'home.floors',
                'home.nurse_cnt',
                'home.is_medical_insurance',
                'home.medical_service',
                'home.assessment',
                'home.intro',
                'home.pay_style',
                'home.property',
                'home.age_min',
                'home.age_max',
                'home.url',
                'home.rate_star',
                'home.home_num'
            )
            ->first();

        if (!$homeBasic) {
            return $this->failed('该机构不存在');
        }
        $homeBasic->rate_star /= 5;
        $homeBasic->picUrls = Pic::where('rid', $id)->pluck('url');

        $lng = $request->get('lngBd', 0);
        $lat = $request->get('latBd', 0);
        $homeBasic->distance = ($lat && $lng) ? $this->getDistance($lng, $lat, $homeBasic->longitude, $homeBasic->latitude) : 0; //有待维护

//        $homeBasic->commentCount = UserComment::getCount($id);

//        $homeBasic->comment = UserComment::homeComment($id)
//            ->take($size);

        //记录浏览历史
        $user = $request->get('user');
        UserBrowse::create([
            'created_at' => Carbon::now()->toDateTimeString(),
            'uid' => $user['id'],
            'hid' => $id,
            'province_code' => $homeBasic->province_code,
            'province_py' => $user['province_code'],
            'type' => 1,
            'content' => $homeBasic->name
        ]);
        list($homeBasic->longitude, $homeBasic->latitude) = $this->Convert_BD09_To_GCJ02($homeBasic->latitude, $homeBasic->longitude);
        $homeBasic->homeDevice = HomeDevice::where('hid', $id)
            ->distinct()
            ->pluck('type')
            ->sort()
            ->values();
        return $this->success($homeBasic);
    }

    /**
     * 获取机构的评论列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function commentList(Request $request)
    {
        $id = $request->get('id', 0);
        $size = $request->get('size', 10);
        $list = UserComment::homeComment($id)->paginate($size);
        foreach ($list as &$item) {
            $item->rate /= 5;
        }
        return $this->success($list);
    }

    /**
     * 对机构进行评论
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function commentSave(Request $request)
    {
        $data = $this->objectKeyCamel2Underline($request->all());
        $this->validate->commentSave($data);
        if ($this->validate->flag) {
            return $this->failed($this->validate->msg);
        }
        UserComment::create([
            'uid' => $request->get('user')['id'],
            'hid' => $data['hid'],
            'rate' => $this->userStar($data['rate_star']),
            'rate_date' => Carbon::now()->toDateTimeString()
//            'content' => $data['content'],
//            'check_status' => 1,
        ]);
        $this->setHomeStar($data['hid']);
        return $this->success();
    }

    /**
     * 获取机构设备设施信息
     * 设备设施类型 1.房间设备，2.公共服务设施 3.医疗康复设备，4.安全保障设备，5.消防安全设备
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function device($id)
    {
        $devices = HomeDevice::where('hid', $id)
            ->orderBy('type', 'asc')
            ->select('id', 'name', 'type')
            ->get();
//        $devices = $devices->sortBy('type')->groupBy('type');
//        $res = [];
//        foreach ($devices as $type => $device) {
//            $res[] = [
//                'type' => $type,
//                'device' => $device->map(function ($item) {
//                    return [
//                        'name' => $item->name
//                    ];
//                })
//            ];
//        }
//        if (!count($res)) {
//            $res = [
//                'type' => 0,
//                'device' => [
//
//                ]
//            ];
//        }
        return $this->success($devices);
    }

    /**
     * 通用查询
     *
     *
     * @param $request
     * @param $isMap
     * @param int $size
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     */
    protected function all($request, $isMap, $size = 10, $page = 1)
    {
        $data = $this->objectKeyCamel2Underline($request->all());
        $this->validate->list($data);
        if ($this->validate->flag) {
            return $this->failed($this->validate->msg);
        }
        $set = [
            ['name', 'like', 'home.name'],
            ['city', '=', 'home.city_code', env('DEFAULT_CITY_CODE', '110100'), false],
            ['area', '=', 'home.area_code'],
            ['type', '=', 'home.type'],
            ['ageMin', '>=', 'home.age_max'],
            ['ageMax', '<=', 'home.age_min'],
            ['feeMin', '>=', 'home.fee_max'],
            ['feeMax', '<=', 'home.fee_min'],
            ['payStyle', '=', 'home.pay_style'],
            ['property', '=', 'home.property'],
            ['foundingDateBegin', '>=', 'home.founding_date'],
            ['foundingDateEnd', '<=', 'home.founding_date'],
            ['bedspaceMin', '>=', 'home.bedspace'],
            ['bedspaceMax', '<=', 'home.bedspace'],
            ['buildingAreaMin', '>=', 'home.building_area'],
            ['buildingAreaMax', '<=', 'home.building_area']
        ];
        $deal = [
            'foundingDateBegin' => 'date|2',
            'foundingDateEnd' => 'date|3',
        ];
        $search = $this->assembleSearchKey($request, $set, $deal);
        $homes = $this->home->sqlHeader();
        if (count($search)) {
            $homes->where($search);
        }

        if ($isMap) {
            $homes = $homes->get();
            foreach ($homes as &$home) {
                list($home->longitude, $home->latitude) = $this->Convert_BD09_To_GCJ02($home->latitude, $home->longitude);
            }
        } else {
            $homes = $homes->addSelect(
                'home.name',
                'home.phone',
                'home.address',
//                'home.founding_date',
                DB::raw("DATE_FORMAT(home.founding_date, '%Y-%m-%d') as foundingDate"),
                'home.bedspace',
                'home.lng_bd',
                'home.lat_bd',
                'home.rate_star'
            )
                ->orderBy('home.rate_star', 'rate_star')
                ->orderBy('home.id', 'rate_star')
                ->get();
            $lng = $request->get('lngBd', 0);
            $lat = $request->get('latBd', 0);
            $diatance = ($lat && $lng) ? 1 : 0;
            foreach ($homes as &$home) {
                $home->picUrl = Pic::where('rid', $home->id)->value('url');
                $home->rate_star /= 5;
//                $home->commentCount = UserComment::getCount($home->id);
                $home->distance = $diatance ? $this->getDistance($lng, $lat, $home->longitude, $home->latitude) : 0;
//                $home->distance = 0.99;
            }
            $homes = $this->paginate(collect($homes)->sortBy('distance'), $page, $size);
        }
        return $this->success($homes);
    }

    /**
     * 根据用户的评星生成实际的评分
     *
     * @param $star
     * @return int
     */
    protected function userStar($star)
    {
        $star = (int)$star;
        if ($star <= 1) {
            $star = 1;
        } else if ($star >= 10) {
            $star = 10;
        }
        return ($star *= 5);
    }


    /**
     * 用户评论完毕更新机构的星级
     *
     * @param $id
     */
    protected function setHomeStar($id)
    {
        $stars = UserComment::where('hid', $id)->pluck('rate');
        $count = $stars->count();
        $sum = $stars->sum();
        $homeStar = $count ? round(($sum + 50) / ($count * 5 + 5)) : 10;   //默认10表示5星
        Home::where('id', $id)->update([
            'rate_star' => $homeStar * 5
        ]);
    }
}


