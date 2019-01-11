<?php

namespace App\Traits;

trait ReqHelper
{
    function camel2Underline ($str)
    {
        return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $str));
    }

    function objectKeyCamel2Underline ($object)
    {
        $res = [];
        foreach ($object as $key => $val) {
            if (is_array($val) || is_object($val)) {
                $res[$this->camel2Underline($key)] = $this->objectKeyCamel2Underline($val);
            } else {
                $res[$this->camel2Underline($key)] = $val;
            }
        }
        return $res;
    }
}
