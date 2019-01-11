<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wechat\HomeRequest;
use App\Traits\PaginateHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Http\Request;

class PCAController extends Controller
{
    use PaginateHelper;

    protected $client;

    protected $baseUrl;

    //请求方式默认设置为get
    protected $type = 'GET';

    protected $path = '';

    protected $params = [];

    protected $responseBody;

    protected $reason;

    public function __construct()
    {
        $client = new  Client();
        $this->client = $client;
    }


    /**
     * 经纬度解析成地址
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function location(Request $request)
    {
        $lat = $request->get('lat', 0);
        $lng = $request->get('lng', 0);

        $city = env('DEFAULT_CITY', '北京市');
        $province = env('DEFAULT_PROVINCE', '北京市');

        if ($lat && $lng) {  //目前国内的经纬度没有过0的，所以给了一个这样的判断
            $params = [
                'location' => $lat . ',' . $lng,
                'key' => env('TC_MAP_KEY')
            ];
            $this->baseUrl = 'https://apis.map.qq.com/ws/geocoder/v1?' . http_build_query($params);
            $this->params = [
                'verify' => false
            ];
            $this->client();
            $body = json_decode($this->responseBody);
            if (true !== $this->reason
                || 0 != $body->status
                || !isset($body->result->address_component->city)
                || !($body->result->address_component->city)
            ) {

            } else {
                $city = $body->result->address_component->city;
                $province = $body->result->address_component->province;
            }
        }
        return $this->success([
            'city' => $city,
            'province' => $province,
        ]);
    }

    /**
     * 地址解析成经纬度
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function geoCoder(Request $request)
    {
        $address = $request->get('address', 0);
        $city = $address ?: env('DEFAULT_CITY', '北京市');
        $params = [
            'address' => $city,
            'key' => env('TC_MAP_KEY')
        ];
        $this->baseUrl = 'https://apis.map.qq.com/ws/geocoder/v1?' . http_build_query($params);
        $this->params = [
            'verify' => false
        ];
        $this->client();
        $body = json_decode($this->responseBody);
        if (true !== $this->reason
            || 0 != $body->status
            || !isset($body->result->location)
        ) {
            return $this->failed('获取失败');
        }
        return $this->success($body->result->location);
    }


    protected function client()
    {
        $this->reason = true;
        try {
            $response = $this->client->request(
                $this->type,
                $this->baseUrl . $this->path,
                $this->params
            );

            if (200 != $response->getStatusCode()) {
                $this->reason = (string)$response->getReasonPhrase();
            }
            $this->responseBody = $response->getBody()->getContents();
        } catch (TransferException $exception) {
            $this->reason = 'get failed';
        }
    }
}
