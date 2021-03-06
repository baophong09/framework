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

class Event extends Base{
	private $hook = array(), $event = array();

	/**
	 * trigger event
	 * @param  string $event
	 * @param  array $args
	 */
	public function trigger($event, $args = null){
		if(!in_array(trim_lower($event), $this->event)){
			$this->event[] = $event;
		}

		// check hook
		if(!isset($this->hook[$event])){
			return null;
		}

		$hook = $this->hook[$event];

		// dont allow overwite
		$hlen = 0;
		foreach($hook as $k => $priority){
			$hlen++;
			$plen 	= 0;
			$break 	= false;
			foreach($priority as $p){
				$plen++;
				if(!$p['overwrite']){
					$hook[$k] 	= array_splice($priority, 0, $plen);
					$break 		= true;
				}
			}

			if($break){
				$hook = array_splice($hook, 0, $hlen);
				break;
			}
		}

		// sort by priority ASC
		krsort($hook);

		// set array type
		if(!is_array($args)){
			$temp = $args;
			$args = array($temp);
		}

		$data = array('args' => &$args);

		foreach($args as $k => $v){
			if(is_string($k)){
				$data[$k] =& $args[$k];
			}
		}

		// fire trigger
		foreach($hook as $priority){
			foreach($priority as $v){
				switch($v['type']){
					case 'callback':
						return call_user_func_array($v['args'], $args);
					break;

					case 'attach':
						return M::import($v['args'], true, $data, true);
					break;

					default:
						return $v['args'];
					break;
				}
			}
		}

		return null;
	}

	/**
	 * assign variable | function on event
	 * @param  string  $event
	 * @param  $args
	 * @param  integer $priority
	 * @param  boolean $overwrite
	 * @param  string  $type
	 */
	public function hook($event, $args = null, $priority = 0, $overwrite = true, $type = null){
		// store event
		if(!isset($this->hook[trim_lower($event)])){
			$this->hook[$event] = array();
		}
		$hook =& $this->hook[$event];

		// event priority
		if(!isset($hook[$priority])){
			$hook[$priority] = array();
		}

		// get type
		if(!$type){
			if(is_callable($args)){
				$type = 'callback';
			}else{
				$type = 'value';
			}
		}

		$hook[$priority][] = array('type' => $type, 'args' => $args, 'overwrite' => $overwrite);

		return $this;
	}

	/**
	 * assign file excute on event
	 * @param  string  $event
	 * @param  string  $file
	 * @param  integer $priority
	 * @param  boolean $overwrite
	 */
	public function attach($event, $file, $priority = 0, $overwrite = true){
		return $this->hook($event, $file, $priority, $overwrite, 'attach');
	}

	/**
	 * expand object method
	 * @param  string  $event
	 * @param  &array  $args
	 * @param  object  &$obj
	 */
	public function expand($event, $args = null, &$obj = null){
		// extend parent
		if(!isset($this->hook[trim_lower($event)])){
			$method = last_string($event, '.');
			$event 	= str_replace('\\', '.', get_parent_class($obj)) . '.expand.' . $method;
			trim_lower($event);
		}

		if(isset($this->hook[$event])){
			array_unshift($args, null);
			$args[0] =& $obj;
			return self::trigger($event, $args);
		}

		return $obj;
	}
}