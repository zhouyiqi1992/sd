<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\UserRequest;
use App\Models\User;
use App\Models\UserAdvice;
use App\Models\UserBrowse;
use App\Models\UserComment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * 用户列表
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(UserRequest $request)
    {
        $size = $request->input('size');
        $set = [
            ['name', 'like', 'nickname'],
            ['province', 'like', 'location']
        ];
        $user = User::orderBy('created_at', 'desc');
        $search = $this->assembleSearchKey($request, $set);
        if (count($search)) {
            $user->where($search);
        }
        return $this->success($user->paginate($size));
    }


    /**
     * 用户访问日志
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function browseList(Request $request)
    {
        $uid = $request->input('uid');
        $size = $request->input('size');
        $browse = UserBrowse::where('uid', $uid)
            ->select('content as homeName', 'created_at')
            ->orderBy('created_at', 'desc');
        $set = [
            ['beginAt', '>=', 'user_browse_log.created_at'],
            ['endAt', '<', 'user_browse_log.created_at']
        ];
        $deal = [
            'beginAt' => 'date|0',
            'endAt' => 'date|1'
        ];
        $where = $this->assembleSearchKey($request, $set, $deal);
        if (count($where)) {
            $browse->where($where);
        }
        return $this->success($browse->paginate($size));
    }


    /**
     * 用户留言列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adviceList(Request $request)
    {
        $size = $request->input('size');
        $advice = UserAdvice::orderBy('created_at', 'desc');
        $set = [
            ['name', 'like', 'nickname'],
            ['beginAt', '>=', 'created_at'],
            ['endAt', '<', 'created_at']
        ];
        $deal = [
            'beginAt' => 'date|0',
            'endAt' => 'date|1'
        ];
        $where = $this->assembleSearchKey($request, $set, $deal);

        if (count($where)) {
            $advice->where($where);
        }
        return $this->success($advice->paginate($size));
    }

    /**
     * 用户评论列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function commentList(Request $request)
    {
        $size = $request->input('size');
        $comment = UserComment::commentInfo();
        $set = [
            ['homeName', 'like', 'h.name'],
            ['checkStatus', '=', 'user_comment.check_status'],
            ['checkUser', 'like', 'su.name'],
            ['userName', 'like', 'u.nickname'],
            ['beginAt', '>=', 'user_comment.created_at'],
            ['endAt', '<', 'user_comment.created_at']
        ];
        $deal = [
            'beginAt' => 'date|0',
            'endAt' => 'date|1'
        ];
        $where = $this->assembleSearchKey($request, $set, $deal);
        if (count($where)) {
            $comment->where($where);
        }
        return $this->success($comment->paginate($size));
    }

    /**
     * 用户评论审核
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function commentCheck(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('checkStatus');
        $reason = $request->input('checkReason');
        $comment = UserComment::find($id);
        if (!$comment) {
            return $this->failed('未查询到评论信息');
        }
        $comment->check_status = $status;
        $comment->check_reason = $reason;
        //增加机构评论数
        if ($status == 2) {
            UserComment::countIncrement($comment->hid);
        }
        $comment->auid = $request->get('user')['id'];
        if ($comment->save()) {
            return $this->success($comment);
        }
    }

}
