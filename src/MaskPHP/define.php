<?php
/*
 * +------------------------------------------------------------------------+
 * | MaskPHP - A Light-weight PHP Framework for developer                   |
 * | @package       : MaskPHP                                               |
 * | @authors       : MaskPHP                                               |
 * | @copyright     : Copyright (c) 2015, MaskPHP                           |
 * | @since         : Version 1.0.0                                         |
 * | @website       : http://maskphp.com                                    |
 * | @e-mail        : support@maskphp.com                                   |
 * | @require       : PHP version >= 5.3.0                                  |
 * +------------------------------------------------------------------------+
 */

/**
 * application time start
 */
define('APP_TIME_START', microtime(true));

/**
 * path seperator
 * ex: windows -> \ | linux -> /
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * php extension
 */
define('EXT', '.php');

/**
 * application path
 * ex: /public_html/,...
 */
define('APP_PATH', getcwd() . DS);

/**
 * system path
 * ex: /public_html/system/
 */
define('SYSTEM_PATH', APP_PATH . 'system' . DS);

/**
 * module path
 * ex: /public_html/module/
 */
define('MODULE_PATH', APP_PATH . 'module' . DS);

/**
 * config path
 * ex: /public_html/library/
 */
define('CONFIG_PATH', APP_PATH . 'config' . DS);

/**
 * library path
 * ex: /public_html/library/
 */
define('LIBRARY_PATH', APP_PATH . 'library' . DS);

/**
 * theme path
 * ex: /public_html/theme/
 */
define('THEME_PATH', APP_PATH . 'theme' . DS);

/**
 * cache path
 * ex: /public_html/theme/
 */
define('CACHE_PATH', APP_PATH . 'cache' . DS);

/**
 * domain
 * ex: localhost, maskphp.com, demo.maskphp.com,...
 */
define('DOMAIN', isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] != $_SERVER['SERVER_NAME'] ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);

/**
 * protocol
 * ex: http, https,...
 */
define('PROTOCOL', isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http');

/**
 * port
 * ex: 80, 8080,...
 */
define('PORT', $_SERVER['SERVER_PORT']);

/**
 * site root
 * ex: http://maskgroup.com, http://localhost/maskphp/,...
 */
define('SITE_ROOT', PROTOCOL . '://' . DOMAIN . (PORT === '80' ? '' : ':' . PORT) . preg_replace('/index.php$/i', '', $_SERVER['PHP_SELF']));

/**
 * Http referer
 * ex: http://sukienhay.com/
 */
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

/**
 * server IP & client IP
 */
define('SERVER_IP', isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1');
define('CLIENT_IP', isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']));

/**
 * CLIENT LANGUAGE
 */
define('CLIENT_LANG', strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)));

/**
 * check is windows server
 */
define('IS_WINDOWS', strncasecmp(PHP_OS, 'WIN', 3) == 0 ? true : false);

/**
 * is local mode
 */
define('IS_LOCAL', CLIENT_IP === SERVER_IP);

/**
 * request method
 */
define('IS_POST', $_SERVER['REQUEST_METHOD'] === 'POST');
define('IS_GET', $_SERVER['REQUEST_METHOD'] === 'GET');

/**
 * is ajax method
 */
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strcasecmp($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') == 0);
define('IS_AJAX_POST', IS_AJAX && IS_POST);
define('IS_AJAX_GET', IS_AJAX && IS_GET);

/**
 * empty value
 */
define('EMPTY_VALUE', '__M::EMPTY::M__' . APP_TIME_START);

/**
 * check empty value
 * @param  $var
 */
function is_empty($var){
    if($var === EMPTY_VALUE){
        return true;
    }

    return false;
}

/**
 * trim & convert string to lower|upper case
 * @param  string &$str
 * @param  boolean $case
 */
function &trimmer(&$str, $case = null){
    $str = trim(preg_replace('#\s+#', ' ', $str));
    if($case === null){
        return $str;
    }

    if(!$case){
        $str = strtolower($str);
    }else{
        $str = strtoupper($str);
    }

    return $str;
}

/**
 * trim & convert string to lower case
 * @param  string &$str
 */
function trim_lower(&$str){
    return trimmer($str, false);
}

/**
 * trim & convert string to upper case
 * @param  string &$str
 */
function trim_upper(&$str){
    return trimmer($str, true);
}

/**
 * replace multi & trim slash
 * @param  string &$str
 * @param  string $slash
 */
function trim_slash(&$str, $slash = '/'){
    return $str = trim(preg_replace("/[\/\\\]+/", $slash, $str), $slash);
}

/**
 * get string last
 * @param  string $str
 * @param  string $symbol
 */
function get_string_last($str, $symbol = '/'){
    return substr(strrchr($str, $symbol), 1);
}

/**
 * get string first
 * @param  string $str
 * @param  string $symbol
 */
function get_string_first($str, $symbol = '/'){
    return strstr($str, $symbol, 1);
}

/**
 * json parse (decode)
 * @param  string  $str
 * @param  boolean $assoc true: array; false: object
 */
function json_parse($str, $assoc = true){
    try{
        return json_decode($str, $assoc);
    }catch(\Exception $e){
        return array();
    }
}

/**
 * get files in folder
 * @param  string $dir
 */
function get_file($dir){
    return get_sub($dir, false);
}

/**
 * get sub folders in folder
 * @param string $dir
 */
function get_folder($dir){
    return get_sub($dir, true);
}

/**
 * get sub folder | file
 * @param  string $dir
 * @param  boolean $child true: folders; false: files
 */
function get_sub($dir, $child = null){
    $dir = get_realpath($dir);
    $sub = array();

    if($child === null){
        $sub = glob($dir . '*');
    }elseif($child){
        $sub = glob($dir . '*', GLOB_ONLYDIR);
    }else{
        $sub = glob($dir . '*');
        foreach($sub as $k => $v){
            if(!is_file($v)){
                unset($sub[$k]);
            }
        }
    }

    return $sub;
}

/**
 * get realpath
 * @param  string $path
 * @param  string $root_path
 */
function get_realpath($path){
    if(!($path = trim($path))){
        return '';
    }

    // for windows
    if(IS_WINDOWS){
        if(trim_slash($path, DS) && (is_readable($path) || is_readable($path = APP_PATH . $path))){
            // file type
            if(is_file($path)){
                return $path;
            }
            // directory type
            else{
                return rtrim($path, DS) . DS;
            }
        }else{
            return '';
        }
    }

    // for linux
    if(!preg_match('/^(\/|\\\)(.*?)/', $path)){
        $path = APP_PATH . trim_slash($path);
    }else{
        $path = DS . trim_slash($path);
    }

    if($path == DS){
        return DS;
    }

    $pies = explode(DS, rtrim($path, DS));
    array_shift($pies);
    $last = array_pop($pies);

    // get parent folder
    $path = DS;
    do{
        $first  = array_shift($pies);
        $sub = glob($path . '*', GLOB_ONLYDIR);

        // dont have permission to access
        if(!$sub){
            $path .= $first . DS;
            continue;
        }

        // have permission to access
        foreach(glob($path . '*', GLOB_ONLYDIR) as $v){
            if(strcasecmp($path . $first, $v) == 0){
                $path = $v . DS;
                break;
            }
        }
    }while($pies);

    // check exist
    foreach(glob($path . '*') as $v){
        if(strcasecmp($path . $last, $v) == 0){
            if(is_file($v)){
                return $v;
            }else{
                return rtrim($v, DS) . DS;
            }
            return $v;
            break;
        }
    }

    return '';
}

/**
 * excution time
 * @param  float $start
 * @param  float $end
 */
function excution_time($start, $end = 0){
    if($end <= 0){
        $end = microtime(true);
    }
    return ($total = (float)($end - $start)) >= 1 ? $total . ' s' : $total*1000 . ' ms';
}

/**
 * memory usage
 * @param  float $size
 */
function memory_usage($size = 0){
    $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

    if($size <= 0){
        $size = memory_get_peak_usage();
    }

    return round(($size)/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}