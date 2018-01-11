<?php
namespace zongphp\route;

use zongphp\route\controller\Message;
use Code;

/**
 * 控制器基础类
 * Class Controller
 *
 * @package zongphp\route
 */
abstract class Controller
{
    use Message;

    /**
     * 验证码
     */
    final public function captcha()
    {
        Code::make();
    }

    /**
     * 404 NotFound
     *
     * @return mixed
     */
    final public function _404($return = false)
    {
        return Response::_404($return);
    }
}
