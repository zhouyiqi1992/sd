<?php

namespace App\Lib\Search\SearchVal;

use Illuminate\Support\Carbon;

class Date
{

    protected $val;

    protected $dealType;

    protected $func = [
        0 => 'BDDT', //获取指定日期的这一天的开始时间,并返回时间日期格式,接收值  0
        1 => 'EDDT', //获取指定日期的这一天的结束时间,并返回时间日期格式,接收值  1
        2 => 'BYDT', //获取指定日期的这一年的开始时间,并返回时间日期格式,接收值  2
        3 => 'EYDT', //获取指定日期的这一年的结束时间,并返回时间日期格式,接收值  3

    ];

    public function __construct($val, $dealType)
    {
        $this->val = $val;
        $this->dealType = $dealType;
        $this->make();
    }

    protected function make()
    {
        if (isset($this->func[$this->dealType])) {
            //只有走到这里面来,才对$val用carbon处理
            if (strpos($this->val, '-') === false) {
                $this->val .= '-01'; //strtotime(2013)  并不会返回2013年的时间戳
            }
            $this->val = Carbon::createFromTimestamp(strtotime($this->val));
            $this->{$this->func[$this->dealType]}();
        }
    }

    protected function BDDT()
    {
        $this->val = $this->val->startOfDay()->toDateTimeString();
    }

    protected function EDDT()
    {
        $this->val = $this->val->endOfDay()->toDateTimeString();
    }

    protected function BYDT()
    {
        $this->val = $this->val->startOfYear()->toDateTimeString();
    }

    protected function EYDT()
    {
        $this->val = $this->val->endOfYear()->toDateTimeString();
    }


    /**
     * @return string
     */
    public function getVal()
    {
        return $this->val;
    }
}