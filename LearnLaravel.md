# LearnLaravel

[TOC]

## 项目准备

### 安装 Laravel

使用 `composer` 创建 laravel 项目

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

`database/migrations/XXX_table.php`

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

在 `app/` 路径下便生成 `Article.php` 类文件

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

`database/migrations` 中 `XXX_createarticle_table` 文件被创建，修改其 `up()` 函数

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

`database/seeds` 中多了一个文件 `ArticleSeeder.php` ，修改其 `run()` 函数

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

修改 `database/seeds/DatabaseSeeder.php` 中的 `run()` 函数

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

运行以下命令把 `ArticleSeeder.php` 加入自动加载系统，避免找不到类的错误。

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
> 所有的 Laravel 路由都定义在你的路由文件下，位于 `routes` 路径下。这些文件通过框架自动加载。 `routes/web.php` 这个文件定义你的 web 接口路由。 这些路由指定了 `web` 中间件组，来提供一些功能，比如 session 状态和 CSRF 保护。在 `routes/api.php` 中的这些路由是无状态的，并且指定 `api` 中间件组。
>
> 对大多数应用来说，以在 `routes/web.php` 文件中定义路由开始。.
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
> 有时可能需要注册一条路由来响应多个 HTTP 动词。你可以这样做，使用 `match` 方法。或者你甚至可能注册一条路由来响应所有 HTTP 动词，使用 `any` 方法。
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
> 所有指向 `POST` 、 `PUT` 或者 `DELETE` 在 `web` 路由文件中定义的路由的 HTML 表单都应该包含一个 CSRF 令牌字段。否则，请求会被拒绝。You can read more about CSRF protection in the [CSRF documentation](https://laravel.com/docs/5.3/csrf):
>
> ```html
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
> 路由参数总是包括在大括号 `{}` 之内，应该由字母符号组成。路由参数不可以包含 `-` 符号，使用下划线 `_` 来代替。
>
> ### Optional Parameters
>
> 偶尔，你可能需要指定一个路由参数，但是存在多个可选的路由参数。可以这样做，在参数名后放置一个 `?` 。要确保给路由的相应的变量一个默认值。 
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
> 命名路由允许指定路由的 URLs 或重定向的方便的派生。你可以通过在路由定义之上链式调用 `name` 方法指定一个名字。
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
> 一旦为一条给定路由指定了名字，你可以通过全局的 `route` 方法在派生 URLs 或重定向时使用路由名字。
>
> ```php
> // Generating URLs...
> $url = route('profile');
>
> // Generating Redirects...
> return redirect()->route('profile');
> ```
>
> 如果命名路由定义了参数，你可以传递参数作为 `route` 方法的第二个参数。给定的参数会自动被插入到 URL 中正确的位置。
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
> 共享属性以数组形式作为 `Route::group` 方法的第一个参数被指定
>
> ### Middleware
>
> 要给一个路由组中的所有路由指定中间件，你可以使用路由组属性数组中的 `middleware` 键。 中间件会以在数组中列出的顺序被执行。
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
> 路由组中另一个常用的用例是使用路由组数组中的 `namespace` 参数给一组控制器指定相同的 PHP 命名空间。
>
> ```php
> Route::group(['namespace' => 'Admin'], function() {
>     // Controllers Within The "App\Http\Controllers\Admin" Namespace
> });
> ```
>
> 牢记，默认情况下， `RouteServiceProvider` 包含着在命名空间组中的路由文件，允许你注册控制器路由而不指定全 `App\Http\Controllers` 命名空间前缀。因此，你只需要指定紧跟在基命名空间 `App\Http\Controllers` 后的部分命名空间。
>
> ### Sub-Domain Routing
>
> 路由组也可以被用来处理子域名路由。子域名可以被指定路由参数，就像路由URIs，允许你捕捉在路由或控制器中使用的子域名的一部分。
>
> Sub-domains may be assigned route parameters just like route URIs, allowing you to capture a portion of the sub-domain for usage in your route or controller. 
>
> 子路名可以使用路由组属性数组的 `domain` 键来指定。
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
> `prefix` 路由组属性可以被用来给指定 URI 路由组中的每一个条路由加前缀。例如，你可能想要给 `admin` 路由组中的所有路由 URIs 加上前缀。
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
> 当注入一个 model ID 给一条路由或控制器 action 时，你会经常执行查询来获取对应 ID 的 model 。Laravel 路由模型绑定提供了一个便利的方式来自动地直接注入 model 实例到你的路由。例如，不是注入用户的 ID ，而是你可以注入匹配给定 ID 的整个 `User` model 。
>
> ### Implicit Binding
>
> Lavavel自动解析定义在路由或控制器 actions 中的 Eloquent models，它们的变量名匹配一个路由片段名。例如：
>
> ```php
> Route::get('api/users/{user}', function (App\User $user) {
>     return $user->email;
> });
> ```
>
> 在此例中，由于定义在路由上的 Eloquent `$user` 变量匹配路由 URI 的 `{user}` 部分， Laravel 会自动注入 ID 匹配来自请求 URI 中对应值的 model 实例。如果在数据库中找不到一个匹配的 model 实例，一个 404 HTTP 响应会自动生成。
>
> #### Customizing The Key Name
>
> 当获取一个给定的 model 类时，如果你想要隐式的 model 绑定来使用数据库中的除`id` 以外的字段，你可以重写 `getRouteKeyName` 方法基于 Eloquent model 。
>
> ```php
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
> 要注册一个显示的绑定，使用路由器的 `model` 方法来为一个给定的参数指定这个类。你应该在 `RouteServiceProvider` 类的 `boot` 方法定义你的显示 `model` 绑定。
>
> ```php
> public function boot()
> {
>     parent::boot();
>
>     Route::model('user', 'App\User');
> }
> ```
>
> 接下来，定义一条包含一个 `{user}` 参数的路由。
>
> ```php
> $router->get('profile/{user}', function(App\User $user) {
>     //
> });
> ```
>
> 由于我们已经绑定所有的 `{user}` 参数到 `App\User` model ，一个 `User` 实例会被注入到这个路由。那么，例如，一个到 `profile/1` 的请求会注入到来自数据库的 ID 为 `1` 的 `User` 实例。
>
> 如果在数据库中找不到一个匹配的 model 实例，一个 404 HTTP 响应会自动生成。
>
> #### Customizing The Resolution Logic
>
> 如果你想使用自己的解决逻辑，你可以使用 `Route::bing` 方法。你传入到 `bing` 方法的 `Closure` 会接收到 URI 片段的值，应该返回要注入到路由中的类的实例。
>
> ```php
> $router->bind('user', function ($value) {
>     return App\User::where('name', $value)->first();
> });
> ```
>
> ## [Form Method Spoofing](https://laravel.com/docs/5.3/routing#form-method-spoofing)
>
> HTML 表单不支持 `PUT` 、 `PATCH` 或者 `DELETE` 动作。那么，当定义被 HTML 表单调用的 `PUT` 、 `PATCH` 或者 `DELETE` 路由时，你会需要添加一个隐藏 `_method` 字段到表单。用 `_method` 字段发送的值会被 HTTP 请求方法使用。
>
> ```html
> <form action="/foo/bar" method="POST">
>     <input type="hidden" name="_method" value="PUT">
>     <input type="hidden" name="_token" value="{{ csrf_token() }}">
> </form>
> ```
>
> 你可以使用 `method_filed` 帮助者来生成 `_method` input 表单域。
>
> ```php
> {{ method_field('PUT') }}
> ```
>
> ## [Accessing The Current Route](https://laravel.com/docs/5.3/routing#accessing-the-current-route)
>
> 你可以使用 `current` 、 `currentRouteName` 和 `currentRouteAction` 方法基于 `Route` facade 来获取路由处理传入的请求信息。
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

Laravel 5.3 路由在 `routes/web.php` 文件中定义。

`web.php`

```php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
```

其中 `Route::auth();` 是 Auth 系统自动注入的路由配置。

## 命名空间

**[绝对类名]**

Laravel 5 引入了 `psr-4` 命名空间标准：命名空间和实际文件所在的文件夹层级一致，文件夹首字母大写即为此文件的约定命名空间。

在启用了命名空间的系统中，子命名空间下的类有一个全局的都可以直接访问的名称，这个名称就是该类的命名空间全称。

> [《PHP 命名空间 解惑 》](https://lvwenhan.com/php/401.html)

## 基础路由解析

### 闭包路由

路由文件中前三行即为闭包路由：

```php
Route::get('/', function () {  
    return view('welcome');
});
```

闭包路由使用闭包作为此条请求的响应代码，方便灵活，很多简单操作直接在闭包里解决即可。

### 控制器@方法 路由

```php
Route::get('/home', 'HomeController@index');
```

### 控制器@方法 调用原理浅析

使用字符串初始化类得到对象，调用对象的指定方法，返回结果。

`app/Providers/RouteServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * 这个命名空间应用于你的控制器路由
     *
     * 另外，它被设置为 URL 生成器的根命名空间
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * 定义你的路由模型绑定、模式过滤器等
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * 为应用定义路由
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();

        $this->mapApiRoutes();

        //
    }

    /**
     * 为应用定义 web 路由
     *
     * 这些路由都接收 session 状态、 CSRF 保护等
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * 为应用定义 api 路由
     *
     * 这些路由是典型的无状态的
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }
}
```

此文件中 `mapWebRoutes` 方法给所有的路由统一加进了一个路由组，定义了一个命名空间和一个中间件。

其父类文件 `Illuminate/Foundation/Support/Providers/RouteServiceProvider.php`

```php
 <?php

namespace Illuminate\Foundation\Support\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\UrlGenerator;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * 应用的控制器命名空间
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * 引导任意应用服务
     *
     * @return void
     */
    public function boot()
    {
        $this->setRootControllerNamespace();

        if ($this->app->routesAreCached()) {
            $this->loadCachedRoutes();
        } else {
            $this->loadRoutes();

            $this->app->booted(function () {
                Route::getRoutes()->refreshNameLookups();
            });
        }
    }

    /**
     * 为应用设置根控制器命名空间
     *
     * @return void
     */
    protected function setRootControllerNamespace()
    {
        if (!is_null($this->namespace)) {
            $this->app[UrlGenerator::class]->setRootControllerNamespace($this->namespace);
        }
    }

    /**
     * 为应用加载缓存的路由
     *
     * @return void
     */
    protected function loadCachedRoutes()
    {
        $this->app->booted(function () {
            require $this->app->getCachedRoutesPath();
        });
    }

    /**
     * 加载应用路由
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $this->app->call([$this, 'map']);
    }

    /**
     * 为应用加载标准的路由文件
     *
     * @param  string $path
     * @return mixed
     */
    protected function loadRoutesFrom($path)
    {
        $router = $this->app->make(Router::class);

        if (is_null($this->namespace)) {
            return require $path;
        }

        $router->group(['namespace' => $this->namespace], function (Router $router) use ($path) {
            require $path;
        });
    }

    /**
     * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * 传递动态方法到路由器实例
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(
            [$this->app->make(Router::class), $method], $parameters
        );
    }
}
```

--> 追踪命名空间、类、方法的传递方式

`vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php`

```php
<?php

namespace Illuminate\Routing;

use Illuminate\Container\Container;

class ControllerDispatcher
{
    use RouteDependencyResolverTrait;

    /**
     * 容器实例
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * 创建一个新的控制器转发器实例
     *
     * @param  \Illuminate\Container\Container $container
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 转发一个请求到一个给定的控制器和方法
     *
     * @param  \Illuminate\Routing\Route $route
     * @param  mixed $controller
     * @param  string $method
     * @return mixed
     */
    public function dispatch(Route $route, $controller, $method)
    {
        $parameters = $this->resolveClassMethodDependencies(
            $route->parametersWithoutNulls(), $controller, $method
        );

        if (method_exists($controller, 'callAction')) {
            return $controller->callAction($method, $parameters);
        }

        return call_user_func_array([$controller, $method], $parameters);
    }

    /**
     * 获得控制器实例的中间价
     *
     * @param  \Illuminate\Routing\Controller $controller
     * @param  string $method
     * @return array
     */
    public static function getMiddleware($controller, $method)
    {
        if (!method_exists($controller, 'getMiddleware')) {
            return [];
        }

        return collect($controller->getMiddleware())->reject(function ($data) use ($method) {
            return static::methodExcludedByOptions($method, $data['options']);
        })->pluck('middleware')->all();
    }

    /**
     * 判断给定的选项是否包含一个特殊的方法
     *
     * @param  string $method
     * @param  array $options
     * @return bool
     */
    protected static function methodExcludedByOptions($method, array $options)
    {
        return (isset($options['only']) && !in_array($method, (array)$options['only'])) ||
        (!empty($options['except']) && in_array($method, (array)$options['except']));
    }
}
```

在 `dispatch` 方法中增加一行 `var_dump($controller);` ，刷新就可以看到页面上输出 `string(35) "App\Http\Controllers\HomeController"` ，这就是要调用的控制器类的“绝对类名”。

Laravel 使用了完整的面向对象程序架构，对控制器的调用进行了很多层封装，所以最简单的探测方式就是手动抛出错误，这样就可以看到完整的调用栈。

在 `HomeController` 的 `index` 方法里的 `return` 之前增加一行 `throw new \Exception('手动抛出错误', 1);` ，刷新页面，就可以看到是 `vendor/laravel/framework/src/Illuminate/Routing/Controller.php` 中的第 80 行最终驱动起了 `HomeController` 。

## 简单博客系统规划

前面已经新建了一个 Eloquent 的 model 类 Article ，使用 migration 建立了数据表并使用 seeder 填入了测试数据。博客系统暂时将只管理这一种资源：后台需要使用帐号密码登录，进入后台之后，可以新增、修改、删除文章；前台显示文章列表，并在点击标题之后显示出文章全文。

## 搭建前台

### 修改路由

`routes/web.php`

```php
Route::get('/', 'HomeController@index');
```

现在系统首页落到 `App\Http\Controllers\HomeController` 类的 `index` 方法上了。

### 查看 HomeController 的 index 函数

`app/Http/Controllers/HomeController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * 创建一个控制器实例
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 返回 home 视图
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }
}
```

### blade 浅析

blade 会对视图文件进行预处理，简化一些重复性很高的 echo 、 foreach 等 PHP 代码。 blade 还提供了一个灵活强大的视图组织系统。

`resources/views/home.blade.php`

```php
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

`@extends('layouts.app')` 

表示此视图的基视图是 `resources/views/layouts/app.blade.php` 。在使用名称查找视图的时候，可以使用 `.` 代替 `/` 或者 `\` 。

`@section('content') ... @endsection` 

这两个标识符之前的代码，会被放到基视图的 `@yield('content')` 中进行输出。

### 访问首页

会看到登录页面，因为 `HomeController` 构造函数中加入了中间件处理。

这个函数会在控制器类初始化的时候自动载入一个名为 `auth` 的中间件，正是这一步导致了首页需要登录认证。

### 向视图文件输出数据

既然 Controller - View 架构已经运行，下一步就是引入 Model 了。

Laravel 中向视图传递数据非常简单：

```php
public function index()
{
  return view('home')->withArticles(\App\Article::all());
}
```

### 修改视图文件

`resources/views/home.blade.php`

```php
@extends('layouts.app')

@section('content')
    <div class="container">
        <div id="title" style="text-align: center;">
            <h1>Learn Laravel 5</h1>
            <div style="padding: 5px; font-size: 16px;">Learn Laravel 5</div>
        </div>
        <hr>
        <ul>
            @foreach($articles as $article)
                <li style="margin: 50px 0;">
                    <div class="title">
                        <a href="{{url('article/'.$article->id)}}"><h4>{{$article->title}}</h4></a>
                    </div>
                    <div class="body">
                        <p>{{$article->body}}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
```

### 调整视图

修改基视图 `resources/views/layouts/app.blade.php`

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link href="//cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</head>
<body id="app-layout">
<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">

            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#app-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                Laravel
            </a>
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                <li><a href="{{ url('/home') }}">Home</a></li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login</a></li>
                    <li><a href="{{ url('/register') }}">Register</a></li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

@yield('content')

</body>
</html>
```

## 搭建后台

### 构建 Article 详情页

#### 生成控制器

使用 Artisan 工具生成控制器文件

`php artisan make::controller Admin/HomeController`

`app/Http/Controllers/Admin/HomeController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('admin/home');
    }
}
```

#### 增加路由

要使用路由组将后台页面至于“需要登录才能访问”的中间件下，保证安全：

`routes/web.php`

```php
Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::get('/', 'HomeController@index');
});
```

路由组可以给组内路由一次性增加 命名空间、 uri 前缀、域名限定、中间件等属性，并且可以多级嵌套，非常强大。

> 路由组文档参见：[路由群组](http://laravel-china.org/docs/5.2/routing#route-groups)

此路由组的功能是：访问这个页面必须先登录，如果已经登录，将 http://locahost 指向 `HomeController` 的 `index` 方法。其中，需要登录由 `middleware` 定义， `/admin` 由 `prefix` 定义， `Admin` 由 `namespace` 定义， `HomeController` 是实际的类名。

### 构建后台首页

#### 新建 index 方法

`app/Http/Controllers/Admin/HomeController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('admin/home');
    }
}
```

#### 新建视图文件

`resources/views/admin/home.blade.php`

```php
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>
                    <div class="panel-body">
                        <a href="{{url('admin/article')}}" class="btn btn-lg btn-success col-xs-12">管理文章</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

#### 修改 Auth 系统登录成功之后的跳转路径

`app/Http/Controllers/Auth/AuthController.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 注册和登录控制器
    |--------------------------------------------------------------------------
    |
    | 这个控制器处理注册新用户和已存在用户的认证。
    | 默认情况下，这个控制器使用一个简单的特征来添加这些行为。
    | 为什么你不探索它呢？
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * 用户登录或注册后重定向到哪里
     *
     * @var string
     */
    protected $redirectTo = 'admin';

    /**
     * 创建一个新的认证控制器实例
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * 为传入的注册请求获得一个验证器
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * 在一个有效的注册后创建一个新的用户实例
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
}
```

### 构建 Article 后台管理功能

#### 添加路由

添加针对 `http://localhost/admin/article` 的路由

`routes/web.php`

```php
Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::get('/', 'HomeController@index');
    Route::get('article', 'ArticleController@index');
//    Route::resource('article', 'ArticleController'); // 资源路由
});
```

#### 新建控制器

```php
php artisan make:controller Admin/ArticleController
```

新增 `index` 方法

`app/Http/Controllers/Admin/ArticleController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
// Article 类和当前控制器不在一个命名空间路径下，不能直接调用
use App\Article;

class ArticleController extends Controller
{
    public function index()
    {
        return view('admin/article/index')->withArticles(Article::all());
    }
}
```

#### 新建视图

`resources/views/admin/article/index.blade.php`

```php
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">文章管理</div>
                    <div class="panel-body">
                        @if(count($errors) > 0)
                            <div class="alert alert-danger">
                                {!! implode('<br>', $errors->all()) !!}
                            </div>
                        @endif
                        <a href="{{url('admin/article/create')}}" class="btn btn-lg btn-primary">新增</a>
                        @foreach($articles as $article)
                            <hr>
                            <div class="article">
                                <h4>{{$article->title}}</h4>
                                <div class="content">
                                    <p>
                                        {{$article->body}}
                                    </p>
                                </div>
                            </div>
                            <a href="{{url('admin/article/'.$article->id.'/edit')}}" class="btn btn-success">编辑</a>
                            <form action="{{url('admin/article/'.$article->id)}}" method="post"
                                  style="display: inline;">
                                {{method_field("DELETE")}}
                                {{csrf_field()}}
                                <button type="submit" class="btn btn-danger">删除</button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

## RESTful 资源控制器

资源控制器是 Laravel 内部的一种功能强大的约定，它约定了一系列对某一种资源进行“增删改查”操作的路由配置，让我们不再需要对每一项需要管理的资源都写 N 行重复形式的路由。

> ## [RESTful 资源控制器](http://laravel-china.org/docs/5.2/controllers#restful-resource-controllers)

### 配置资源路由

`routes/web.php`

```php
Route::group(['middleware' => 'auth', 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::get('/', 'HomeController@index');
//    Route::get('article', 'ArticleController@index');
    Route::resource('article', 'ArticleController'); // 资源路由
});
```

### 新增 Article

#### 获取“新增 Article ”的页面

在 ArticleController 中新增 create 方法，返回一个可以输入文章的页面

`app/Http/Controllers/Admin/ArticleController.php`

```php
    public function create()
    {
        return view('admin/article/create');
    }
```

新增视图文件 `resources/views/admin/article/create.blade.php`

```php
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">新增一篇文章</div>
                    <div class="panel-body">

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>新增失败</strong> 输入不符合要求<br><br>
                                {!! implode('<br>', $errors->all()) !!}
                            </div>
                        @endif

                        <form action="{{ url('admin/article') }}" method="POST">
                            {!! csrf_field() !!}
                            <input type="text" name="title" class="form-control" required="required"
                                   placeholder="请输入标题">
                            <br>
                            <textarea name="body" rows="10" class="form-control" required="required"
                                      placeholder="请输入内容"></textarea>
                            <br>
                            <button class="btn btn-lg btn-info">新增文章</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

视图调用 `return view('admin/article/create');` 返回了视图文件。

`view()` 方法是 Laravel 中一个全局的方法，用于调用视图文件，接收一个字符串参数，并按照参数去调取对应的路由。

#### 提交数据到后端

视图 `resources/views/admin/article/create.blade.php` 中有一个表单。

第一，表单的 `action` 。动态生成了一个 URL 作为 action ，并且指定了表单提交需要使用 POST 方法。

第二， `csrf_field()` 。这是 Laravel 中内置的防止应对 CSRF 攻击的防范措施，任何 POST PUT PATCH 请求都会被检测是否提交了 CSRF 字段。

```php
{!! csrf_field() !!}
```

实际上会生成一个隐藏的 input ：

```html
<input type="hidden" name="_token" value="JXu9M89kBgMKkIMYBhFbJe1tv0RmO6D4UdxdKwq6">
```

也可以这样写：

```html
<input type="hidden" name="_token" value="{{ csrf_token() }}">
```
如果你的系统有很多 Ajax ，而你又不想降低安全性，这里的 `csrf_token()` 函数将会给你巨大的帮助。

#### 后端接收数据

新建 store 方法

```php
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
```

### 编辑 Article

#### 获取“编辑 Article ”的页面

在 ArticleController 中新增 edit 方法，返回一个可以编辑文章的页面

`app/Http/Controllers/Admin/ArticleController.php`

```php
public function edit($id) {
    // 带数据返回该视图
    return view('admin/article/edit')->withArticle(Article::find($id));
}
```

新增视图文件 `resources/views/admin/article/edit.blade.php`

```php
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">编辑文章</div>
                    <div class="panel-body">

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <strong>编辑失败</strong> 输入不符合要求<br><br>
                                {!! implode('<br>', $errors->all()) !!}
                            </div>
                        @endif

                        <form action="{{ url('admin/article/'.$article->id) }}" method="POST">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}
                            <input type="text" name="title" class="form-control" required="required" placeholder="请输入标题"
                                   value="{{ $article->title }}">
                            <br>
                            <textarea name="body" rows="10" class="form-control" required="required"
                                      placeholder="请输入内容">{{ $article->body }}</textarea>
                            <br>
                            <button class="btn btn-lg btn-info">提交修改</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

视图调用 `return view('admin/article/edit')->withArticle(Article::find($id));` 返回了视图文件。

#### 提交数据到后端

视图 `resources/views/admin/article/edit.blade.php` 中有一个表单，动态生成了一个 URL 作为 action ，并且指定了表单提交需要使用 POST 方法。

#### 后端接收数据

新建 update 方法

```php
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
```

### 删除 Article

删除某个资源跟新增、编辑相比，最大的不同就是运行方式的不同：删除按钮看起来是一个独立的按钮，其实它是一个完整的表单，只不过只有这一个按钮暴露在页面上：

```html
<form action="{{url('admin/article/').$article->id}}" method="post" style="display: inline;">
    {{method_field("DELETE")}}
    {{csrf_field()}}
    <button type="submit" class="btn btn-danger">删除</button>
</form>
```

`{{ method_field('DELETE') }}` 是 Laravel 特有的请求处理系统的特殊约定。

Laravel 的请求处理系统要求所有非 GET 和 POST 的请求全部通过 POST 请求来执行，再将真正的方式使用 _method 表单字段携带给后端代码。 PUT / PATCH 请求也要通过 POST 来执行。

另外， PUT / PATCH 请求通过 POST 方法执行仅限于 form 提交，对 Ajax 请求目前来看是无效的。

在控制器中增加删除文章对应的是 `destroy` 方法：

```php
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
```

---

## 开始构建评论系统

### 基础规划

需要新建一个表专门用来存放数据库，每条评论都属于某一篇文章。评论之间的层级关系比较复杂，本文为入门教程，将“回复别人的评论”暂定为简单的在评论内容前面增加 @john 这样的字符串。

### 建立 Model 类和数据表

创建名为 Comment 的 Model 类，并顺便创建附带的 migration ，在 laravel 目录下运行命令：

```shell
php artisan make:model Comment -m
```

`Model created successfully.`
`Created Migration: 2016_09_11_012245_create_comments_table`

这样一次性建立了 Comment 类和 `XXX_create_comments_table` 两个文件。

修改 migration 文件的 up 方法为：

```php
public function up()
{
    Schema::create('comments', function (Blueprint $table) {
        $table->increments('id');
        $table->string('nickname');
        $table->string('email')->nullable();
        $table->string('website')->nullable();
        $table->text('content')->nullable();
        $table->integer('article_id');
        $table->timestamps();
    });
}
```

运行命令创建数据库中对应的数据表。

```shell
php artisan migrate
```

### 建立“一对多关系”

在 Article 模型中增加一对多关系的函数：

`app/Article.php`

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public function hasManyComments()
    {
        return $this->hasMany('App\Comment', 'article_id', 'id');
    }
}
```

> 模型间关系中文文档：[Eloquent：关联](http://laravel-china.org/docs/5.2/eloquent-relationships) 
>
> 扩展阅读：[深入理解 Laravel Eloquent（三）——模型间关系（关联）](https://lvwenhan.com/laravel/423.html)

### 构建前台 UI

修改前台的视图文件，把评论功能添加进去。

#### 创建前台的 ArticleController 类

运行命令：

```shell
php artisan make:controller ArticleController
```

增加路由：

```php
Route::get('article/{id}', 'ArticleController@show');
```

此处的 `{id}` 指代任意字符串，在本系统的规划中，此字段为文章 ID 为数字，但是本行路由却会尝试匹配所有的请求，*所以当你遇到了奇怪的路由调用的方法跟你想象的不一样时，记得检查路由顺序*。路由匹配方式为前置匹配：任何一条路由规则匹配成功，会立刻返回结果，后面的路由便没有了机会。

给 ArticleController 增加 show 函数：

```php
<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;

use App\Http\Requests;

class ArticleController extends Controller
{
    public function show($id)
    {
        return view('article/show')->withArticle(Article::with('hasManyComments')->find($id));
    }
}
```

#### 创建前台文章展示视图

新建 `resources/views/article/show.balde.php` 文件：

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Learn Laravel 5</title>

    <link href="//cdn.bootcss.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</head>

<div id="content" style="padding: 50px;">

    <h4>
        <a href="/"><< 返回首页</a>
    </h4>

    <h1 style="text-align: center; margin-top: 50px;">{{ $article->title }}</h1>
    <hr>
    <div id="date" style="text-align: right;">
        {{ $article->updated_at }}
    </div>
    <div id="content" style="margin: 20px;">
        <p>
            {{ $article->body }}
        </p>
    </div>

    <div id="comments" style="margin-top: 50px;">

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>操作失败</strong> 输入不符合要求<br><br>
                {!! implode('<br>', $errors->all()) !!}
            </div>
        @endif

        <div id="new">
            <form action="{{ url('comment') }}" method="POST">
                {!! csrf_field() !!}
                <input type="hidden" name="article_id" value="{{ $article->id }}">
                <div class="form-group">
                    <label>Nickname</label>
                    <input type="text" name="nickname" class="form-control" style="width: 300px;" required="required">
                </div>
                <div class="form-group">
                    <label>Email address</label>
                    <input type="email" name="email" class="form-control" style="width: 300px;">
                </div>
                <div class="form-group">
                    <label>Home page</label>
                    <input type="text" name="website" class="form-control" style="width: 300px;">
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" id="newFormContent" class="form-control" rows="10"
                              required="required"></textarea>
                </div>
                <button type="submit" class="btn btn-lg btn-success col-lg-12">Submit</button>
            </form>
        </div>

        <script>
            function reply(a) {
                var nickname = a.parentNode.parentNode.firstChild.nextSibling.getAttribute('data');
                var textArea = document.getElementById('newFormContent');
                textArea.innerHTML = '@' + nickname + ' ';
            }
        </script>

        <div class="conmments" style="margin-top: 100px;">
            @foreach ($article->hasManyComments as $comment)

                <div class="one" style="border-top: solid 20px #efefef; padding: 5px 20px;">
                    <div class="nickname" data="{{ $comment->nickname }}">
                        @if ($comment->website)
                            <a href="{{ $comment->website }}">
                                <h3>{{ $comment->nickname }}</h3>
                            </a>
                        @else
                            <h3>{{ $comment->nickname }}</h3>
                        @endif
                        <h6>{{ $comment->created_at }}</h6>
                    </div>
                    <div class="content">
                        <p style="padding: 20px;">
                            {{ $comment->content }}
                        </p>
                    </div>
                    <div class="reply" style="text-align: right; padding: 5px;">
                        <a href="#new" onclick="reply(this);">回复</a>
                    </div>
                </div>

            @endforeach
        </div>
    </div>

</div>

</body>
</html>
```

### 构建评论存储功能

创建一个 CommentController 控制器，并增加一条“存储评论”的路由。

运行命令：

```shell
php artisan make:controller CommentController
```

控制器创建成功，增加一条路由：

```php
Route::post('comment', 'CommentController@store');
```

给控制器类增加 store 函数：

```php
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
```

#### 批量赋值

采用批量赋值方法来减少存储评论的代码。

另参见：[批量赋值](http://laravel-china.org/docs/5.2/eloquent#批量赋值)

给 Comment 类增加 $fillable 成员变量：

`app/Comment.php`

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['nickname', 'email', 'website', 'content', 'article_id'];
}
```

前台评论功能构建完成。

## 构建后台评论管理功能

页面效果如下：

**管理评论**

![管理评论](http://7xlmi4.dl1.z0.glb.clouddn.com/2016-06-03-14649668823242.jpg)

**编辑评论**

![编辑评论](http://7xlmi4.dl1.z0.glb.clouddn.com/2016-06-03-14649668944304.jpg)



