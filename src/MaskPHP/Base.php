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
	 * auto expand method
     * @param  string $method
     * @param  array $args
	 */
	function __call($method, $args){
		$lib = str_replace('\\', '.', get_class($this));
		return M::event()->expand("$lib.expand.$method", $args, $this);
	}
}