<?php

use console\controllers\AbstractDevice;
require_once (__DIR__ . "/../../src/include/broadlink.class.php");

class Broadlink_sp3 extends Broadlink
{
	
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

	public function init() {}
	public function getVersion() { return "0.0.1"; }
	public function setOptions(array $opt) {}
	public function getOptions() {}

}
