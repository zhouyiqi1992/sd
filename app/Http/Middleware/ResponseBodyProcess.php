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
        $response = $next($request);
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept');
        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
        // $response->header('Access-Control-Allow-Credentials', 'true');

        $data = json_decode($response->getContent());
        if (!isset($data) || empty($data)) {
            return $response;
        }
        $data = json_encode($this->objectKeyUnderline2Camel($data));
        $response->setContent($data);
        return $response;
    }

}
