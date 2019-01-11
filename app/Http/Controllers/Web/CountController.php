<?php

namespace App\Http\Controllers\Web;

use App\Models\UserBrowse;
use App\Models\UserComment;
use App\Traits\TimeHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CountController extends Controller
{
    use TimeHelper;
    /**
     * 浏览统计
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function browse(Request $request)
    {
        //获取开始时间与结束时间
        $date = $request->get('date');
        $startAndEnd = $this->getStartAndEndTime($date);
        //获取指定月份的统计
        $browse = UserBrowse::whereBetween('created_at', [$startAndEnd[0], $startAndEnd[1]])
            ->select(
                DB::raw(
                    "count(DATE_FORMAT(created_at, '%Y-%m-%d')) as browseCount, 
                    DATE_FORMAT(created_at, '%Y-%m-%d') as date"
                )
            )
            ->groupBy(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')")
            )
            ->get();
        $data = [];
        $i = 1;
        $stareAt = strtotime($startAndEnd[0]);
        $endAt = strtotime($startAndEnd[1]);
        while (true){
            $date = Carbon::createFromTimestamp($stareAt)->toDateString();
            $data[$i]['date'] = $date;
            $data[$i]['browseCount'] = $browse->where('date', $date)->sum('browseCount');
            if ($date >= $endAt || $i >= $startAndEnd[2]) {
                break;
            }
            $i++;
            $stareAt += 86400;
        }
        $data = collect($data)->values();
        return $this->success($data);
    }


    /**
     * 评论统计
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function comment(Request $request)
    {
        //获取开始时间与结束时间
        $date = $request->get('date');
        $startAndEnd = $this->getStartAndEndTime($date);
        $comment = UserComment::whereBetween('created_at', [$startAndEnd[0], $startAndEnd[1]])
            ->select(
                DB::raw(
                    "count(DATE_FORMAT(created_at, '%Y-%m-%d')) as commentCount, 
                    DATE_FORMAT(created_at, '%Y-%m-%d') as date"
                )
            )
            ->groupBy(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')")
            )
            ->get();
        $data = [];
        $i = 1;
        $stareAt = strtotime($startAndEnd[0]);
        $endAt = strtotime($startAndEnd[1]);
        while (true){
            $date = Carbon::createFromTimestamp($stareAt)->toDateString();
            $data[$i]['date'] = $date;
            $data[$i]['commentCount'] = $comment->where('date', $date)->sum('commentCount');
            if ($date >= $endAt || $i >= $startAndEnd[2]) {
                break;
            }
            $i++;
            $stareAt += 86400;
        }
        $data = collect($data)->values();
        return $this->success($data);
    }
}
