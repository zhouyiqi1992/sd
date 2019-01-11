<?php

namespace App\Http\Controllers\Wechat;

use App\Lib\TokenHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Iwanli\Wxxcx\Wxxcx;

class SystemController extends Controller
{

    /**
     * 微信授权登录登录
     *
     * @param Request $request
     * @param Wxxcx $wxxcx
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(Request $request, Wxxcx $wxxcx, User $user)
    {
        //code 在小程序端使用 wx.login 获取
        $code = $request->get('code', '');
        //encryptedData 和 iv 在小程序端使用 wx.getUserInfo 获取
        $encryptedData = $request->get('encryptedData', '');
        $iv = $request->get('iv', '');

        //根据 code 获取用户 session_key 等信息, 返回用户openid 和 session_key
        $return = $wxxcx->getLoginInfo($code);

        if (!isset($return['openid'])) {
            return $this->failed('登录失败，请稍后再试');
        }

        $userBase = $user->where('appid', $return['openid'])->first();
        //获取解密后的用户信息
        $userInfo = json_decode($wxxcx->getUserInfo($encryptedData, $iv));
        $province = $request->get('province', '北京市');
        if (!$userBase) { //不存在则注册该用户
            $data = [
                'avator_url' => $userInfo->avatarUrl,
                'location' => $userInfo->province ?: 'unknown',
                'sex' => isset($userInfo->gender) ? $userInfo->gender : 0,
                'nickname' => $userInfo->nickName,
                'appid' => $userInfo->openId,
                'province_code' => $province,
            ];
            $userBase = $user->create($data);
        } else {
            $userBase->avator_url = $userInfo->avatarUrl;
            $userBase->location = $userInfo->province ?: 'unknown';/*$userInfo->city*/
            $userBase->sex = isset($userInfo->gender) ? $userInfo->gender : 0;
            $userBase->nickname = $userInfo->nickName;
            $userBase->province_code = $province;
            $userBase->save();
        }
        return $this->success([
            'token' => TokenHelper::genToken($userBase, 'wechat'),
            'system' => [
                'hotline' => env('SYS_HOTLINE', ''),
                'homeUrl' => env('SYS_HOMEURL', '')
            ]
        ]);
    }

    /**
     * 测试用登录接口
     *
     * @param User $user
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function self(User $user, $id)
    {
        return $this->success([
            'token' => TokenHelper::genToken($user->find($id), 'wechat')
        ]);
    }
}
