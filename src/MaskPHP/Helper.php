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
 * check empty value
 * @param  $var
 */
if(!function_exists('is_empty')){
	function is_empty($var){
	    if($var === EMPTY_VALUE){
	        return true;
	    }

	    return false;
	}
}

/**
 * trim string
 * @param  string &$str
 */
if(!function_exists('trimmer')){
	function trimmer(&$str){
	    return $str = trim(preg_replace('#\s+#', ' ', $str));
	}
}

/**
 * trim & convert string to lower case
 * @param  string &$str
 */
if(!function_exists('trim_lower')){
	function trimLower(&$str){
	    return $str = strtolower(self::trimmer($str));
	}
}

/**
 * trim & convert string to upper case
 * @param  string &$str
 */
if(!function_exists('trim_upper')){
	function trim_upper(&$str){
	    return $str = strtoupper(self::trimmer($str));
	}
}

/**
 * get last string after symbol
 * @param  string $str
 * @param  string $symbol
 */
if(!function_exists('last_string')){
	function last_string($str, $symbol = '/'){
	    return substr(strrchr($str, $symbol), 1);
	}
}

/**
 * get first string before symbol
 * @param  string $str
 * @param  string $symbol
 */
if(!function_exists('fisrt_string')){
	function fisrt_string($str, $symbol = '/'){
	    return strstr($str, $symbol, 1);
	}
}

/**
 * replace multi & trim slash
 * @param  string &$str
 * @param  string $slash
 */
if(!function_exists('trim_slash')){
	function trim_slash(&$str, $slash = '/'){
        return $str = trim(preg_replace("/[\/\\\]+/", $slash, $str), $slash);
    }
}

/**
 * json parse (decode)
 * @param  string  $str
 * @param  boolean $assoc true: array; false: object
 */
if(!function_exists('json_parse')){
	function json_parse($str, $assoc = true){
	    try{
	        return json_decode($str, $assoc);
	    }catch(\Exception $e){
	        return array();
	    }
	}
}

/**
 * get files in folder
 * @param  string $dir
 */
if(!function_exists('get_files')){
    function get_files($dir){
        return get_sub($dir, false);
    }
}

/**
 * get sub folders in folder
 * @param string $dir
 */
if(!function_exists('get_folders')){
	function get_folders($dir){
        return get_sub($dir, true);
    }
}

/**
 * get sub folder | file
 * @param  string $dir
 * @param  boolean $child true: folders; false: files
 */
if(!function_exists('get_sub')){
    function get_sub($dir, $child = null){
        $dir = get_path($dir);
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
}

/**
 * get real path of file or directory
 * @param  string $path
 */
if(!function_exists('get_path')){
    function get_path($path){
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
            }

            return '';
        }

        // for linux
        // check not absolute path
        if(!preg_match('/^\//', $path)){
            $path = ROOT_PATH . $path;
        }

        $path = DS . trim_slash($path);

        $pies = explode(DS, $path);
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
            $exist = false;
            foreach(glob($path . '*', GLOB_ONLYDIR) as $v){
                if(strcasecmp($path . $first, $v) == 0){
                    $path = $v . DS;
                    $exist = true;
                    break;
                }
            }

            if(!$exist){
                return '';
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
            }
        }

        return '';
    }
}

/**
 * excution time
 * @param  float $start
 * @param  float $end
 */
if(!function_exists('excution_time')){
	function excution_time($start, $end = 0){
	    if($end <= 0){
	        $end = microtime(true);
	    }
	    return ($total = (float)($end - $start)) >= 1 ? $total . ' s' : $total*1000 . ' ms';
	}
}

/**
 * memory usage
 * @param  float $size
 */
if(!function_exists('memory_usage')){
	function memory_usage($size = 0){
	    $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

	    if($size <= 0){
	        $size = memory_get_peak_usage();
	    }

	    return round(($size)/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
	}
}