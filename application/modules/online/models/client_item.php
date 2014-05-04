<?php
/*
 * Site item
 *
 * @author rav <arudyuk@rg3.su>
 * @version 1.0
 */

class Client_item extends CI_Model {
	protected $_info;

	public function __construct() {
		parent::__construct();
		$this->_info['id']				= 0;
		$this->_info['name']			= '';
		$this->_info['phone']			= '';
		$this->_info['email']			= '';
		$this->_info['created']			= 0;
		$this->_info['updated']			= 0;
	}

	public function __set($name, $value) {
		if (isset($this->_info[$name])) {
			if ($name == 'name' || $name == 'phone' || $name == 'email') $this->_info[$name] = trim($value);
			else $this->_info[$name] = (int)$value;
		} else {
			return parent::__set($name, $value);
		}
	}
}