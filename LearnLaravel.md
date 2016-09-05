# LearnLaravel

[TOC]

## 项目准备

### 安装 Laravel

使用`composer`创建 laravel 项目

### 开启 PHP 内置 web 服务器

```shell
cd laravel/public
php -S 0.0.0.0:80
```

### 使用 Auth 系统

```shell
php artisan make:auth
```

### 连接数据库

1. 修改配置

`.env`

```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel5
DB_USERNAME=root
DB_PASSWORD=root
```

2. 数据库迁移

```shell
php artisan migrate
```

3. migration

`/database/migrations/XXX_table.php`

用 PHP 描述数据库构造，并且使用命令行一次性部署所有数据库结构

### 使用 Eloquent

Eloquent 是 Laravel 的 ORM，对象关系映射。

>  [深入理解 Laravel Eloquent（一）——基本概念及用法](https://lvwenhan.com/laravel/421.html)
>


1. Eloquent 是什么

Laravel 内置的 ORM 系统，Model 类继承自 Eloquent 提供的 Model 类，子类就可以直接使用父类的函数

2. 怎么使用

使用 Artisan 工具新建 Model 类及其附属的 Migration 和 Seeder （数据填充）类。

```shell
php artisan make:model Article
```

在`app/`路径下便生成`Article.php`类文件

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
}
```

### 使用 Migration 和 Seeder

生成对应 Article 这个 Model 的 Migration 和 Seeder

1. 使用 artisan 生成 Migration

```shell
php artisan make:migration create_article_table
```

`/database/migrations` 中 `XXX_createarticle_table`文件被创建，修改其`up()`函数

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('body')->nullable();
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
```

这段代码描述的是 Article 对应的数据表中那张表的结构。 Laravel 默认 Model 对应的表名是这个英文单词的复数形式，所以就是 articles。

运行下面命令，将 PHP 代码变成 MySQL 中的数据表

```shell
php artisan migrate
```

2. 使用 artisan 生成 Seeder

Seeder 解决的是在开发 web 应用的时候，需要手动向数据库中填入假数据的繁琐低效问题。

运行以下命令，创建 Seeder 文件

```shell
php artisan make:seeder ArticleSeeder
```

`/database/seeds`中多了一个文件`ArticleSeeder.php`，修改其`run()`函数

```php
<?php

use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('articles')->delete();

        for ($i = 0; $i < 10; $i++) {
            \App\Article::create([
                'title' => 'Title ' . $i,
                'body' => 'Body ' . $i,
                'user_id' => 1,
      	    ]);
        }
    }
}
```

3. 把 ArticleSeeder 注册到系统内。

修改`/database/seeds/DatabaseSeeder.php`中的`run()`函数

```php
<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(ArticleSeeder::class);
    }
}
```

运行以下命令把`ArticleSeeder.php`加入自动加载系统，避免找不到类的错误。

```shel
composer dump-autoload
```

然后执行 seed

```shell
php artisan db:seed
```

此时，articles 表中已经插入了 10 行假数据。

## 初识路由

路由系统是所有 PHP 框架的核心，路由承载的是 URL 到代码片段的映射，不同的框架所附带的路由系统是这个框架本质最真实的写照。

> # [Routing - Laravel - The PHP Framework For Web Artisans](https://laravel.com/docs/5.3/routing)
>
> ## [Basic Routing](https://laravel.com/docs/5.3/routing#basic-routing)
>
> 最基本的 Laravel 路由仅接受一个 URI 和一个闭包，提供一个非常简单和有表现力的定义路由的方法。
>
> ```php
> Route::get('foo', function () {
>     return 'Hello World';
> });
> ```
>
> #### The Default Route Files
>
> 所有的 Laravel 路由都定义在你的路由文件下，位于`routes`路径下。这些文件通过框架自动加载。`routes/web.php`这个文件定义你的 web 接口路由。 这些路由指定了`web`中间件组，来提供一些功能，比如 session 状态和 CSRF 保护。在`routes/api.php`中的这些路由是无状态的，并且指定`api`中间件组。
>
> 对大多数应用来说，以在`routes/web.php`文件中定义路由开始。.
>
> #### Available Router Methods
>
> 路由器允许你注册路由来响应任何 HTTP 动词。
>
> ```php
> Route::get($uri, $callback);
> Route::post($uri, $callback);
> Route::put($uri, $callback);
> Route::patch($uri, $callback);
> Route::delete($uri, $callback);
> Route::options($uri, $callback);
> ```
>
> 有时可能需要注册一条路由来响应多个 HTTP 动词。你可以这样做，使用`match`方法。或者你甚至可能注册一条路由来响应所有 HTTP 动词，使用`any`方法。
>
> ```php
> Route::match(['get', 'post'], '/', function () {
>     //
> });
>
> Route::any('foo', function () {
>     //
> });
> ```
>
> #### CSRF Protection
>
> 所有指向`POST`, `PUT`, 或者 `DELETE`在`web`路由文件中定义的路由的 HTML 表单都应该包含一个 CSRF 令牌字段。否则，请求会被拒绝。You can read more about CSRF protection in the [CSRF documentation](https://laravel.com/docs/5.3/csrf):
>
> ```php
> <form method="POST" action="/profile">
>     {{ csrf_field() }}
>     ...
> </form>
> ```
>
> ## [Route Parameters](https://laravel.com/docs/5.3/routing#route-parameters)
>
> ### Required Parameters
>
> 当然，有时你会需要捕捉路由中的 URI 片段。例如，你可能需要从 URI 中捕捉一个用户的 ID 。可以通过这样定义路由参数来实现。
>
> ```php
> Route::get('user/{id}', function ($id) {
>     return 'User '.$id;
> });
> ```
>
> 你可以通过路由定义多个路由参数为必需的。
>
> ```php
> Route::get('posts/{post}/comments/{comment}', function ($postId, $commentId) {
>     //
> });
> ```
>
> 路由参数总是包括在大括号`{}`之内，应该由字母符号组成。路由参数不可以包含`-`符号，使用下划线`_`来代替。
>
> ### Optional Parameters
>
> 偶尔，你可能需要指定一个路由参数，但是存在多个可选的路由参数。可以这样做，在参数名后放置一个`?`。要确保给路由的相应的变量一个默认值。 
>
> ```php
> Route::get('user/{name?}', function ($name = null) {
>     return $name;
> });
>
> Route::get('user/{name?}', function ($name = 'John') {
>     return $name;
> });
> ```
>
> ## [Named Routes](https://laravel.com/docs/5.3/routing#named-routes)
>
> 命名路由允许指定路由的 URLs 或重定向的方便的派生。你可以通过在路由定义之上链式调用`name`方法指定一个名字。
>
> ```php
> Route::get('user/profile', function () {
>     //
> })->name('profile');
> ```
>
> 你也可以给控制器 actions 指定路由名字。
>
> ```php
> Route::get('user/profile', 'UserController@showProfile')->name('profile');
> ```
>
> #### Generating URLs To Named Routes
>
> 一旦为一条给定路由指定了名字，你可以通过全局的`route`方法在派生 URLs 或重定向时使用路由名字。
>
> ```php
> // Generating URLs...
> $url = route('profile');
>
> // Generating Redirects...
> return redirect()->route('profile');
> ```
>
> 如果命名路由定义了参数，你可以传递参数作为`route`方法的第二个参数。给定的参数会自动被插入到 URL 中正确的位置。
>
> ```php
> Route::get('user/{id}/profile', function ($id) {
>     //
> })->name('profile');
>
> $url = route('profile', ['id' => 1]);
> ```
>
> ## [Route Groups](https://laravel.com/docs/5.3/routing#route-groups)
>
> 路由组允许共享路由属性，比如中间件或命名空间，across 大量的路由不需要在每个独立的路由中定义它们的属性。
>
> Route groups allow you to share route attributes, such as middleware or namespaces, across a large number of routes without needing to define those attributes on each individual route.
>
> 共享属性以数组形式作为`Route::group`方法的第一个参数被指定
>
> ### Middleware
>
> 要给一个路由组中的所有路由指定中间件，你可以使用路由组属性数组中的`middleware`键。 中间件会以在数组中列出的顺序被执行。
>
> ```php
> Route::group(['middleware' => 'auth'], function () {
>     Route::get('/', function ()    {
>         // Uses Auth Middleware
>     });
>
>     Route::get('user/profile', function () {
>         // Uses Auth Middleware
>     });
> });
> ```
>
> ### Namespaces
>
> 路由组中另一个常用的用例是使用路由组数组中的`namespace`参数给一组控制器指定相同的 PHP 命名空间。
>
> ```php
> Route::group(['namespace' => 'Admin'], function() {
>     // Controllers Within The "App\Http\Controllers\Admin" Namespace
> });
> ```
>
> 牢记，默认情况下，`RouteServiceProvider`包含着在命名空间组中的路由文件，允许你注册控制器路由而不指定全`App\Http\Controllers`命名空间前缀。因此，你只需要指定紧跟在基命名空间`App\Http\Controllers`后的部分命名空间。
>
> ### Sub-Domain Routing
>
> 路由组也可以被用来处理子域名路由。子域名可以被指定路由参数，就像路由URIs，允许你捕捉在路由或控制器中使用的子域名的一部分。
>
> Sub-domains may be assigned route parameters just like route URIs, allowing you to capture a portion of the sub-domain for usage in your route or controller. 
>
> 子路名可以使用路由组属性数组的`domain`键来指定。
>
> ```php
> Route::group(['domain' => '{account}.myapp.com'], function () {
>     Route::get('user/{id}', function ($account, $id) {
>         //
>     });
> });
> ```
>
> ### Route Prefixes
>
> `prefix`路由组属性可以被用来给指定 URI 路由组中的每一个条路由加前缀。例如，你可能想要给`admin`路由组中的所有路由 URIs 加上前缀。
>
> ```php
> Route::group(['prefix' => 'admin'], function () {
>     Route::get('users', function ()    {
>         // Matches The "/admin/users" URL
>     });
> });
> ```
>
> ## [Route Model Binding](https://laravel.com/docs/5.3/routing#route-model-binding)
>
> 当注入一个 model ID 给一条路由或控制器 action 时，你会经常执行查询来获取对应 ID 的 model 。Laravel 路由模型绑定提供了一个便利的方式来自动地直接注入 model 实例到你的路由。例如，不是注入用户的 ID ，而是你可以注入匹配给定 ID 的整个`User` model 。
>
> ### Implicit Binding
>
> Lavavel自动解析定义在路由或控制器 actions 中的 Eloquent models，它们的变量名匹配一个路由片段名。例如：
>
>  Laravel automatically resolves Eloquent models defined in routes or controller actions whose variable names match a route segment name. For example:
>
> ```
> Route::get('api/users/{user}', function (App\User $user) {
>     return $user->email;
> });
> ```
>
> 在此例中，由于定义在路由上的 Eloquent `$user` 变量匹配路由 URI 的`user`部分， Laravel 会自动注入 ID 匹配来自请求 URI 中对应值的 model 实例。如果在数据库中找不到一个匹配的 model 实例，一个 404 HTTP 响应会自动生成。
>
> #### Customizing The Key Name
>
> If you would like implicit model binding to use a database column other than `id` when retrieving a given model class, you may override the `getRouteKeyName` method on the Eloquent model:
>
> ```
> /**
>  * Get the route key for the model.
>  *
>  * @return string
>  */
> public function getRouteKeyName()
> {
>     return 'slug';
> }
> ```
>
> ### Explicit Binding
>
> To register an explicit binding, use the router's `model` method to specify the class for a given parameter. You should define your explicit model bindings in the `boot` method of the`RouteServiceProvider` class:
>
> ```
> public function boot()
> {
>     parent::boot();
>
>     Route::model('user', 'App\User');
> }
> ```
>
> Next, define a route that contains a `{user}` parameter:
>
> ```
> $router->get('profile/{user}', function(App\User $user) {
>     //
> });
> ```
>
> Since we have bound all `{user}` parameters to the `App\User` model, a `User` instance will be injected into the route. So, for example, a request to `profile/1` will inject the `User` instance from the database which has an ID of `1`.
>
> If a matching model instance is not found in the database, a 404 HTTP response will be automatically generated.
>
> #### Customizing The Resolution Logic
>
> If you wish to use your own resolution logic, you may use the `Route::bind` method. The `Closure`you pass to the `bind` method will receive the value of the URI segment and should return the instance of the class that should be injected into the route:
>
> ```
> $router->bind('user', function ($value) {
>     return App\User::where('name', $value)->first();
> });
> ```
>
> ## [Form Method Spoofing](https://laravel.com/docs/5.3/routing#form-method-spoofing)
>
> HTML forms do not support `PUT`, `PATCH` or `DELETE` actions. So, when defining `PUT`, `PATCH` or`DELETE` routes that are called from an HTML form, you will need to add a hidden `_method` field to the form. The value sent with the `_method` field will be used as the HTTP request method:
>
> ```
> <form action="/foo/bar" method="POST">
>     <input type="hidden" name="_method" value="PUT">
>     <input type="hidden" name="_token" value="{{ csrf_token() }}">
> </form>
> ```
>
> You may use the `method_field` helper to generate the `_method` input:
>
> ```
> {{ method_field('PUT') }}
> ```
>
> ## [Accessing The Current Route](https://laravel.com/docs/5.3/routing#accessing-the-current-route)
>
> You may use the `current`, `currentRouteName`, and `currentRouteAction` methods on the `Route`facade to access information about the route handling the incoming request:
>
> ```
> $route = Route::current();
>
> $name = Route::currentRouteName();
>
> $action = Route::currentRouteAction();
> ```
>
> Refer to the API documentation for both the [underlying class of the Route facade](http://laravel.com/api/5.3/Illuminate/Routing/Router.html) and [Route instance](http://laravel.com/api/5.3/Illuminate/Routing/Route.html) to review all accessible methods.


