<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;

use App\Http\Requests;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        if (Comment::create($request->all())) {
            // 评论成功，页面重定向返回
            return redirect()->back();
        } else {
            // 评论失败，页面重定向回去，保留用户输入并给出提示
            return redirect()->back() - withInput()->withErrors('评论发表失败！');
        }
    }
}
