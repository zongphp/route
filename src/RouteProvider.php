<?php
namespace zongphp\route;

use zongphp\framework\build\Provider;

class RouteProvider extends Provider
{
    use Csrf;
    //延迟加载
    public $defer = false;

    public function boot()
    {
        Config::set('controller.app', Config::get('app.path'));
        Config::set('route.cache', Config::get('http.route_cache'));
        //CSRF验证
        $this->csrfCheck();
    }

    public function register()
    {
        $this->app->single('Route', function () {
            return Route::single();
        });
    }
}
