<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Log;

trait ResHelper
{
    public function success($content = '', $message = 'success', $code = 0, $status = 200, array $headers = [], $options = 0, $type = 'json')
    {
        return $this->response($message, $content, $code, $status, $headers, $options, $type);
    }

    public function failed($message, $content = '', $code = 1, $status = 200, array $headers = [], $options = 0, $type = 'json')
    {
        if ($code == 0) {
            Log::warning('--- failed response result can not be 0, use default value 1 instead ---');
            $code = 1;
        }
        return $this->response($message, $content, $code, $status, $headers, $options, $type);
    }

    public function response($message, $content, $code, $status = 200, array $headers = [], $options = 0, $type = 'json')
    {
        switch ($type) {
            case 'json':
                return $this->jsonResponse($message, $content, $code, $status, $headers, $options);
            default:
                return $this->jsonResponse($message, $content, $code, $status, $headers, $options);
        }
    }

    public function jsonResponse($message, $content, $code, $status = 200, array $headers = [], $options = 0)
    {
        $res = array(
            'code' => $code,
            'message' => $message
        );
        if (!empty($content)) {
            $res['content'] = $content;
        }
        $response = [
            'responseBody' => $res,
            'status' => $status,
            'headers' => $headers,
            'option' => $options
        ];
        Log::info('===接口响应信息['.json_encode($response).']===');
        return Response::json($res, $status, $headers, $options);
    }

    public function unAuth()
    {
        return $this->jsonResponse('Token is missing or invalid', '', '401', 401);
    }

    function objectKeyUnderline2Camel ($object, $val2str = true)
    {
        $res = [];
        foreach ($object as $key => $val) {
            if (is_array($val) || is_object($val)) {
                $res[$this->underline2Camel($key)] = $this->objectKeyUnderline2Camel($val);
            } else {
                if ($val2str) {
                    $val = $val === null ? $val : $val . '';
                }
                $res[$this->underline2Camel($key)] = $val;
            }
        }
        return $res;
    }

    function underline2Camel (string $str , $ucfirst = false)
    {
        $str = ucwords(str_replace('_', ' ', $str));
        $str = str_replace(' ', '', lcfirst($str));
        return $ucfirst ? ucfirst($str) : $str;
    }

}
