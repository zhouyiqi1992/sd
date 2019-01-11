<?php
/**
 * Created by PhpStorm.
 * User: zhouyiqi
 * Date: 2018/7/13
 * Time: 上午10:53
 */

namespace App\Traits;


trait PaginateHelper
{

    /**
     * 对给定的数据集合仿照laravel进行分页
     *
     * @param $data
     * @param $page
     * @param $size
     * @return array
     */
    public function paginate($data, $page = 1, $size = 10)
    {
        $data = collect($data);
        $total = $data->count();
        $size = $size ? $size : 10;
        $lastPage = ceil(1.00 * $total / $size);
        $page = $page ? $page > $lastPage ? $lastPage : $page : 1;
        $res = [
            'current_page' => $page,
            'data' => $data->slice(($page - 1) * $size, $size)->values(),
            'last_page' => $lastPage,
            'per_page' => $size,
            'total' => $total
        ];
        return $res;
    }
}