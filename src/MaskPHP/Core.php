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

abstract class M{
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
     * @param  boolean $overwrite
     */
    public static function register($name, $resource = '', $class = '', $args = array(), $overwrite = true){
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
                , $overwrite
            );
        }
    }

    /**
     * get core
     */
    public static function __callStatic($name, $args){
        $cls = CORE_NAMESPACE . $name;
        self::register($cls, new $cls, $cls);
        return self::get($cls);
    }

    /**
     * load library
     * @param  string  $name
     * @param  string  $class
     * @param  string | object  $resource
     * @param  array   $args
     * @param  boolean $register
     * @param  boolean $overwrite
     */
    public static function load($name, $class = null, $resource = null, $args = null, $register = false, $overwrite = true){
        static $lib = array();
        trim_lower($name);

        if($register){
            if(!isset($lib[$name]) || (isset($lib[$name]) && $lib[$name]['overwrite'])){
                $class = '\\' . trim($class, '\\');
                $lib[$name] = array('resource' => $resource, 'class' => $class, 'args' => $args, 'overwrite' => $overwrite);
            }
            return;
        }

        // check library is defined
        if(!isset($lib[$name])){
            self::exception('M::load(...) : Library "%s" is not exist', $name);
        }

        // return library if exist & init
        if(is_object($lib[$name]['resource'])){
            return $lib[$name]['resource'];
        }

        // create new object
        self::import($lib[$name]['resource'], true);
        $class =& $lib[$name]['class'];

        if(!class_exists($class)){
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
            if(!$key){
                return $data;
            }
            $temp[trim_lower($key)] = $val;
        }else{
            $overwrite = (boolean)$val;
            $temp =& $key;
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
            // skip include file
            if(in_array($file, get_included_files())){
                continue;
            }

            // check file exist
            if(!($f = get_path($file))){
                if($require){
                    self::exception('M::import(...) : Failed opening required %s', $file);
                }else{
                    $error = true;
                    continue;
                }
            }

            require_once $f;
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