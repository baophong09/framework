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

/**
 * core namespace
 */
define('CORE_NAMESPACE', '\MaskPHP\\');

/**
 * application time start
 */
defined('APP_TIME_START') OR define('APP_TIME_START', microtime(true));

/**
 * path seperator
 * ex: windows -> \ | linux -> /
 */
defined('DS') OR define('DS', DIRECTORY_SEPARATOR);

/**
 * php extension
 */
defined('EXT') OR define('EXT', '.php');

/**
 * root path
 * ex: /public_html/,...
 */
defined('ROOT_PATH') OR define('ROOT_PATH', dirname(getcwd()) . DS);

/**
 * vendor path
 * ex: /public_html/vendor/,...
 */
defined('VENDOR_PATH') OR define('VENDOR_PATH', ROOT_PATH . 'vendor' . DS);

/**
 * core path
 * ex: /public_html/vendor/maskphp/framework/src/MaskPHP/,...
 */
defined('CORE_PATH') OR define('CORE_PATH', VENDOR_PATH . 'maskphp' . DS . 'framework' . DS . 'src' . DS . 'MaskPHP' . DS);

/**
 * applictaion path
 * ex: /public_html/app,...
 */
defined('APP_PATH') OR define('APP_PATH', ROOT_PATH . 'app' . DS);

/**
 * cache path
 * ex: /public_html/app/cache/,...
 */
defined('CACHE_PATH') OR define('CACHE_PATH', APP_PATH . 'cache' . DS);

/**
 * module path
 * ex: /public_html/app/module/,...
 */
defined('MODULE_PATH') OR define('MODULE_PATH', APP_PATH . 'module' . DS);

/**
 * config path
 * ex: /public_html/app/config/,...
 */
defined('CONFIG_PATH') OR define('CONFIG_PATH', APP_PATH . 'config' . DS);

/**
 * library path
 * ex: /public_html/app/library/,...
 */
defined('LIBRARY_PATH') OR define('LIBRARY_PATH', APP_PATH . 'library' . DS);

/**
 * public path
 * ex: /public_html/public/,...
 */
defined('PUBLIC_PATH') OR define('PUBLIC_PATH', ROOT_PATH . 'public' . DS);

/**
 * theme path
 * ex: /public_html/public/theme/,...
 */
defined('THEME_PATH') OR define('THEME_PATH', PUBLIC_PATH . 'theme' . DS);

/**
 * media path
 * ex: /public_html/public/media/,...
 */
defined('MEDIA_PATH') OR define('MEDIA_PATH', PUBLIC_PATH . 'media' . DS);

/**
 * domain
 * ex: localhost, maskphp.com, demo.maskphp.com,...
 */
if(!isset($_SERVER['SERVER_NAME'])){
    defined('DOMAIN') OR define('DOMAIN', 'localhost');
}else{
    defined('DOMAIN') OR define('DOMAIN', isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] != $_SERVER['SERVER_NAME'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);
}

/**
 * protocol
 * ex: http, https,...
 */
defined('PROTOCOL') OR define('PROTOCOL', isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http');

/**
 * port
 * ex: 80, 8080,...
 */
defined('PORT') OR define('PORT', isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80);

/**
 * site path
 * ex: /public_html
 */
$php_self = substr($_SERVER['PHP_SELF'], 0, strripos($_SERVER['PHP_SELF'], DS));
$site_path = '';
if(substr_count($php_self, DS) >= 2){
	$site_path = $php_self;
}
defined('SITE_PATH') OR define('SITE_PATH', $site_path);

/**
 * site root
 * ex: http://maskphp.com, http://localhost/maskphp/,...
 */
defined('SITE_ROOT') OR define('SITE_ROOT', trim(PROTOCOL . '://' . DOMAIN . (PORT == '80' ? '' : ':' . PORT) . SITE_PATH, '/') . '/');

/**
 * Http referer
 * ex: http://maskphp.com/
 */
defined('HTTP_REFERER') OR define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

/**
 * server IP
 */
defined('SERVER_IP') OR define('SERVER_IP', isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1');

/**
 * client IP
 */
if(!isset($_SERVER['REMOTE_ADDR'])){
    defined('CLIENT_IP') OR define('CLIENT_IP', '127.0.0.1');
}else{
    defined('CLIENT_IP') OR define('CLIENT_IP', isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']));
}

/**
 * CLIENT LANGUAGE
 */
defined('CLIENT_LANG') OR define('CLIENT_LANG', strtolower(substr(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : (isset($_SERVER['LANG']) ? $_SERVER['LANG'] : 'en'), 0, 2)));

/**
 * check is windows server
 */
defined('IS_WINDOWS') OR define('IS_WINDOWS', strncasecmp(PHP_OS, 'WIN', 3) == 0 ? true : false);

/**
 * is local mode
 */
defined('IS_LOCAL') OR define('IS_LOCAL', CLIENT_IP === SERVER_IP);

/**
 * request method
 */
defined('IS_POST') OR define('IS_POST', isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST');
defined('IS_GET') OR define('IS_GET', isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET');
defined('IS_PUT') OR define('IS_PUT', isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'PUT');
defined('IS_DELETE') OR define('IS_DELETE', isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'DELETE');
defined('IS_HEAD') OR define('IS_HEAD', isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'HEAD');
defined('IS_OPTIONS') OR define('IS_OPTIONS', isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS');

/**
 * is ajax method
 */
defined('IS_AJAX') OR define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') == 0);
defined('IS_AJAX_POST') OR define('IS_AJAX_POST', IS_AJAX && IS_POST);
defined('IS_AJAX_GET') OR define('IS_AJAX_GET', IS_AJAX && IS_GET);
defined('IS_AJAX_PUT') OR define('IS_AJAX_PUT', IS_AJAX && IS_PUT);
defined('IS_AJAX_DELETE') OR define('IS_AJAX_DELETE', IS_AJAX && IS_DELETE);
defined('IS_AJAX_HEAD') OR define('IS_AJAX_HEAD', IS_AJAX && IS_HEAD);
defined('IS_AJAX_OPTIONS') OR define('IS_AJAX_OPTIONS', IS_AJAX && IS_OPTIONS);

/**
 * empty value
 */
defined('EMPTY_VALUE') OR define('EMPTY_VALUE', '__M::EMPTY::M__' . APP_TIME_START);