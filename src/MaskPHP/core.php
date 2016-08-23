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

\M::register('base', 'system/base.php', array('method' => 'POST'), false);
\M::reborn('base', array('method' => 'GET'));

abstract class M{
    function load(){}
    function get(){}
    function get_controller(&$controller = null){}

    public static function reborn($name, $args){
        return self::register($name, null, $args, false);
    }

    public static function register($name, $file = null, $args = array(), $overwrite = true){
        static
            $data = array(),
            $allow_overwrite = array();

        if(!trim_lower($name)){
            return;
        }

        $temp = array();

        if(is_array($name)){
            $temp = $name;
        }else{
            //$temp[$name] = 
        }
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
            if(!trimmer($key)){
                return $data;
            }

            // convert to lower case
            $key = trimmer($key, false);
            $temp[$key] = $val;
        }else{
            $overwrite = (boolean)$val;
            $temp = $key;
        }

        foreach((array)$temp as $k => $v){
            // return config by $key
            if(is_empty($v)){
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
     * import with reference variable
     * @param  string|array  $files
     * @param  boolean $require
     * @param  &$args
     * @param  string $var_name
     */
    public static function import_ref($files, $require = true, &$args = EMPTY_VALUE, $var_name = null){
        $data = array();

        // check assign reference variable
        if(!is_empty($args)){
            if($var_name){
                $data[$var_name] =& $args;
            }else{
                $data['args'] =& $args;
            }
        }

        return self::import($files, $require, $data, true, null);
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
        $error = array();

        // extract variable
        if(is_bool($flag) && $flag){
            $flag = EXTR_REFS;
        }
        extract((array)$data, $flag, $prefix);

        // check & include file
        foreach((array)$files as $file){
            if(!($f = get_realpath($file))){
                if($require){
                    self::exception('\M::import(...) : Failed opening required %s', $file);
                }else{
                    $error[] = $file;
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
}