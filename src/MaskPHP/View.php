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

interface Template{
	/**
	 * __construct
	 * @param  array $args
	 */
	public function __construct($args);

	/**
	 * load content
	 * @param  string $view
	 * @param  array $args
	 */
	public function display($view, &$args);
}

class View extends Base{
	public $default_layout;

	function theme(){}

	function layout(){}

	public function display(){
		$current_path = MODULE_PATH . 'mask/view/index/';
		$view_path 	= MODULE_PATH . 'mask/view/';
		$theme_path	= THEME_PATH . 'mask/';
		$cache_path = CACHE_PATH . 'view/';
		$data 		= array('name' => 'Fabien');

		// load view
		$loader = new \Twig_Loader_Filesystem($current_path);
		$loader->addPath($view_path, 'view');
		$loader->addPath($theme_path, 'theme');
		$twig = new \Twig_Environment($loader);
		$html_view = $twig->render('index.tpl', $data);

		// load theme
		$loader = new \Twig_Loader_Filesystem($theme_path);
		$twig = new \Twig_Environment($loader);
		$html_theme = $twig->render('index.tpl', $data);

		// extends layout
		$loader = new \Twig_Loader_Chain(array( 
			new \Twig_Loader_Array(array(
				'layout'	=> file_get_contents($theme_path . 'index.tpl'),
				'view'		=> "{% extends \"layout\" %}{% block content %}$html_view{% endblock %}"
			))
		));

		$twig = new \Twig_Environment($loader);
		echo $twig->render('view', $data);
		die;


		/*
		$loader2 = new \Twig_Loader_Array(array(
			'base.html' => file_get_contents($theme_path . 'index.tpl'),
		    'index.html' => '{% extends "base.html" %}' . file_get_contents($current_path . 'index.tpl')
		));

		/*
		$twig = new \Twig_Environment($loader);
		echo $twig->render('index.tpl', $data);
		*/
		 

		$loader1 = new \Twig_Loader_Filesystem($theme_path);
		$loader2 = new \Twig_Loader_Filesystem($current_path);
		

		$loader = new \Twig_Loader_Chain(array($loader1, $loader2));
		$twig = new \Twig_Environment($loader);
		$twig->loadTemplate('index.tpl');
		echo $twig->display('index.tpl');

		// get egine
		// get theme
		// get view
		// get cache path


		// get view
		

		echo '<pre>';
			print_r($this);
		echo '</pre>';

	}
}