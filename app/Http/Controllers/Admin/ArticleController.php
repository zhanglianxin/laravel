<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Article;

class ArticleController extends Controller
{
    public function index()
    {
        return view('admin/article/index')->withArticles(Article::all());
    }

    public function create()
    {
        return view('admin/article/create');
    }

    public function edit($id) {
        // 带数据返回该视图
        return view('admin/article/edit')->withArticle(Article::find($id));
    }

    public function store(Request $request)
    {
        // 数据验证
        $this->validate($request, [
            'title' => 'required|unique:articles|max:255',
            'body' => 'required',
        ]);

        /* 通过Artiel Model插入一条数据进articles表 */
        $article = new Article; // 初始化对象
        // 通过表单提交的字段给对象的属性赋值
        $article->title = $request->get('title');
        $article->body = $request->get('body');
        // 获取当前Auth系统中注册的用户，将id赋值给对象的相应属性
        $article->user_id = $request->user()->id;

        // 将数据保存到数据库，通过判断保存结果，控制页面进行不同跳转
        if ($article->save()) {
            // 保存成功，页面重定向到 文章管理页
            return redirect('admin/article');
        } else {
            // 保存失败，页面重定向回去，保留用户输入并给出提示
            return redirect()->back()->withInput()->withErrors('保存失败');
        }
    }

    public function update(Request $request, $id) {
        // 数据验证
        $this->validate($request, [
            'title' => 'required|unique:articles|max:255',
            'body' => 'required',
        ]);

        /* 通过Artiel Model修改一条articles表中已有的数据 */
        $article = Article::find($id); // 从数据库中取出该对象
        // 通过表单提交的字段给对象的属性赋值
        $article->title = $request->get('title');
        $article->body = $request->get('body');

        // 将数据保存到数据库，通过判断保存结果，控制页面进行不同跳转
        if ($article->save()) {
            // 保存成功，页面重定向到 文章管理页
            return redirect('admin/article');
        } else {
            // 保存失败，页面重定向回去，保留用户输入并给出提示
            return redirect()->back()->withInput()->withErrors('更新失败');
        }
    }

    public function destroy($id) {
        /* 通过Artiel Model删除一条articles表中已有的数据 */
        $article = Article::find($id);

        // 将数据从数据库中删除，通过判断删除结果，控制页面进行不同跳转
        $msg = '';
        if ($article->delete()) {
            $msg = '删除成功';
        } else {
            $msg = '删除失败';
        }
        return redirect()->back()->withInput()->withErrors($msg);
    }
}
