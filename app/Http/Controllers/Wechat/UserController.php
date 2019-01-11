<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Requests\Wechat\HomeRequest;
use App\Http\Controllers\Controller;
use App\Models\UserAdvice;
use App\Models\UserComment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserController extends Controller
{

    /**
     * 我的页面用户信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        $count = UserComment::where('uid', $request->get('user')['id'])
            ->count();
        return $this->success([
            'commentCount' => $count
        ]);
    }

    /**
     * 用户评论列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function commentList(Request $request)
    {
        $size = $request->get('size', 10);
        $userComments = UserComment::userComment($request->get('user')['id'])->paginate($size);
        foreach ($userComments as &$comment) {
            $comment->rate /= 5;
        }
        return $this->success($userComments);
    }

    /**
     * 用户留言
     *
     * @param Request $request
     * @param HomeRequest $validate
     * @return \Illuminate\Http\JsonResponse
     */
    public function advice(Request $request, HomeRequest $validate)
    {
        $data = $this->objectKeyCamel2Underline($request->all());
        $validate->advice($data);
        if ($validate->flag) {
            return $this->failed($validate->msg);
        }
        $user = $request->get('user');
        UserAdvice::create([
            'uid' => $user['id'],
            'nickname' => $user['nickname'],
            'content' => $data['content'],
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        return $this->success();
    }

}
