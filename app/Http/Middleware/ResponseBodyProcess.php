<?php

namespace App\Http\Middleware;

use App\Traits\ResHelper;
use Closure;
use Log;

class ResponseBodyProcess
{
    use ResHelper;

    public function handle($request, Closure $next)
    {
        $response =  $next($request);
        $data = json_decode($response->getContent());
        if (!isset($data) || empty($data)) {
            return $response;
        }
        $data = json_encode($this->objectKeyUnderline2Camel($data));
        $response->setContent($data);
        return $response;
    }

}
