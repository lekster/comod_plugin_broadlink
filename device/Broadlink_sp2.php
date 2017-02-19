<?php

require_once (PROJ_DIR . "/htdocs/console/controllers/AbstractDevice.php");
require_once (__DIR__ . "/../src/include/broadlink.class.php");

class Broadlink_sp2 extends Broadlink
{
	const DEVICE_TYPE = 1;

	public function __construct($address=null)
    {
        parent::__construct($address);
    }

	public function discovery()
	{
		//возвращать mac адреса
		$devs = Broadlink::Discover();
		$ret = [];

		foreach ($devs as $dev)
		{
			if (get_class($dev) == get_class())
			{
				$dev->Auth();
				$ret[] = $dev;
			}
		}

		return $ret;
	}
	
	public function getPortsConf()
	{
		return array(
			'p0' => ['AccessType' => 'R/W', 'PortReal' => 'power0'],
		);
	}

	protected function setPortValTempl($port, $val)
	{
		return $this->Set_Power($val ? 1 : 0);
	}

	protected function getPortValTempl($port)
	{
		return $this->Check_Power() ? 1 : 0;
	}
	
	public function ping()
	{
		return true;
	}



	public function Set_Power($state)
	{
        $packet = self::bytearray(16);
        $packet[0] = 0x02;
        $packet[4] = $state ? 1 : 0;

        $response = $this->send_packet(0x6a, $packet);
        $err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
        

        if($err == 0){
            $enc_payload = array_slice($response, 0x38);

            if(count($enc_payload) > 0){

                $payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
                return ($payload[0x4] == $state);    
            }

        }

        return false;
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
                return $payload[0x4] ? true : false;    
            }

        }

        return false;
        
    }

    public function getVersion() { return "0.0.1"; }
}
