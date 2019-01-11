<?php

namespace App\Http\Middleware;

use App\Lib\TokenHelper;
use App\Traits\ResHelper;
use Closure;

class Token
{
    use ResHelper;

    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $tokenType = '')
    {
        $token = $request->header('token');
        if (empty($token)) {
            return $this->unAuth();
        }

        $token = TokenHelper::tolerant($token, $tokenType);                                 //旧token被映射在宽容token表中
        $tokenInfo = TokenHelper::getTokenInfo($token, $tokenType);
        $time = time();
        if (
            empty($tokenInfo) ||                                                //找不到token里的信息
            TokenHelper::checkInvalid($tokenInfo, $time, $tokenType) ||                     //检查是否有效
            TokenHelper::forbidMultiparty($tokenInfo['user'], $token, $tokenType)           //检查是否被人挤下线
        ) {
            return $this->unAuth();
        }

        //token是否需要刷新
        if ($time > $tokenInfo['created_at'] + TokenHelper::getConfig('after' ,$tokenType)) {
            $token = TokenHelper::refreshTokenKey($tokenInfo['user'], $token, $tokenType);
            $tokenInfo['created_at'] = $time;
            header('token:' . $token);
        }

        //重设token有效时间
        $tokenInfo['invalid_at'] = $time + TokenHelper::getConfig('invalid' ,$tokenType);
        TokenHelper::setTokenInfo($token, $tokenInfo, $tokenType);
        $request->merge([
            'user' => $tokenInfo['user'],
            'tokenType' => $tokenType
        ]);
        return $next($request);
    }
}
