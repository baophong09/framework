<?php
/*
 * +------------------------------------------------------------------------+
 * | MaskPHP - A Light-weight PHP Framework for developer                   |
 * | @package       : MaskPHP                                               |
 * | @authors       : MaskPHP                                               |
 * | @copyright     : Copyright (c) 2016                                    |
 * | @since         : Version 1.0.0                                         |
 * | @website       : http://www.maskphp.com                                |
 * | @email         : support@maskphp.com                                   |
 * | @require       : PHP version >= 5.4.0                                  |
 * +------------------------------------------------------------------------+
 */

namespace MaskPHP;

class Route extends Base{
	const
		MAP_URI_PATTERN 		= 'module/hook_module/group_controller/controller/action';

	public
		$default_module 		= 'Mask',
		$default_request_uri 	= 'index/index',
		$default_error 			= 'error/404',
		$url_extension			= '.html',
		$use_query_param 		= false,
		$allow_hook_module 		= array();

	protected
		$request_uri			= 'null',
		$query_string 			= null,
		$uri 					= null,
		$uri_segment 			= array(),

		$module 				= null,
		$hook_module 			= null,
		$group_controller 		= null,
		$controller 			= null,
		$action 				= null,

		$module_path 			= null,
		$hook_path 				= null;

	/**
	 * parsing request... then response result to client
	 */
	public function response(){
		ob_start();
			// system on load
			M::event()->trigger('system.on_load');

			// system on shutdown
			register_shutdown_function(function(){
				M::event()->trigger('system.on_shutdown');
			});

			// load default config & domain config
			$config = array();
			foreach(M::import(array(CONFIG_PATH . 'default' . EXT, CONFIG_PATH . DOMAIN . EXT), false) as $v){
				if(is_array($v)){
					$config = array_replace_recursive($config, $v);
				}
			}

			// on load system config
			M::event()->trigger('system.on_load_system_config', array(&$config));

			// set global system config
			M::config()->set('system', $config);

			// set route config
			foreach((array)$config['route'] as $k => $v){
				$this->{$k} = $v;
			}

			$this
			// parser url
			->parseURL()
			// load module
			->loadModule()
			// load controller
			->loadController()
			// do action
			->doAction();

			echo '<pre>';
			print_r($this);
			echo '</pre>';
		$html = ob_get_clean();

		// on response
		ob_start();

		// system on response
		M::event()->trigger('system.on_response', array(&$html));

		// display html & end all script
		die($html);
	}

	/**
	 * parser request url
	 */
	private function parseURL(){
		// on get domain
		M::event()->trigger('route.on_get_domain', array(DOMAIN, $this));

		// get request uri
		$this->request_uri = trim(substr($_SERVER['REQUEST_URI'], strlen(SITE_PATH)), '/');
		// on get request uri
		M::event()->trigger('route.on_get_request_uri', array(&$this->request_uri, $this));
		if(!trim_slash($this->request_uri)){
			$this->request_uri = trim_slash($this->default_request_uri);
		}

		// get query string
		if(strpos($this->request_uri, '?') != false){
			$this->query_string = preg_replace('/(.*?)\?([^\?].*)/', '$2', $this->request_uri);
		}
		// on get query string
		M::event()->trigger('route.on_get_query_string', array(&$this->query_string, $this));

		// get query segments
		$query_segment = array();
		if($this->query_string){
			parse_str($this->query_string, $query_segment);
		}

		// get uri
		$this->uri = trim(substr($this->request_uri, 0, strlen($this->request_uri) - strlen($this->query_string)), '?');
		// remove url extension
		$this->uri = preg_replace('#' . $this->url_extension . '#', '', $this->uri);
		// on get uri
		M::event()->trigger('route.on_get_uri', array(&$this->uri, $this));

		// get uri segement
		if($this->uri){
			$this->uri_segment = explode('/', $this->uri);
		}

		// map query string into uri segment
		M::event()->trigger('route.use_query_param', array(&$this->use_query_param, $this));
		if($this->use_query_param){
			$this->mapUriSegment(array_merge($_GET, $query_segment));
		}

		// on get uri segment
		M::event()->trigger('route.on_get_uri_segment', array(&$this->uri_segment, $this));

		return $this;
	}

	/**
	 * load module
	 */
	private function loadModule(){
		$first = array_shift($this->uri_segment);
		if($first && is_dir(get_path(MODULE_PATH . $first . DS))){
			$this->module = $first;
		}else{
			$this->module = $this->default_module;
			if($first){
				array_unshift($this->uri_segment, $first);
			}
		}

		$this->module_path = MODULE_PATH . $this->module . DS;
		M::event()->trigger('route.on_get_module', array(&$this->module, &$this->module_path, $this));

		if(!is_dir(get_path($this->module_path))){
			M::exception('M::route()->loadModule(...) : Module "%s" dose not exist. Path: "%s"', array($this->module, $this->module_path));
		}

		// load bootstrap
		M::import($this->module_path . 'bootstrap' . EXT, false);

		// get system config
		$system_config =& M::config()->get('system');
		// load config
		if(is_array($config = M::import($this->module_path . 'config' . EXT, false))){
			$system_config = array_replace_recursive($system_config, $config);
		}

		// check load module hook
		if(!($first = array_shift($this->uri_segment))){
			return $this;
		}

		// check allow hook module
		M::event()->trigger('route.allow_hook_module', array(&$this->allow_hook_module, $this->module, $this));
		$allow = array();
		foreach($this->allow_hook_module as $k => $v){
			if(strcasecmp($k, $this->module) == 0){
				$allow = $v;
			}
		}

		$hook = false;
		foreach($allow as $v){
			if(strcasecmp($v, $first) == 0){
				$hook = true;
			}
		}

		if($hook){
			// load hook module
			$this->hook_module = $first;
			$this->hook_path = MODULE_PATH . $this->hook_module . DS . 'hook' . DS . $this->module . DS;

			// on get hook module
			M::event()->trigger('route.on_get_hook_module', array(&$this->hook_module, &$this->hook_path, $this));

			if(!is_dir(get_path($this->hook_path))){
				M::exception('M::route()->load_hook_module(...) : Hook module "%s" dose not exist. Path: "%s"', array($this->hook_module, $this->hook_path));
			}

			// load hook config
			if(is_array($config = M::import($this->hook_path . 'config' . EXT, false))){
				$system_config = array_replace_recursive($system_config, $config);
			}
		}else{
			array_unshift($this->uri_segment, $first);
		}

		// dont allow overwrite system config
		M::config()->set('system', $system_config, false);

		return $this;
	}

	/**
	 * load controller
	 */
	private function loadController(){
		// get controller path
		$path = $this->module_path;

		if($this->hook_module){
			$path = $this->hook_path;
		}

		$path .= 'controller' . DS;

		// group controller
		if(($first = array_shift($this->uri_segment)) && is_dir(get_path($path . $first))){
			$this->group_controller = $first;
			$path .= $first . DS;
			$first = array_shift($this->uri_segment);
		}

		// controller
		if($first && is_file(get_path($path . $first . EXT))){
			$this->controller = $first;
		}elseif($first){
			array_unshift($this->uri_segment, $first);
		}

		// check controller
		if(!M::import($path . $this->controller . EXT, false)){
			// avoid loop redirect
			if(!session_id()){
				session_start();
			}

			if(!isset($_SESSION['__ROUTE_ERROR__'])){
				$_SESSION['__ROUTE_ERROR__'] = true;
				$this->error(0);
			}else{
				unset($_SESSION['__ROUTE_ERROR__']);
				M::exception('M::route()->loadController(...) : The requested "%s" was not found on this server.', $this->default_error);
			}
		}

		// load controller
		$cls = '\App\Module\\' . $this->module . '\\' . ($this->group_controller ? $this->group_controller : '') . $this->controller;
		if(!class_exists($cls)){
			$this->error(0);
		}

		$controller = new $cls;
		// on get controller
		M::event()->trigger('route.on_get_controller', array(&$controller, $this));

		// register instance
		M::get_instance($controller);

		return $this;
	}

	/**
	 * do action
	 */
	private function doAction(){
		// get controller
		$controller = M::get_instance();
		$this->action = array_shift($this->uri_segment);

		// check method exist | is public
		if(!method_exists($controller, $this->action) || !(new \ReflectionMethod($controller, $this->action))->isPublic()){
			$this->error(1);
		}

		// on get action
		M::event()->trigger('route.on_get_action', array(&$this->action, $this));

		// do action
		call_user_func_array(array($controller, $this->action), $this->uri_segment);

		return $this;
	}

	/**
	 * handle error
	 */
	private function error($err_number = 0){
		$error = array(
			'CONTROLLER_NOT_FOUND',
			'ACTION_NOT_FOUND'
		);
		// on error controller
		M::event()->trigger('route.on_error', array($error[$err_number], $this));
		redirect($this->default_error);
	}

	/**
	 * map args to uri segment
	 * @param  array $args
	 */
	public function mapUriSegment($args){
		// map uri
		$map_uri = $this->getUriPattern(true);
		foreach((array)$args as $k => $v){
			if(array_key_exists(trim_lower($k), $map_uri)){
				$map_uri[$k] = $v;
			}
		}

		// remove uri segment
		foreach($map_uri as $v){
			if(!$v){
				continue;
			}
			array_unshift($this->uri_segment, $v);
		}

		return $map_uri;
	}

	/**
	 * get uri pattern
	 * @param  boolean $revert
	 */
	public function getUriPattern($revert = false){
		$pattern = explode('/', self::MAP_URI_PATTERN);

		if($revert){
			$pattern = array_reverse($pattern);
		}

		$uri_pattern = array();
		foreach($pattern as $v){
			$uri_pattern[$v] = '';
		}

		return $uri_pattern;
	}

	/**
	 * build url
	 * @param  string|array $args
	 * @param  boolean $use_query
	 * @param  string $extension
	 */
	public function renderUrl($args, $use_query = false, $extension = '.html'){
		// get uri pattern
		$map = $this->getUriPattern();

		foreach($args as $k => $v){
			$key = strtolower($k);
			if(isset($map[$key])){
				$map[$key] = $v;
				unset($args[$k]);
			}
		}

		$url = '';
		foreach($map as $v){
			if($v){
				$url .= $v . '/';
			}
		}
		$url = trim($url, '/');

		if(!$use_query){
			foreach($args as $v){
				$url .= '/' . $v;
			}
			$url .= $extension;
		}else{
			$url .= $extension;

			if($args){
				$url .= '?';
				foreach($args as $v){
					$url .= $k . '=' . $v . '&';
				}
				$url = trim($url, '&');
			}
		}

		return $url;
	}
}