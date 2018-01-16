<?php
namespace zongphp\route;

use zongphp\config\Config;
use zongphp\framework\build\Provider;

class RouteProvider extends Provider {
	//延迟加载
	public $defer = false;

	public function boot() {
		//解析路由
		require ROOT_PATH . '/system/routes.php';
		Route::dispatch();
	}

	public function register() {
		Config::set( 'controller.app', Config::get( 'app.path' ) );
		Config::set( 'route.cache', Config::get( 'http.route_cache' ) );
		Config::set( 'route.mode', Config::get( 'http.route_mode' ) );
		$this->app->single( 'Route', function () {
			return Route::single();
		} );
	}
}