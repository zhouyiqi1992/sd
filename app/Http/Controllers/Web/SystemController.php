<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\SysUserRequest;
use App\Lib\TokenHelper;
use App\Models\SysUser;
use App\Traits\ResHelper;
use App\Http\Controllers\Controller;
use App\Traits\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    use ResHelper, RoleHelper;

    /**
     * 用户登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $sysUser = SysUser::where('account', $request->input('account'))
            ->where('authstr', $request->input('authstr'))
            ->first();

        if (!empty($sysUser)) {
            if (!$sysUser->status == 2) {
                return $this->failed('无法登录');
            }
            $token = TokenHelper::genToken($sysUser, 'web');
            $roleId = DB::table('sys_user_role_map')->where('suid', $sysUser->id)->value('srid');
            $sysUser->roleId = $roleId;
            $permission = $this->getPermissionByRoleId($roleId);
            if ($sysUser->account === 'admin') {
                $permission['is_admin'] = 1;
            } else {
                $permission['is_admin'] = 0;
            }
            $res = [
                'token' => $token,
                'user' => $sysUser,
                'permissions' => $permission
            ];
            return $this->success($res);
        } else {
            return $this->failed('invalid username or password');
        }
    }

    /**
     * 用户登出
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->get('user');
        if (!$user) {
            return $this->failed('未查询到用户信息');
        }
        TokenHelper::delToken($user, 'web');
        return $this->success('登出成功');
    }

    /**
     * 管理员列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $size = $request->get('size');
        $sysUser = SysUser::sysUserInfo();
        $set = [
            ['account', '=', 'account'],
        ];
        $where = $this->assembleSearchKey($request, $set);
        if (count($where)) {
            $sysUser->where($where);
        }
        $sysUser = $sysUser->paginate($size);
        //隐藏中间五位数
        foreach ($sysUser as $value) {
            $value->account = substr_replace($value->account, '*****', 3, 5);
        }
        return $this->success($sysUser);
    }


    /**
     * 管理员新增/修改
     * @param SysUserRequest $sysUserRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(SysUserRequest $sysUserRequest)
    {
        if ($sysUserRequest->input('id')) {
            return $this->sysUserUpdate($sysUserRequest);
        } else {
            return $this->sysUserSave($sysUserRequest);
        }
    }

    /**
     * 新增管理员
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sysUserSave($request)
    {
        if ($request->errorBag) {
            return $this->failed($request->errorBag);
        }
        $sysUser = new SysUser;
        $sysUser->name = $request->input('name');
        $sysUser->account = $request->input('account');
        $sysUser->authstr = '123456';       //默认密码123456
        $sysUser->add_suid = $request->get('user')['id'];
        DB::transaction(function () use ($sysUser, $request) {
            $sysUser->save();
            DB::table('sys_user_role_map')->insert([
                'add_suid' => $request->get('user')['id'],
                'suid' => $sysUser->id,
                'srid' => $request->input('srid')
            ]);
        });
        return $this->success($sysUser);
    }

    /**
     * 修改管理员
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sysUserUpdate($request)
    {
        if ($request->errorBag) {
            return $this->failed($request->errorBag);
        }
        $data = $request->all();
        $sysUser = SysUser::find($data['id']);
        if (!$sysUser) {
            return $this->failed('未查询到管理员信息');
        }

        DB::transaction(function () use ($sysUser, $data) {
            $sysUser->update($this->objectKeyCamel2Underline($data));

            //修改用户权限
            if (isset($data['srid'])) {
                DB::table('sys_user_role_map')->where('suid', $data['id'])
                    ->update([
                        'srid' => $data['srid']
                    ]);
            }
        });
        return $this->success($sysUser);
    }

    /**
     * 管理员删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $sysUser = SysUser::find($id);
        if (!$sysUser) {
            return $this->failed('管理员信息不存在');
        }
        if ($sysUser->delete()) {
            return $this->success('删除成功');
        } else {
            return $this->failed('删除失败');
        }
    }

    /**
     * 修改密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authstrReset(Request $request)
    {
        $password = $request->input('authstr');
        $confirm = $request->input('confirmAuthstr');
        if ($password !== $confirm) {
            return $this->failed('两次密码必须一致');
        }
        $sysUser = SysUser::find($request->input('id'));

        if (!$sysUser) {
            return $this->failed('管理员不存在');
        }
        if ($request->get('user')['account'] !== 'admin' && $sysUser->account === 'admin') {
            return $this->failed('无法修改超级管理员密码');
        }

        //TODO: 加密处理
        $sysUser->authstr = $password;
        $sysUser->update_suid = $request->get('user')['id'];
        if ($sysUser->save()) {
            return $this->success('更新成功');
        } else {
            return $this->failed('更新失败');
        }
    }


}
