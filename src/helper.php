<?php
if ( ! function_exists('doAction')) {
    /**
     * 执行控制器方法
     *
     * @param       $controller
     * @param       $action
     *
     * @return mixed
     */
    function doAction($controller, $action)
    {
        return \zongphp\route\Route::executeControllerAction($controller.'@'.$action);
    }
}
