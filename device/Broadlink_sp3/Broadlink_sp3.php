<?php

require_once (PROJ_DIR . "/htdocs/console/controllers/AbstractDevice.php");
require_once (__DIR__ . "/../../src/include/broadlink.class.php");

class Broadlink_sp3 extends AbstractDevice
{
	
	public function discovery()
	{
		//возвращать mac адреса
		$ret = ['123'];
		
		return $ret;
	}
	
	public function getPortsConf()
	{
		return array(
			'a0' => ['AccessType' => 'RW', 'PortReal' => 'power'],
		);
	}

	protected function setPortValTempl($port, $val)
	{
		//$this->_addr
		return true;
	}

	protected function getPortValTempl($port)
	{
		return 0;
	}
	
	public function ping()
	{
		return true;
	}

	public function getVersion() { return "0.0.1"; }

}