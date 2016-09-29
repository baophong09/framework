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
     * get property: public|protected
     * @param  string $property
     */
    public function get($property){
        return $this->{$property};
    }

    /**
     * get private property
     * @param  string $property
     */
    public function getPrivate($property){
        if(property_exists($this, $property)){
            $ref = new \ReflectionClass($this);
            $p = $ref->getProperty($property);
            if($p->isPrivate()){
                $p->setAccessible(true);
                return $p->getValue($this);
            }
        }

        return null;
    }

    /**
	 * auto expand method
	 */
	function __call($method, $args){
		$lib = str_replace('\\', '.', get_class($this));
		return M::event()->expand("$lib.expand.$method", $args, $this);
	}
}