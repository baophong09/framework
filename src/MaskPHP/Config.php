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

class Config extends Base{
	protected $data = array(), $overwrite = array();

	/**
	 * set config
	 * @param  array|string $key
	 * @param  $val
	 * @param  boolean $overwrite
	 */
	public function set($key, $val, $overwrite = true){
		$temp = array();

		if(is_string($key)){
			$temp[$key] = $val;
		}else{
			$overwrite = (boolean)$val;
			$temp =& $key;
		}

		foreach($temp as $k => $v){
			trim_lower($k);

			if(!isset($this->data[$k]) || $this->overwrite[$k]){
				$this->data[$k] = $v;
				$this->overwrite[$k] = $overwrite;
			}
		}

		return $this;
	}

	/**
	 * get config
	 * @param  string $key
	 * @param  $default
	 */
	public function &get($key, $default = null){
		trim_lower($key);
		if(isset($this->data[$key])){
			if($this->overwrite[$key]){
				return $this->data[$key];
			}

			// dont allow overwrite
			$temp = $this->data[$key];
			return $temp;
		}

		return $default;
	}

	/**
	 * get all config
	 */
	public function getAll(){
		return $this->data;
	}
}