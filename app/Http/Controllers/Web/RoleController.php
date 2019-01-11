<?php

namespace App\Http\Controllers\Web;

use App\Models\Permission;
use App\Models\Role;
use App\Traits\ResHelper;
use App\Traits\RoleHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    use ResHelper, RoleHelper;

    /**
     * 角色列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $name = $request->input('name');
        $list = Role::orderBy('updated_at', 'desc');
        if (isset($name)) {
            $list = $list->where('name', 'like', "%$name%");
        }
        $list = $list->get();
        return $this->success($list);
    }

    /**
     * 新增角色/修改角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $data = $request->all();
        if (isset($data['id'])) {
            //修改操作
            $role = Role::find($data['id']);
            $data['update_suid'] = $data['user']['id'];
            $result = $role->update($this->objectKeyUnderline2Camel($data));
            if ($result) {
                return $this->success($role);
            }
        }
        //新增操作
        $data['add_suid'] = $data['user']['id'];
        $role = new Role;
        $result = $role->create($data);
        if ($result) {
            return $this->success($result);
        }
    }

    /**
     * 删除角色
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return $this->failed('角色不存在');
        }
        $result = $role->delete();
        if ($result) {
            return $this->success('删除成功');
        } else {
            return $this->failed('删除失败');
        }
    }

    /**
     * 添加权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function permissionSave(Request $request)
    {
        $permissionIds = $request->input('menuPermissions');
        $optPermissions = $request->input('optPermissions');
        $roleId = $request->input('id');
        $addUserId = $request->get('user')['id'];
        DB::table('sys_role_permission_map')->where('srid', $roleId)->delete();
        $id = [];
        if (isset($permissionIds)) {
            foreach ($permissionIds as $value) {
                $tag = $value . ',0';
                $permissionId = Permission::where('tag', $tag)->value('id');
                if ($permissionId) {
                    $id[] = $permissionId;
                }
            }
        }

        if (isset($optPermissions)) {
            foreach ($optPermissions as $value) {
                $optPermissionId = Permission::where('tag', $value)->value('id');
                if ($optPermissionId) {
                    $id[] = $optPermissionId;
                }
            }
        }
        $insert = [];
        $now = Carbon::now()->toDateTimeString();

        foreach ($id as $v) {
            $insert[] = [
                'created_at' => $now,
                'add_suid' => $addUserId,
                'srid' => $roleId,
                'spid' => $v
            ];
        }
        DB::table('sys_role_permission_map')->insert($insert);
        return $this->success();
    }

    /**
     * 权限列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function permissionListAll()
    {
        $permission = Permission::get();
        $array = [];
        $i = 0;
        foreach ($permission as $k => $value) {
            $date = explode(',', $value->tag);
            if (!$date[1]) { // 我拿到了这个1级菜单
                $array[$i]['name'] = $value->name;
                $array[$i]['menuPermission'] = $date[0];
                foreach ($permission as $k2 => $value2) {
                    $date2 = explode(',', $value2->tag);
                    if ($date[0] == $date2[0] && $date2[1]) {
                        $array[$i]['optPermissions'][] = [
                            'name' => $value2->name,
                            'optPermission' => $date2[1]
                        ];
                    }
                }
                $i++;
            }
        }
        return $this->success($array);
    }


    /**
     * 单个角色所有权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function permissionList(Request $request)
    {
        $roleId = $request->input('srid');
        $permission = $this->getPermissionByRoleId($roleId);
        return $this->success($permission);
    }

}
