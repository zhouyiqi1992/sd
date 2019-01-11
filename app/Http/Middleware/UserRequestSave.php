<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;


class UserRequestSave
{
    public function handle($request, Closure $next)
    {
        $dur = number_format(microtime(true) - LARAVEL_START, 3);
        //记录请求时间、响应时间、访客IP，请求方法、请求Url
        $log = [
            'time' => Carbon::now()->toDateTimeString(),
            'dur' => $dur,
            'clientIp' => $request->getClientIp(),
            'method' => $request->getMethod(),
            'requestUri' => $request->getRequestUri(),
        ];
        if (empty($request->file())) {
            $log['requestBody'] = $request->all();
        }
        Log::info('===用户请求信息['.json_encode($log).']===');
        return $next($request);
    }
}
