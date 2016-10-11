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
     * get core library
     * @param  string $name
     * @param  array $args
     */
    public static function __callStatic($lib, $args){
        static $data = array();

        $first  = substr(trim_lower($lib), 0, 1);
        $cls    = "\MaskPHP\\" . strtoupper($first) . substr($lib, 1);
        if(!isset($data[$lib])){
            // does not exist class
            if(!class_exists($cls)){
                return null;
            }

            // dont init able
            $ref = new \ReflectionClass($cls);
            if(!$ref->IsInstantiable()){
                return null;
            }

            $data[$lib] = new $cls;
        }

        return $data[$lib];
    }

    /**
     * get library
     * @param  string $lib
     */
    public static function get($lib){
        return self::load($lib);
    }

    /**
     * register library
     * @param  string  $lib
     * @param  string  $class
     * @param  string | object  $resource
     * @param  array   $args
     * @param  boolean $overwrite
     */
    public static function register($lib, $class, $resource, $args = array(), $overwrite = true){
        return self::load($lib, $class, $resource, $args, $overwrite, true);
    }

    /**
     * load library
     * @param  string  $lib
     * @param  string  $class
     * @param  string | object  $resource
     * @param  array   $args
     * @param  boolean $overwrite
     * @param  boolean $register
     */
    public static function load($lib, $class = '', $resource = '', $args = null, $overwrite = true, $register = false){
        static $data = array();

        trim_lower($lib);
        if($register){
            if(!isset($data[$lib]) || $data[$lib]['overwrite']){
                $class      = '\\' . trim($class, '\\');
                $data[$lib] = array('class' => $class, 'resource' => $resource, 'args' => $args, 'overwrite' => $overwrite);
            }
            return null;
        }

        // check library is defined
        if(!isset($data[$lib])){
            self::exception('M::load(...) : Library "%s" is not exist', $lib);
        }

        // return library if exist & init
        if(is_object($data[$lib]['resource'])){
            return $data[$lib]['resource'];
        }

        // create new object
        self::import($data[$lib]['resource'], true);
        $class =& $data[$lib]['class'];

        if(!class_exists($class)){
            self::exception('M::load(...) : Class "%s" is not defined', $class);
        }

        if(method_exists($class,  '__construct')){
            $ref                    = new \ReflectionClass($class);
            $data[$lib]['resource'] = $ref->newInstanceArgs((array)$data[$lib]['args']);
        }else{
            $data[$lib]['resource'] = new $class;
        }

        // return lib
        return $data[$lib]['resource'];
    }

    /**
     * Reference to your controller’s instance
     * @param  object $controller
     */
    public static function &getInstance(&$controller = null){
        static $instance = null;

        if(!$instance && is_object($controller)){
            $instance = $controller;
        }

        return $instance;
    }

    /**
     * get & assign variable
     * @param  string | array $name
     * @param  $value
     */
    public static function assign($key = null, $value = EMPTY_VALUE){
        static $data = array();

        if(!$key){
            return $data;
        }

        if(is_empty($value)){
            if(isset($data[$key])){
                return $data[$key];
            }

            return null;
        }

        if(is_array($key)){
            $data = array_merge($data, $key);
        }else{
            $data[$key] = $value;
        }
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
        // extract variable
        if(is_bool($flag) && $flag){
            $flag = EXTR_REFS;
        }
        extract((array)$data, $flag, $prefix);

        // check import with one file
        $import_one = false;
        if(is_string($files)){
            $import_one = true;
        }

        // store data
        $data = array();

        // check & include file
        foreach((array)$files as $k => $file){
            // skip include file
            if(in_array($file, get_included_files())){
                continue;
            }

            // check file exist
            if(!($f = get_path($file))){
                if($require){
                    self::exception('M::import(...) : Failed opening required %s', $file);
                }else{
                    $data[$k] = false;
                    continue;
                }
            }

            $data[$k] = require_once $f;
        }

        return $import_one && $data ? $data[0] : $data;
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