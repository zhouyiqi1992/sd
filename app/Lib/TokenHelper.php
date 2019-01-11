<?php

namespace App\Lib;

use Illuminate\Support\Facades\Redis;

class TokenHelper
{

    /**
     * @func 登录时生成token
     * @param $user
     * @return string
     */
    public static function genToken($user, $type = '')
    {
        $token = self::uuid();
        $time = time();
        $tokenInfo = [
            'user' => $user,
            'created_at' => $time,
            'invalid_at' => $time + self::getConfig('invalid', $type)
        ];
        self::setTokenInfo($token, $tokenInfo, $type); //token->用户信息
        Redis::hSet(self::getConfig('redisAmlTokenKey', $type), $user->id, $token);//用户->token  防止多端登录
        return $token;
    }

    /**
     * @func 登出时清空token
     * @param $token
     * @param $user
     */
    public static function delToken($user, $type = '')
    {
        $token = Redis::hGet(self::getConfig('redisAmlTokenKey', $type), $user['id']);
        Redis::hDel(self::getConfig('redisTokenKey', $type), $token);
        Redis::hDel(self::getConfig('redisAmlTokenKey', $type), $user['id']);
    }

    /**
     * @func 旧token被映射在宽容token表中
     * @param $token
     * @return string
     */
    public static function tolerant($token, $type = '')
    {
        if (Redis::exists(self::getConfig('redisTolerantTokenKey', $type) . $token)) { //旧token被映射在宽容token表中
            $token = Redis::get(self::getConfig('redisTolerantTokenKey', $type) . $token);
        }
        return $token;
    }


    /**
     * @func  检查token是否有效
     * @param $tokenInfo
     * @param $time
     * @return bool
     */
    public static function checkInvalid($tokenInfo, $time = 0, $type = '')
    {
        if (!$time) {
            $time = time();
        }
        if ($time >= $tokenInfo['invalid_at']) {  //长时间未操作,token失效，自动登出
            TokenHelper::delToken($tokenInfo['user'], $type);
            return true;
        }
        return false;
    }

    /**
     * @func 检查多方登录问题//存在则只保留最后登录的用户
     * @param $user
     * @param $token
     * @return boolean
     */
    public static function forbidMultiparty($user, $token, $type = '')
    {
        $userToken = Redis::hGet(self::getConfig('redisAmlTokenKey', $type), $user['id']);
        if ($token != $userToken && self::getConfig('forbidMultiparty', $type)) {   //该账号在别的地方登陆,登录过期
            Redis::hDel(self::getConfig('redisTokenKey', $type), $token);
            return true;
        }
        return false;
    }

    public static function refreshTokenKey($user, $token, $type = '')
    {
        $key = self::uuid();
        Redis::set(self::getConfig('redisTolerantTokenKey', $type) . $token, $key); //设置宽容对照
        Redis::expire(self::getConfig('redisTolerantTokenKey', $type) . $token, self::getConfig('tolerant', $type)); //设置宽容对照过期时间
        Redis::hDel(self::getConfig('redisTokenKey', $type), $token); //删除原token->user
        Redis::hSet(self::getConfig('redisAmlTokenKey', $type), $user['id'], $key); //设置新的 user->token
        return $key;
    }

    /**
     * 设置token信息
     * @param $token
     * @param $tokenInfo
     */
    public static function setTokenInfo($token, $tokenInfo, $type = '')
    {
        Redis::hSet(self::getConfig('redisTokenKey', $type), $token, json_encode($tokenInfo)); //token->用户信息
    }

    /**
     * @func 获取token里的信息
     * @param $token
     * @return array
     */
    public static function getTokenInfo($token, $type = '')
    {
        return json_decode(Redis::hGet(self::getConfig('redisTokenKey', $type), $token), true);
    }

    /**
     * @func 获取唯一key
     * @return string
     */
    public static function uuid()
    {
        return md5(uniqid(mt_rand(), true));
    }

    public static function getConfig($key, $type = '')
    {
        if (empty($type)) {
            return config('token.' . $key);
        }
        $val = config('token.' . $type . '.' . $key);
        if ($val === null) {
            return config('token.' . $key);
        }
        return $val;
    }

}
