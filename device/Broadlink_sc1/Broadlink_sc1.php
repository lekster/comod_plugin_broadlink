<?php

use console\controllers\AbstractDevice;
require_once (__DIR__ . "/../../src/include/broadlink.class.php");

class Broadlink_sc1 extends Broadlink
{
	const DEVICE_TYPE = [0x7547];

	public function getPortsConf()
	{
		return array(
			'p0' => ['AccessType' => 'RW', 'PortReal' => 'power0'],
		);
	}
	public function init() {}

	protected function setPortValTempl($port, $val)
	{
		return $this->Set_Power($val ? 1 : 0);
	}

	protected function getPortValTempl($port)
	{
		return $this->Check_Power();
	}
	
	public function ping()
	{
		return true;
	}

	public function getVersion() { return "0.0.1"; }



	public function Set_Power($state){

        $packet = self::bytearray(16);
        $packet[0] = 0x02;
        $packet[4] = $state ? 1 : 0;

        return $this->send_packet(0x6a, $packet);
    }

    public function Check_Power(){

        $packet = self::bytearray(16);
        $packet[0] = 0x01;

        $response = $this->send_packet(0x6a, $packet);
        $err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
        

        if($err == 0){
            $enc_payload = array_slice($response, 0x38);

            if(count($enc_payload) > 0){

                $payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
                return $payload[0x4] ? 1 : 0;    
            }

        }

        return null;

        
    }
}


/*

	$devs = Broadlink::Discover();

	//var_dump($devs);

	var_dump($devs[0]->mac());

	var_dump($devs[0]->Auth());
	var_dump($devs[0]->Set_Power(1));
	var_dump($devs[0]->Check_Power());


*/
