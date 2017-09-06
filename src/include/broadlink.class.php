<?php

require_once __DIR__ . "/../phpcrypt/phpCrypt.php";
use PHP_Crypt\PHP_Crypt as PHP_Crypt;

use console\controllers\AbstractDevice;
use src\helpers\SysHelper;

function aes128_cbc_encrypt($key, $data, $iv) {

  $crypt = new PHP_Crypt($key, PHP_Crypt::CIPHER_AES_128, PHP_Crypt::MODE_CBC);
  $crypt->IV($iv);
  return $crypt->encrypt($data);

  //return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
}

function aes128_cbc_decrypt($key, $data, $iv) {
  
  $crypt = new PHP_Crypt($key, PHP_Crypt::CIPHER_AES_128, PHP_Crypt::MODE_CBC);
  $crypt->IV($iv);
  return $crypt->decrypt($data);

  //return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
}

abstract class Broadlink extends AbstractDevice {
	protected $name; 
    protected $host;
    protected $port = 80;
    protected $mac;
    protected $timeout = 10;
    protected $count;
    protected $key = array(0x09, 0x76, 0x28, 0x34, 0x3f, 0xe9, 0x9e, 0x23, 0x76, 0x5c, 0x15, 0x13, 0xac, 0xcf, 0x8b, 0x02);
    protected $iv = array(0x56, 0x2e, 0x17, 0x99, 0x6d, 0x09, 0x3d, 0x28, 0xdd, 0xb3, 0xba, 0x69, 0x5a, 0x2e, 0x6f, 0x58);
    protected $id = array(0, 0, 0, 0);
    protected $devtype;


    public function __construct($address=null)
    {
        parent::__construct($address);

        $this->count = rand(0, 0xffff);
    }

    public static function discovery()
    {
        //возвращать mac адреса
        $devs = self::Discover();
        $ret = [];

        foreach ($devs as $dev)
        {
            $dev->Auth();
            $ret[] = $dev;
        }

        return $ret;
    }

    /*
    function __construct($h = "", $m = "", $p = 80, $d = null) {

    	$this->host = $h;
    	$this->port = $p;
    	$this->devtype = is_string($d) ? hexdec($d) : $d;

    	if(is_array($m)){

    		$this->mac = $m;      		
    	}
    	else{

    		$this->mac = array();
		    $mac_str_array = explode(':', $m);

            foreach ( array_reverse($mac_str_array) as $value ) {
                array_push($this->mac, $value);
            }

    	}

    }
        */	 		
		

    
    
    protected static function CreateDevice($host, $mac, $deviceType, $className)
    {

        /*
        $files = SysHelper::glob_recursive(__DIR__ . "/../../device/*.php");
        //get classes for types
        foreach ($files as $fileName)
        {
            require_once $fileName;
        }


        
        $dc = get_declared_classes();
        $classNameLower = strtolower(get_class());
        $bc = array_values(array_filter($dc, function ($x) use($classNameLower) { return preg_match("/$classNameLower/i", $x); } ));

        foreach ($bc as $className) {
            $reflector = new ReflectionClass($className);
            if ($reflector->isSubclassOf(get_class()))
            {
                if (@in_array($deviceType, $reflector->getConstant("DEVICE_TYPE")))
                {
                    $ret = new $className($mac);
                    $ret->setOptions(['host' => $host]);
                    return $ret;
                }
                //var_dump($reflector);
                //var_dump($reflector->getConstants());
            }
        }
        */

        $reflector = new ReflectionClass($className);
        if ($reflector->isSubclassOf(get_class()))
        {
            if (@in_array($deviceType, $reflector->getConstant("DEVICE_TYPE")))
            {
                $ret = new $className($mac);
                $ret->setOptions(['host' => $host]);
                return $ret;
            }
            //var_dump($reflector);
            //var_dump($reflector->getConstants());
        }

        return NULL;
    }

    public function key(){
    	return implode(array_map("chr", $this->key));
    }

    protected function iv(){
    	return implode(array_map("chr", $this->iv));
    }

    public function getIdHex()
    {
        //return implode(array_map("chr", $this->id));
        return  implode(array_map(function ($x) {return sprintf("%02x", $x); }, $this->id )); 
    }

    public function getKeyHex()
    {
        //return implode(array_map("chr", $this->id));
        return  implode(array_map(function ($x) {return sprintf("%02x", $x); }, $this->key )); 
    }

    public function mac(){

    	$mac = "";

    	foreach ($this->mac as $value) {
    		$mac = sprintf("%02x", $value) . ':' . $mac;
    	}

    	return substr($mac, 0, strlen($mac) - 1);
    }

    public function host(){
    	return $this->host;
   	}

   	public function name(){
    	return $this->name;
   	}

   	public function devtype(){
    	return sprintf("0x%x", $this->devtype);
   	}

   	public function model(){
    	
    	$type = "Unknown";


        $typesArr = 
        [
            "SP1" => [0],
            "SP2" => [0x2711],
            "Honeywell SP2" => [0x2719, 0x7919, 0x271a, 0x791a],
            "SPMini" => [0x2720],
            "SP3" => [0x753e],
            "SPMini2" => [0x2728],
            "OEM branded SPMini" => [0x2733, 0x273e],
            "OEM branded SPMini2" => [0x7530, 0x7918],
            "SPMiniPlus" => [0x2736],
            "RM2" => [0x2712],
            "RM Mini" => [0x2737],
            "RM Pro Phicomm" => [0x273d],
            "RM2 Home Plus" => [0x2783, 0x277c],
            "RM2 Pro Plus" => [0x272a],
            "RM2 Pro Plus2" => [0x2787],
            "RM2 Pro Plus BL" => [0x278b],
            "RM Mini Shate" => [0x278f],
            "A1" => [0x2714],
            "MP1" => [0x4EB5],
        ];

        foreach ($typesArr as $key => $value)
        {
            if (in_array($devtype, $value))
            {
                $type = $key;
                break;
            }
        }

    	return $type;
    }

    public static function getdevtype($devtype){
    	
    	$type = -1;

        $devtype = is_string($devtype) ? hexdec($devtype) : $devtype;

        $typesArr = 
        [
            0 => [0],
            1 => [0x2711, 0x2719, 0x7919, 0x271a, 0x791a, 0x2720, 0x753e, 0x2728, 0x2733, 0x273e, 0x7530, 0x7918,
                0x2736],
            2 => [0x2712, 0x2737, 0x273d, 0x2783, 0x277c, 0x272a, 0x2787, 0x278b, 0x278f],
            3 => [0x2714],
            4 => [0x4EB5],

        ];

        foreach ($typesArr as $key => $value)
        {
            if (in_array($devtype, $value))
            {
                $type = $key;
                break;
            }
        }

    	return $type;
    } 	

    protected static function bytearray($size){

    	$packet = array();

	    for($i = 0 ; $i < $size ; $i++){
	    	$packet[$i] = 0;
	    }

	    return $packet;
    }

    protected static function byte2array($data){

	    return array_merge(unpack('C*', $data));
    }

    protected static function byte($array){

	    return implode(array_map("chr", $array));
    }

    public static function Discover()
    {

    	$s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
  		socket_connect($s ,'8.8.8.8', 53);  // connecting to a UDP address doesn't send packets
  		socket_getsockname($s, $local_ip_address, $port);
  		socket_close($s);

  		$cs = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

   		if($cs){
   			socket_set_option($cs, SOL_SOCKET, SO_REUSEADDR, 1);
    		socket_set_option($cs, SOL_SOCKET, SO_BROADCAST, 1);
    		socket_set_option($cs, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>1, 'usec'=>0));
    		socket_bind($cs, 0, 0);
   		}

  		$address = explode('.', $local_ip_address);
		$packet = self::bytearray(0x30);

	    $timezone = (int)intval(date("Z"))/-3600;
	  	$year = date("Y");

		if($timezone < 0){
		    $packet[0x08] = 0xff + $timezone - 1;
		    $packet[0x09] = 0xff;
		    $packet[0x0a] = 0xff;
		    $packet[0x0b] = 0xff;
		}
		else{

			$packet[0x08] = $timezone;
		    $packet[0x09] = 0;
		    $packet[0x0a] = 0;
		    $packet[0x0b] = 0;
		}    

		$packet[0x0c] = $year & 0xff;
		$packet[0x0d] = $year >> 8;
		$packet[0x0e] = intval(date("i"));
		$packet[0x0f] = intval(date("H"));
		$subyear = substr($year, 2);
		$packet[0x10] = intval($subyear);
		$packet[0x11] = intval(date('N'));
		$packet[0x12] = intval(date("d"));
		$packet[0x13] = intval(date("m"));
		$packet[0x18] = intval($address[0]);
		$packet[0x19] = intval($address[1]);
		$packet[0x1a] = intval($address[2]);
		$packet[0x1b] = intval($address[3]);
		$packet[0x1c] = $port & 0xff;
		$packet[0x1d] = $port >> 8;
		$packet[0x26] = 6;

		$checksum = 0xbeaf;

		for($i = 0 ; $i < sizeof($packet) ; $i++){
	      $checksum += $packet[$i];
	    }

	   	$checksum = $checksum & 0xffff;

		$packet[0x20] = $checksum & 0xff;
		$packet[0x21] = $checksum >> 8;

        $devices = array();

		socket_sendto($cs, self::byte($packet), sizeof($packet), 0, "255.255.255.255", 80);
		while(socket_recvfrom($cs, $response, 1024, 0, $from, $port)){

			$host = '';

			$responsepacket = self::byte2array($response);


			$devtype = hexdec(sprintf("%x%x", $responsepacket[0x35], $responsepacket[0x34]));
			$host_array = array_slice($responsepacket, 0x36, 4);
			$mac = array_slice($responsepacket, 0x3a, 6);

			foreach ( $host_array as $ip ) {
 				$host .= $ip . ".";
			}

			$host = substr($host, 0, strlen($host) - 1);

            //var_dump($host, $mac, $devtype);
			//var_dump($mac);
            $hexMac = implode(":", (array_map(function($x) {return strtoupper(bin2hex(chr($x)));}, $mac)));
            //var_dump($hexMac);

            $className = get_called_class();
            $device = Broadlink::CreateDevice($host, $hexMac, $devtype, $className);

            //var_dump(get_class($device));

			if($device != NULL)
            {
                $device->name = str_replace("\0", '', Broadlink::byte(array_slice($responsepacket, 0x40)));
				$devices[] = $device;
			}

		}

		if($cs){
			socket_close($cs);
		}

		return $devices;

    }


    function send_packet($command, $payload){

    	$cs = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

   		if($cs){
   			socket_set_option($cs, SOL_SOCKET, SO_REUSEADDR, 1);
    		socket_set_option($cs, SOL_SOCKET, SO_BROADCAST, 1);
    		socket_bind($cs, 0, 0);
   		}

	    $this->count = ($this->count + 1) & 0xffff;

	    $packet = $this->bytearray(0x38);

	    $packet[0x00] = 0x5a;
	    $packet[0x01] = 0xa5;
	    $packet[0x02] = 0xaa;
	    $packet[0x03] = 0x55;
	    $packet[0x04] = 0x5a;
	    $packet[0x05] = 0xa5;
	    $packet[0x06] = 0xaa;
	    $packet[0x07] = 0x55;
	    $packet[0x24] = 0x2a;
	    $packet[0x25] = 0x27;
	    $packet[0x26] = $command;
	    $packet[0x28] = $this->count & 0xff;
	    $packet[0x29] = $this->count >> 8;
	    $packet[0x2a] = $this->mac[0];
	    $packet[0x2b] = $this->mac[1];
	    $packet[0x2c] = $this->mac[2];
	    $packet[0x2d] = $this->mac[3];
	    $packet[0x2e] = $this->mac[4];
	    $packet[0x2f] = $this->mac[5];
	    $packet[0x30] = $this->id[0];
	    $packet[0x31] = $this->id[1];
	    $packet[0x32] = $this->id[2];
	    $packet[0x33] = $this->id[3];

	    $checksum = 0xbeaf;
	    for($i = 0 ; $i < sizeof($payload) ; $i++){
	      $checksum += $payload[$i];
	      $checksum = $checksum & 0xffff;  
	    }	    

	    $aes = $this->byte2array(aes128_cbc_encrypt($this->key(), $this->byte($payload), $this->iv()));

      
        /*$packetHex = "";
        foreach (array_reverse($payload) as $value) {
            $packetHex = sprintf("%02x", $value) . ':' . $packetHex;
        }
        var_dump($packetHex);
        $packetHex = "";
        foreach (array_reverse($aes) as $value) {
            $packetHex = sprintf("%02x", $value) . ':' . $packetHex;
        }
        var_dump($packetHex);
        */
	    $packet[0x34] = $checksum & 0xff;
	    $packet[0x35] = $checksum >> 8;

	    for($i = 0 ; $i < sizeof($aes) ; $i++){
	      array_push($packet, $aes[$i]);
	    }

	    $checksum = 0xbeaf;
	    for($i = 0 ; $i < sizeof($packet) ; $i++){
	      $checksum += $packet[$i];
	      $checksum = $checksum & 0xffff;
	    }	    

	    $packet[0x20] = $checksum & 0xff;
	    $packet[0x21] = $checksum >> 8;

	    $starttime = time();


	    $from = '';
    
        /*$packetHex = "";	    
        foreach (array_reverse($packet) as $value) {
            $packetHex = sprintf("%02x", $value) . ':' . $packetHex;
        }
        var_dump($packetHex);
        */
        $ret = false;
        $i = 5;

        while($ret === false and $i > 0)
        {
           socket_sendto($cs, self::byte($packet), sizeof($packet), 0, $this->host, $this->port);
	       socket_set_option($cs, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>$this->timeout, 'usec'=>0));

	       $ret = socket_recvfrom($cs, $response, 1024, 0, $from, $port);
           sleep(1);
           $i--;
        }
	    if($cs){
	    	socket_close($cs);
	    }
	    return $this->byte2array($response);

    }

    public function Auth(){

    	$payload = $this->bytearray(0x50);

	    $payload[0x04] = 0x31;
	    $payload[0x05] = 0x31;
	    $payload[0x06] = 0x31;
	    $payload[0x07] = 0x31;
	    $payload[0x08] = 0x31;
	    $payload[0x09] = 0x31;
	    $payload[0x0a] = 0x31;
	    $payload[0x0b] = 0x31;
	    $payload[0x0c] = 0x31;
	    $payload[0x0d] = 0x31;
	    $payload[0x0e] = 0x31;
	    $payload[0x0f] = 0x31;
	    $payload[0x10] = 0x31;
	    $payload[0x11] = 0x31;
	    $payload[0x12] = 0x31;
	    $payload[0x1e] = 0x01;
	    $payload[0x2d] = 0x01;
	    $payload[0x30] = ord('T');
	    $payload[0x31] = ord('e');
	    $payload[0x32] = ord('s');
	    $payload[0x33] = ord('t');
	    $payload[0x34] = ord(' ');
	    $payload[0x35] = ord(' ');
	    $payload[0x36] = ord('1');

	    $response = $this->send_packet(0x65, $payload);
	    $enc_payload = array_slice($response, 0x38);

	    $payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));

		$this->id = array_slice($payload, 0x00, 4);
		$this->key = array_slice($payload, 0x04, 16);
    }

    public function setOptions(array $opt)
    {
        $this->_options = $opt;
        if (isset($this->_options['id']))
            $this->id = $this->_options['id'];
        $this->host = $this->_options['host'];
        if (isset($this->_options['key']))
            $this->key = $this->_options['key'];
    }

    public function getOptions()
    {
        $this->_options['id'] = $this->id;
        $this->_options['host'] = $this->host;
        $this->_options['key'] = $this->key;
        return $this->_options;
    }

    /*public function getMacAddress()
    {
        return $this->mac();
    }*/


}


