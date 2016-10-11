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

abstract class Base{
    /**
     * get property
     * @param  string $property
     * @param  $default
     */
    public function get($property, $default = null){
        if(property_exists($this, $property)){
            $ref    = new \ReflectionClass($this);
            $p      = $ref->getProperty($property);

            if($p->isPrivate()){
                $p->setAccessible(true);
                return $p->getValue($this);
            }

            return $this->{$property};
        }

        return $default;
    }

    /**
     * assign variable to view
     * @param  string | array $name
     * @param  $value
     */
    public function assign($key, $value = null){
        M::assign($key, $value);
        return $this;
    }

    /**
     * get data assigned
     * @param  string $key
     */
    public function data($key = null){
        return M::assign($key);
    }

    /**
	 * auto expand method or get core lib
     * @param  string $method
     * @param  array $args
	 */
	public function __call($method, $args){
        if(($instance = M::__callStatic($method, $args))){
            return $instance;
        }

		$lib = str_replace('\\', '.', get_class($this));
		return M::event()->expand("$lib.expand.$method", $args, $this);
	}
}