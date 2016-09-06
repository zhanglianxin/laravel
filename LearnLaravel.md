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

