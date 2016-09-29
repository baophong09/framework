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

abstract class M{
    const CORE_NAMESPACE = 'MaskPHP';

    /**
     * get core
     */
    public static function __callStatic($name, $args){
        $cls = self::CORE_NAMESPACE . '\\' . $name;
        return self::load($cls, $cls, CORE_PATH . $name . EXT, $args, false);
    }

    /**
     * get library
     * @param  string $name
     */
    public static function get($name){
        return self::load($name, null, null, null, false);
    }

    /**
     * register library
     * @param  string | array  $name
     * @param  string | object  $resource
     * @param  string  $class
     * @param  array   $args
     */
    public static function register($name, $resource = '', $class = '', $args = array()){
        $libs = array();
        if(!is_array($name)){
            $libs[] = array('name' => $name, 'resource' => $resource, 'class' => $class, 'args' => $args);
        }else{
            $libs = (array)$name;
        }

        foreach($libs as $v){
            self::load(
                $v['name']
                , isset($v['class']) ? $v['class'] : ''
                , isset($v['resource']) ? $v['resource'] : ''
                , isset($v['args']) ? $v['args'] : array()
                , true
            );
        }
    }

    /**
     * load library
     * @param  string  $name
     * @param  string  $class
     * @param  string | object  $resource
     * @param  array   $args
     * @param  boolean $register
     */
    public static function load($name, $class = null, $resource = null, $args = null, $register = false){
        static $lib = array();

        // dont allow overwite core
        if(preg_match('/^' . self::CORE_NAMESPACE . '\\\/i', self::trimLower($name))){
            if(!isset($lib[$name])){
                $lib[$name] = array('resource' => $resource, 'class' => $class, 'args' => $args);
            }
        }
        // for register
        elseif($register){
            // overwrite lib
            if(isset($lib[$name])){
                unset($lib[$name]);
            }

            // register lib
            if(!isset($lib[$name])){
                if(is_object($resource)){
                    $lib[$name] = array('resource' => $resource, 'class' => null, 'args' => null);
                    return;
                }

                if(!$resource = self::getPath(self::trimmer($resource))){
                    self::exception('M::load(...) : Library "%s" dose not exist', $name);
                }

                $lib[$name] = array('resource' => $resource, 'class' => $class, 'args' => $args);
            }

            return;
        }

        // check library is defined
        if(!isset($lib[$name])){
            self::exception('M::load(...) : Library "%s" is not defined', $name);
        }

        // return library if exist
         if(is_object($lib[$name]['resource'])){
            return $lib[$name]['resource'];
        }

        // create new object
        self::import($lib[$name]['resource'], true);
        $class = $lib[$name]['class'];

        if(!class_exists(self::trimmer($class))){
            self::exception('M::load(...) : Class "%s" is not defined', $class);
        }

        if(method_exists($class,  '__construct')){
            $ref = new \ReflectionClass($class);
            $lib[$name]['resource'] = $ref->newInstanceArgs((array)$lib[$name]['args']);
        }else{
            $lib[$name]['resource'] = new $class;
        }

        // return lib
        return $lib[$name]['resource'];
    }

    /**
     * set & get global config
     * @param  array|string $key
     * @param  $val
     * @param  boolean $overwrite
     */
    public static function config($key = null, $val = EMPTY_VALUE, $overwrite = true){
        static
            $data = array(),
            $allow_overwrite = array();

        $temp = array();

        if(is_string($key)){
            // return all configs
            if(!self::trimLower($key)){
                return $data;
            }
            $temp[$key] = $val;
        }else{
            $overwrite = (boolean)$val;
            $temp = $key;
        }

        foreach((array)$temp as $k => $v){
            // return config by $key
            if(self::isEmpty($v)){
                return isset($data[$k]) ? $data[$k] : null;
            }

            // set config
            if(!isset($data[$k]) || !isset($allow_overwrite[$k])){
                $data[$k] = $v;
            }

            // don't allow overwrite
            if(!$overwrite){
                $allow_overwrite[$k] = true;
            }
        }

        return $data;
    }

    /**
     * import file
     * @param  string | array $file
     * @param  boolean $require
     * @param  array $data
     * @param  int | const $flag
     * @param  string $prefix
     */
    public static function import($files, $require = true, $data = array(), $flag = EXTR_OVERWRITE, $prefix = null){
        $error = false;

        // extract variable
        if(is_bool($flag) && $flag){
            $flag = EXTR_REFS;
        }
        extract((array)$data, $flag, $prefix);

        // check & include file
        foreach((array)$files as $file){
            if(!($f = self::getPath($file))){
                if($require){
                    self::exception('M::import(...) : Failed opening required %s', $file);
                }else{
                    $error = true;
                    continue;
                }
            }

            if(!in_array($f, get_included_files())){
                require_once $f;
            }
        }

        return $error;
    }

    /**
     * redirect url
     * @param  string $url
     * @param  int $delay millisecond
     * @param  int $code
     * @param  boolean $replace
     */
    public static function redirect($url = null, $delay = 0, $code = 301, $replace = false){
        $absolute = false;

        if(!$url){
            $url = SITE_ROOT;
            $absolute = true;
        }

        if(preg_match('/^[a-zA-Z0-9]+\:\/\/(.*?)/', $url)){
            $absolute = true;
        }

        if(!$absolute){
            $url = SITE_ROOT . trim($url, '/');
        }

        // delay
        usleep($delay * 1000);

        // redirect
        header('Location: ' . $url, $replace, $code);
        die;
    }

    /**
     * handler exception
     * @param  string $str
     * @param  array $args
     */
    public static function exception($str, $args = null){
        throw new \Exception(vsprintf($str, (array)$args));
    }

    /**
     * check empty value
     * @param  $var
     */
    public static function isEmpty($var){
        if($var === EMPTY_VALUE){
            return true;
        }

        return false;
    }

    /**
     * trim string
     * @param  string &$str
     */
    public static function trimmer(&$str){
        return $str = trim(preg_replace('#\s+#', ' ', $str));
    }

    /**
     * trim & convert string to lower case
     * @param  string &$str
     */
    public static function trimLower(&$str){
        return $str = strtolower(self::trimmer($str));
    }

    /**
     * trim & convert string to upper case
     * @param  string &$str
     */
    public static function trimUpper(&$str){
        return $str = strtoupper(self::trimmer($str));
    }

    /**
     * replace multi & trim slash
     * @param  string &$str
     * @param  string $slash
     */
    public static function trimSlash(&$str, $slash = '/'){
        return $str = trim(preg_replace("/[\/\\\]+/", $slash, $str), $slash);
    }

    /**
     * get last string after symbol
     * @param  string $str
     * @param  string $symbol
     */
    public static function lastString($str, $symbol = '/'){
        return substr(strrchr($str, $symbol), 1);
    }

    /**
     * get first string before symbol
     * @param  string $str
     * @param  string $symbol
     */
    public static function firstString($str, $symbol = '/'){
        return strstr($str, $symbol, 1);
    }

    /**
     * json parse (decode)
     * @param  string  $str
     * @param  boolean $assoc true: array; false: object
     */
    public static function jsonParse($str, $assoc = true){
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
    public static function getFile($dir){
        return self::getSub($dir, false);
    }

    /**
     * get sub folders in folder
     * @param string $dir
     */
    public static function getFolder($dir){
        return self::getSub($dir, true);
    }

    /**
     * get sub folder | file
     * @param  string $dir
     * @param  boolean $child true: folders; false: files
     */
    public static function getSub($dir, $child = null){
        $dir = self::getPath($dir);
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
     * get real path of file or directory
     * @param  string $path
     */
    public static function getPath($path){
        // for windows
        if(IS_WINDOWS){
            if(self::trimSlash($path, DS) && (is_readable($path) || is_readable($path = APP_PATH . $path))){
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

        $path = DS . self::trimSlash($path);

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

    /**
     * excution time
     * @param  float $start
     * @param  float $end
     */
    public static function excutionTime($start, $end = 0){
        if($end <= 0){
            $end = microtime(true);
        }
        return ($total = (float)($end - $start)) >= 1 ? $total . ' s' : $total*1000 . ' ms';
    }

    /**
     * memory usage
     * @param  float $size
     */
    public static function memoryUsage($size = 0){
        $unit = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

        if($size <= 0){
            $size = memory_get_peak_usage();
        }

        return round(($size)/pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }
}