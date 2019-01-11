<?php
/**
 * Created by PhpStorm.
 * User: zhouyiqi
 * Date: 2018/7/13
 * Time: 上午10:53
 */

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait RoleHelper
{

    /**
     * 根据roleId查询用户权限
     * @param $roleId
     * @return array
     */
    public function getPermissionByRoleId($roleId)
    {
        $list = DB::table('sys_role_permission_map as r')
            ->leftJoin('sys_permission as p', 'r.spid', '=', 'p.id')
            ->where('r.srid', $roleId)
            ->select('p.name', 'p.id', 'p.tag')
            ->get();
        $array = [];
        if (!count($list)) {
            $array['menuPermissions'] = [];
            $array['optPermissions'] = [];
        }

        foreach ($list as $k => $value) {
            $date = explode(',', $value->tag);
            if (!$date[1]) { // 我拿到了这个1级菜单
                $array['menuPermissions'][] = $date[0];
                foreach ($list as $k2 => $value2) {
                    $date2 = explode(',', $value2->tag);
                    if ($date[0] == $date2[0] && $date2[1]) {
                        $array['optPermissions'][] = $date[0].','.$date2[1];
                    }
                }
            }
        }
        return $array;
    }
}