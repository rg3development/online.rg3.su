<?php
/*
 * Site item
 *
 * @author rav <arudyuk@rg3.su>
 * @version 1.0
 */

class Site_item extends CI_Model {
	protected $_info;

	public function __construct() {
		parent::__construct();
		$this->_info['id']				= 0;
		$this->_info['url']				= '';
		$this->_info['client']			= new stdClass();
		$this->_info['created']			= 0;
		$this->_info['updated']			= 0;
	}
	
	public function __set($name, $value) {
		if (isset($this->_info[$name])) {
			if ($name == 'url') $this->_info[$name] = trim($value);
			elseif ($name == 'client' && is_object($value) &&  get_class($value) == 'Client_item') $this->_info[$name] = $value;
			else $this->_info[$name] = (int)$value;
		} else {
			return parent::__set($name, $value);
		}
	}
}