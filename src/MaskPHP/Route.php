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
 * | @require       : PHP version >= 5.3.0                                  |
 * +------------------------------------------------------------------------+
 */

namespace MaskPHP;

class Route extends Base{
	const
		MAP_URI_PATTERN 		= 'module/hook_module/group_controller/controller/action';

	public
		$default_module 		= 'helloworld',
		$default_url 			= 'default/index/index',
		$default_error 			= 'default/error/404',
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
		$controller 			= 'index',
		$action 				= 'index',

		$module_path 			= null,
		$hook_path 				= null;

	/**
	 * parsing request... then response result to client
	 */
	public function response(){
		ob_start();
			// load default config
			require_once CONFIG_PATH . 'default.php';

			// system on load
			M::event()->trigger('system.on_load');

			// system on shutdown
			register_shutdown_function(function(){
				M::event()->trigger('system.on_shutdown');
			});

			// autoload class
			/*
			spl_autoload_register(function($cls){
				$file = $cls . EXT;
				if(!class_exists($cls)){
					if(($f = get_readable(MODULE_PATH . $file)) || ($f = get_readable(LIBRARY_PATH . $file)) || ($f = get_readable(APP_PATH . $file))){
						//M::load($cls, $f);
						M::import($f, false);
					}
				}
			});
			*/


			// parser url
			$this->parseURL();
			// load module
			$this->loadModule();
			// load controller
			//$this->load_controller();
			// load action
			//$this->load_action();

			echo '<pre>';
			print_r($this);
			echo '</pre>';
		$html = ob_get_clean();

		// on response
		ob_start();
		//M::event()->change('system.on_response', $html);

		// display html & end all script
		die($html);
	}

	/**
	 * load module
	 */
	private function loadModule(){
		$first = array_shift($this->uri_segment);
		if($first && M::getPath(MODULE_PATH . $first . DS)){
			$this->module = $first;
		}else{
			$this->module = $this->default_module;
			if($first){
				array_unshift($this->uri_segment, $first);
			}
		}

		$this->module_path = MODULE_PATH . $this->module . DS;
		M::event()->trigger('router.on_get_module', array(&$this->module, &$this->module_path, $this));

		if(!M::getPath($this->module_path)){
			M::exception('M::router()->loadModule(...) : Module "%s" dose not exist. Path: "%s"', array($this->module, $this->module_path));
		}

		// load bootstrap & configs
		M::import(array($this->module_path . 'bootstrap' . EXT, $this->module_path . 'config' . EXT), false);

		// check load module hook
		if(!($first = array_shift($this->uri_segment))){
			return $this;
		}

		// check allow hook module
		M::event()->trigger('router.allow_hook_module', array(&$this->allow_hook_module, $this->module, $this));
		$allow = array();
		foreach($this->allow_hook_module as $k => $v){
			if(strcasecmp($k, $this->module) == 0){
				$allow = $v;
			}
		}

		$check = false;
		foreach($allow as $v){
			if(strcasecmp($v, $first) == 0){
				$check = true;

				// load hook module
				$this->hook_module = $first;
				$this->hook_path = MODULE_PATH . $this->hook_module . DS . 'hook' . DS . $this->module . DS;
				M::event()->trigger('router.on_get_hook_module', array(&$this->hook_module, &$this->hook_path, $this));
				if(!M::getPath($this->hook_path)){
					M::exception('M::router()->load_hook_module(...) : Hook module "%s" dose not exist. Path: "%s"',
						array($this->hook_module, $this->hook_path));
				}

				// load bootstrap & configs
				M::import(array($this->hook_path . 'bootstrap' . EXT, $this->hook_path . 'config' . EXT), false);
			}
		}

		if(!$check){
			array_unshift($this->uri_segment, $first);
		}

		return $this;
	}

	/**
	 * parser request url
	 */
	private function parseURL(){
		// on get domain & load config by domain
		M::event()->trigger('router.on_get_domain', array(DOMAIN, &$this));
		M::import(CONFIG_PATH . DOMAIN . EXT, false);

		// get request uri
		$this->request_uri = M::trimSlash($_SERVER['REQUEST_URI']);

		// get query string
		$this->query_string = $_SERVER['QUERY_STRING'];

		// get uri
		$this->uri = trim(substr($this->request_uri, 0, strlen($this->request_uri) - strlen($this->query_string)), '?');
		// remove url extension
		$uri = preg_replace('#' . $this->url_extension . '#', '', $this->uri);
		M::event()->trigger('router.on_get_uri', array(&$uri));

		// get uri segement
		if($uri){
			$this->uri_segment = explode('/', $uri);
		}

		// map query string into uri segment
		M::event()->trigger('router.use_query_param', array(&$this->use_query_param));
		if($this->use_query_param){
			$this->mapUriSegment($_GET);
		}

		// on get uri segment
		M::event()->trigger('router.on_get_uri_segment', array(&$this->uri_segment));

		return $this;
	}

	/**
	 * map args to uri segment
	 * @param  array $args
	 */
	public function mapUriSegment($args){
		$map_uri = $this->getUriPattern();

		foreach((array)$args as $k => $v){
			if(array_key_exists(M::trimLower($k), $map_uri)){
				$map_uri[$k] = $v;
			}
		}

		foreach($map_uri as $v){
			if(!$v){
				continue;
			}
			array_unshift($this->uri_segment, $v);
		}

		return $this;
	}

	/**
	 * convert string uri pattern to array
	 */
	public function getUriPattern(){
		static $uri_pattern = array();
		if(!$uri_pattern){
			foreach(array_reverse(explode('/', self::MAP_URI_PATTERN)) as $v){
				$uri_pattern[$v] = '';
			}
		}

		return $uri_pattern;
	}
}