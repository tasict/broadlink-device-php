<?
date_default_timezone_set("Asia/Taipei");

function aes128_cbc_encrypt($key, $data, $iv) {
  return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
}

function aes128_cbc_decrypt($key, $data, $iv) {
  return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
}

class Broadlink{
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

    function __construct($h = "", $m = "", $p = 80, $d) {

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

    	 		
		$this->count = rand(0, 0xffff);

    }
    
    function __destruct() {

		    
    }

    public static function CreateDevice($h, $m, $p, $d){

        switch (self::getdevtype($d)) {
            case 0:
                return new SP1($h, $m, $p, $d);
                break;
            case 1:
                return new SP2($h, $m, $p, $d);
                break;
            case 2:
                return new RM($h, $m, $p, $d);
                break;    
            case 3:
                return new A1($h, $m, $p, $d);
                break;
            default:
        } 

        return NULL;
    }

    protected function key(){
    	return implode(array_map("chr", $this->key));
    }

    protected function iv(){
    	return implode(array_map("chr", $this->iv));
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

    public function devmodel(){
        return self::getdevtype($this->devtype);
    }

   	public function model(){
    	
    	$type = "Unknown";

    	switch ($this->devtype) {
    		case 0:
    			$type = "SP1";
    			break;
    		case 0x2711:
    			$type = "SP2";
    			break;
    		case 0x2719: 
    		case 0x7919:
    		case 0x271a:
    		case 0x791a:
    			$type = "Honeywell SP2";
    			break;
    		case 0x2720: 
    			$type = "SPMini";
    			break;
    		case 0x753e: 
    			$type = "SP3";
    			break;
    		case 0x2728: 
    			$type = "SPMini2";
    			break;
    		case 0x2733: 
    		case 0x273e:
    			$type = "OEM branded SPMini";
    			break;
    		case 0x7530: 
    		case 0x7918:
    			$type = "OEM branded SPMini2";
    			break;
    		case 0x2736: 
    			$type = "SPMiniPlus";
    			break;
    		case 0x2712: 
    			$type = "RM2";
    			break;
    		case 0x2737: 
    			$type = "RM Mini";
    			break;
    		case 0x273d: 
    			$type = "RM Pro Phicomm";
    			break;
    		case 0x2783: 
    			$type = "RM2 Home Plus";
    			break;
    		case 0x277c: 
    			$type = "RM2 Home Plus";
    			break;														 	    			
			case 0x277c: 
    			$type = "RM2 Home Plus GDT";
    			break;
    		case 0x272a: 
    			$type = "RM2 Pro Plus";
    			break;
    		case 0x2787: 
    			$type = "RM2 Pro Plus2";
    			break;
    		case 0x278b: 
    			$type = "RM2 Pro Plus BL";
    			break;														 	    			
			case 0x278f: 
    			$type = "RM Mini Shate";
    			break;
    		case 0x2714: 
    			$type = "A1";
    			break;	
    		default:
    			break;
    	}

    	return $type;
    }

    public static function getdevtype($devtype){
    	
    	$type = -1;

        $devtype = is_string($devtype) ? hexdec($devtype) : $devtype;

    	switch ($devtype) {
    		case 0:
    			$type = 0;
    			break;
    		case 0x2711:
    			$type = 1;
    			break;
    		case 0x2719: 
    		case 0x7919:
    		case 0x271a:
    		case 0x791a:
    			$type = 1;
    			break;
    		case 0x2720: 
    			$type = 1;
    			break;
    		case 0x753e: 
    			$type = 1;
    			break;
    		case 0x2728: 
    			$type = 1;
    			break;
    		case 0x2733: 
    		case 0x273e:
    			$type = 1;
    			break;
    		case 0x7530: 
    		case 0x7918:
    			$type = 1;
    			break;
    		case 0x2736: 
    			$type = 1;
    			break;
    		case 0x2712: 
    			$type = 2;
    			break;
    		case 0x2737: 
    			$type = 2;
    			break;
    		case 0x273d: 
    			$type = 2;
    			break;
    		case 0x2783: 
    			$type = 2;
    			break;
    		case 0x277c: 
    			$type = 2;
    			break;														 	    			
			case 0x277c: 
    			$type = 2;
    			break;
    		case 0x272a: 
    			$type = 2;
    			break;
    		case 0x2787: 
    			$type = 2;
    			break;
    		case 0x278b: 
    			$type = 2;
    			break;														 	    			
			case 0x278f: 
    			$type = 2;
    			break;
    		case 0x2714: 
    			$type = 3;
    			break;	
    		default:
    			break;
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

    public static function Discover(){

    	$devices = array();

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

		socket_sendto($cs, self::byte($packet), sizeof($packet), 0, "255.255.255.255", 80);
		while(socket_recvfrom($cs, $response, 1024, 0, $from, $port)){

			$host = '';

			$responsepacket = self::byte2array($response);


			$devtype = hexdec(sprintf("%x%x", $responsepacket[0x35], $responsepacket[0x34]));
			$host_array = array_slice($responsepacket, 0x36, 4);
			$mac = array_slice($responsepacket, 0x3a, 6);

			foreach ( array_reverse($host_array) as $ip ) {
 				$host .= $ip . ".";
			}

			$host = substr($host, 0, strlen($host) - 1);

			$device = Broadlink::CreateDevice($host, $mac, 80, $devtype);

			if($device != NULL){
                $device->name = str_replace("\0", '', Broadlink::byte(array_slice($responsepacket, 0x40)));
				array_push($devices, $device);
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
	    socket_sendto($cs, $this->byte($packet), sizeof($packet), 0, $this->host, $this->port);
	    socket_set_option($cs, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>$this->timeout, 'usec'=>0));

	    $ret = socket_recvfrom($cs, $response, 1024, 0, $from, $port);

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


}

class SP1 extends Broadlink{

    function __construct($h = "", $m = "", $p = 80, $d = 0x2712) {

         parent::__construct($h, $m, $p, $d);

    }

    public function Set_Power($state){

        $packet = self::bytearray(4);
        $packet[0] = $state;

        $this->send_packet(0x66, $packet);
    }   

}

class SP2 extends Broadlink{

    function __construct($h = "", $m = "", $p = 80, $d = 0x2712) {

         parent::__construct($h, $m, $p, $d);

    }

    public function Set_Power($state){

        $packet = self::bytearray(16);
        $packet[0] = 0x02;
        $packet[4] = $state ? 1 : 0;

        $this->send_packet(0x6a, $packet);
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

}

class A1 extends Broadlink{

    function __construct($h = "", $m = "", $p = 80) {

         parent::__construct($h, $m, $p, 0x2714);

    }

    public function Check_sensors(){

        $data = array();

        $packet = self::bytearray(16);
        $packet[0] = 0x01;

        $response = $this->send_packet(0x6a, $packet);
        $err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
        

        if($err == 0){
            $enc_payload = array_slice($response, 0x38);

            if(count($enc_payload) > 0){

                $payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
                
                $data['temperature'] = ($payload[0x4] * 10 + $payload[0x5]) / 10.0;
                $data['humidity'] = ($payload[0x6] * 10 + $payload[0x7]) / 10.0;
                $data['light'] = $payload[0x8];
                $data['air_quality'] = $payload[0x0a];
                $data['noise'] = $payload[0x0c];

                switch ($data['light']) {
                    case 0:
                        $data['light_word'] = 'dark';
                        break;
                    case 1:
                        $data['light_word'] = 'dim';
                        break;                        
                    case 2:
                        $data['light_word'] = 'normal';
                        break;
                    case 3:
                        $data['light_word'] = 'bright';
                        break;
                    default:
                        $data['light_word'] = 'unknown';
                        break;
                }

                switch ($data['air_quality']) {
                    case 0:
                        $data['air_quality_word'] = 'excellent';
                        break;
                    case 1:
                        $data['air_quality_word'] = 'good';
                        break;                        
                    case 2:
                        $data['air_quality_word'] = 'normal';
                        break;
                    case 3:
                        $data['air_quality_word'] = 'bad';
                        break;
                    default:
                        $data['air_quality_word'] = 'unknown';
                        break;
                }

                switch ($data['noise']) {
                    case 0:
                        $data['noise_word'] = 'quiet';
                        break;
                    case 1:
                        $data['noise_word'] = 'normal';
                        break;                        
                    case 2:
                        $data['noise_word'] = 'noisy';
                        break;
                    default:
                        $data['noise_word'] = 'unknown';
                        break;
                }

            }

        }

        return $data;
        
    }   

}


class RM extends Broadlink{

	function __construct($h = "", $m = "", $p = 80, $d = 0x2712) {

    	 parent::__construct($h, $m, $p, $d);

    }

    public function Enter_learning(){

    	$packet = self::bytearray(16);
    	$packet[0] = 0x03;
    	$this->send_packet(0x6a, $packet);

	}

    public function Send_data($data){

    	$packet = self::bytearray(4);
    	$packet[0] = 0x02;

    	if(is_array($data)){
    		$packet = array_merge($packet, $data);
    	}
    	else{
    		for($i = 0 ; $i < strlen($data) ; $i+=2){
    			array_push($packet, hexdec(substr($data, $i, 2)));
    		}
    	}

    	$this->send_packet(0x6a, $packet);
    }	

	public function Check_data(){

		$code = array();

		$packet = self::bytearray(16);
  
    	$packet[0] = 0x04;
    	$response = $this->send_packet(0x6a, $packet);
    	$err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));
    	

    	if($err == 0){
	   		$enc_payload = array_slice($response, 0x38);

	   		if(count($enc_payload) > 0){

	    		$payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
		    	
				$code = array_slice($payload, 0x04);
    		}
    	}

    	return $code;
	}

	public function Check_temperature(){

    	$temp = 0;

    	$packet = $this->bytearray(16);

	    $packet[0] = 0x01;
    	$response = $this->send_packet(0x6a, $packet);
    	$err = hexdec(sprintf("%x%x", $response[0x23], $response[0x22]));

    	if($err == 0){
	   		$enc_payload = array_slice($response, 0x38);

	   		if(count($enc_payload) > 0){

	    		$payload = $this->byte2array(aes128_cbc_decrypt($this->key(), $this->byte($enc_payload), $this->iv()));
		    	
				$temp = ($payload[0x4] * 10 + $payload[0x5]) / 10.0;

    		}
    	}
      
      	return $temp;

    }

}
?>
