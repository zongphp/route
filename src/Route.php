<?php
namespace zongphp\route;

use zongphp\route\build\Base;

/**
 * 路由处理类
 * Class Route
 *
 * @package zongphp\route
 */
class Route
{
    protected static $link;

    public function __call($method, $params)
    {
        return call_user_func_array([self::single(), $method], $params);
    }

    public static function single()
    {
        if ( ! self::$link) {
            self::$link = new Base();
        }

        return self::$link;
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([static::single(), $name], $arguments);
    }
}
