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

namespace Maskphp;

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
 * site root
 * ex: http://maskgroup.com, http://localhost/maskphp/,...
 */
defined('SITE_ROOT') OR define('SITE_ROOT', trim(PROTOCOL . '://' . DOMAIN . (PORT == '80' ? '' : ':' . PORT) . preg_replace('/(index|console).php$/i', '', $_SERVER['PHP_SELF']), '/') . '/');

/**
 * Http referer
 * ex: http://sukienhay.com/
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

/**
 * is ajax method
 */
defined('IS_AJAX') OR define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') == 0);
defined('IS_AJAX_POST') OR define('IS_AJAX_POST', IS_AJAX && IS_POST);
defined('IS_AJAX_GET') OR define('IS_AJAX_GET', IS_AJAX && IS_GET);

/**
 * empty value
 */
defined('EMPTY_VALUE') OR define('EMPTY_VALUE', '__M::EMPTY::M__' . APP_TIME_START);