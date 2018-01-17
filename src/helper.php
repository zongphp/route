<?php
if ( ! function_exists('action')) {
    /**
     * 执行控制器方法
     *
     * @param       $controller
     * @param       $action
     *
     * @return mixed
     */
    function action($controller, $action)
    {
        return \zongphp\route\Route::executeControllerAction($controller.'@'.$action);
    }
}
