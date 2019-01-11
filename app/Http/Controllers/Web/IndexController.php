<?php

namespace App\Http\Controllers\Web;

use App\Models\UserBrowse;
use App\Models\UserComment;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    /**
     * 首页城市访问量统计
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $client = new Client();
        $url = env('REQUEST_BASE_URL');
        $response = $client->request('GET', $url.'/province');
        $body = $response->getBody()->getContents();
        $provinceData = json_decode($body,true)['content'];
        $browse = UserBrowse::get();
        $data = [];
        foreach ($provinceData as $value) {
            $data[] = [
                'province' => $value['label'],
                'browseCount' => $browse->where('province_code', $value['value'])->count('id')
            ];
        }
        return $this->success($data);
    }

    /**
     * 首页访问人所在地热度
     * @return \Illuminate\Http\JsonResponse
     */
    public function user()
    {
        /*$client = new Client();
        $url = env('REQUEST_BASE_URL');
        $response = $client->request('GET', $url.'/province');
        $body = $response->getBody()->getContents();
        $provinceData = json_decode($body,true)['content'];
        $data = [];*/
        $userBrowse = UserBrowse::select('province_py', 'id')->get()->groupBy('province_py');
        $data = [];
        foreach ($userBrowse as $k => $value) {
            $data[] = [
                'province' => $k,
                'browseCount' => count($value)
            ];
        }
        return $this->success($data);
    }
}
