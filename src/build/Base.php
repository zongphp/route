<?php
namespace zongphp\route\build;

use zongphp\cache\Cache;
use zongphp\config\Config;
use zongphp\controller\Controller;
use zongphp\middleware\Middleware;
use zongphp\request\Request;

/**
 * 路由处理类
 * Class Route
 */
class Base extends Compile {
	use Method;
	//路由定义
	public $route = [ ];
	//请求的URI
	protected $requestUri;
	//路由缓存
	protected $cache = [ ];
	//正则替换字符
	protected $patterns = [
		':num' => '[0-9]+',
		':all' => '.*',
	];

	//请求地址
	protected function getRequestUri() {
			$REQUEST_URI = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
      $REQUEST_URI = str_replace($_SERVER['SCRIPT_NAME'], '', $REQUEST_URI);

      return trim($REQUEST_URI, '/');
	}

	/**
	 * 使用正则表达式限制参数
	 *
	 * @param mixed $name
	 * @param string $regexp
	 *
	 * @return $this
	 */
	public function where( $name, $regexp = '' ) {
		if ( is_array( $name ) ) {
			foreach ( $name as $k => $v ) {
				$this->route[ count( $this->route ) - 1 ]['where'][ $k ] = '#^' . $v . '$#';
			}
		} else {
			$this->route[ count( $this->route ) - 1 ]['where'][ $name ] = '#^' . $regexp . '$#';
		}

		return $this;
	}

	/**
	 * 解析标签
	 * @return bool|void
	 */
	public function dispatch() {
		//请求URL
		$this->requestUri = $this->getRequestUri();
		//设置路由缓存
		if ( Config::get( 'route.cache' ) && ( $route = Cache::get( '_ROUTES_' ) ) ) {
			$this->route = $route;
		} else {
			$this->route = $this->parseRoute();
		}
		//匹配路由
		foreach ( $this->route as $key => $route ) {
			$method = '_' . $route['method'];
			$this->$method( $key );
			if ( $this->found ) {
				return;
			}
		}
		/**
		 * 控制器处理
		 * 当请求参数为空或者不存在访问控制器的GET变量s为路由解析失败执行ROUTER_NOT_FOUND中间件
		 * 否者执行控制器处理
		 */
		$requestUrl = trim( preg_replace( '#\w+\.php#i', '', $_SERVER['REQUEST_URI'] ), '/' );
		$scriptName = trim( preg_replace( '#\w+\.php#i', '', $_SERVER['SCRIPT_NAME'] ), '/' );
		//执行控制器处理
		if ( Config::get( 'route.mode' ) || ( $requestUrl == $scriptName || Request::get( Config::get( 'http.url_var' ) ) ) ) {
			Controller::run();
		} else {
			//路由解析失败,控制器执行条件不满足时执行中间件
			Middleware::system( 'router_not_found' );
		}
	}

	/**
	 * 解析路由
	 */
	protected function parseRoute() {
		/**
		 * 为每一条路由规则生成正则表达式缓存
		 * 同时解析路由中的{name}等变量
		 * @var [type]
		 */
		foreach ( $this->route as $key => $value ) {
			//原始路由数据
			$regexp = $value['route'];
			//将:all等符号替换为标签路由字符
			if ( strpos( $regexp, ':' ) !== false ) {
				//替换正则符号
				$regexp = str_replace( array_keys( $this->patterns ), array_values( $this->patterns ), $regexp );
			}
			//将{name?}等替换为(.*?)形式
			preg_match_all( '#\{(.*?)(\?)?\}#', $regexp, $args, PREG_SET_ORDER );
			foreach ( $args as $i => $ato ) {
				//存在$ato[2]表示存在{name?}中的问号，用来设置正则中的?
				$has = isset( $ato[2] ) ? $ato[2] : '';
				if ( $has ) {
					//有{.*?}问号，表示变量是可选的，前面加? 组合成/? 形式
					//要不没变量时会多一个/
					$regexp = str_replace( $ato[0], '?([^/]+?)' . $has, $regexp );
				} else {
					$regexp = str_replace( $ato[0], '([^/]+?)' . $has, $regexp );
				}
			}
			$this->route[ $key ]['regexp'] = '#^' . $regexp . '$#';
			$this->route[ $key ]['args']   = $args;
		}
		//缓存路由
		if ( Config::get( 'route.cache' ) ) {
			Cache::set( '_ROUTES_', $this->route );
		}

		return $this->route;
	}

	/**
	 * 获取路由参数
	 *
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function input( $name = null ) {
		if ( is_null( $name ) ) {
			return $this->args;
		} else {
			return isset( $this->args[ $name ] ) ? $this->args[ $name ] : null;
		}
	}

	/**
	 * 获取匹配成功的路由规则
	 * @return string
	 */
	public function getMatchRoute() {
		return $this->matchRoute;
	}
}
