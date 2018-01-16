<?php
namespace zongphp\route;

use zongphp\framework\build\Facade;

class RouteFacade extends Facade {
	public static function getFacadeAccessor() {
		return 'Route';
	}
}