<?php

namespace App\Traits;


use App\Lib\Search\Search;

trait SearchKeyHelper
{
    /**
     * 组装查询数据
     * @param $request
     * @param $set array
     *       [
     *          [
     *            0=>'接收的key'  //前端传过来的key
     *            1=>'查询方式'   //处理方式,如'=','>=','like'等
     *            2=>'filed'     //数据库里的字段,存在3,4时必传
     *            3=>'default'   //一个默认值，存在4时必传
     *            4=>'bool'      //对3的一个补充,true表示强制使用3的值,false表示不强制使用,不传时表示强制使用
     *         ]
     *       ]
     * @param $deal array
     *      [
     *        'set里接收的key'=>'处理格式(date)|处理方式(1)'
     *      ]
     * @return array
     */
    public function assembleSearchKey($request, $set, $deal = [])
    {
        $searchKey = new Search($request, $set, $deal);
        return $searchKey->getSearch();
    }
}
