<?
include("include/broadlink.class.php");
$path = realpath(dirname($_SERVER['SCRIPT_FILENAME']));

$command = isset($_GET['command']) ? $_GET['command'] : "";
$device = isset($_GET['device']) ? $_GET['device'] : "";

$json = array();

$json['code'] = -1;

if(file_exists($path . "/config/broadlink/$device" . ".json")){

	$info = json_decode(file_get_contents($path . "/config/broadlink/$device" . ".json"), true);

	$json['code'] = -1;

	if(Broadlink::getdevtype($info['devtype']) == 2){

		$rm = Broadlink::CreateDevice($info['host'], $info['mac'], 80, $info['devtype']);

		$rm->Auth();
		$rm->Enter_learning();

		sleep(10);

		$json['hex'] = $rm->Check_data();
		
		$json['code'] = 1;

		$json['hex_number'] = '';

		foreach ($json['hex'] as $value) {
	    	$json['hex_number'] .= sprintf("%02x", $value);
	    }

		if(strlen($command) > 0 && count($json['hex']) > 0){

	    	file_put_contents($path . "/config/broadlink/command/$command" . ".command", $json['hex_number'] );

		}




	}	

}

$result = json_encode($json, JSON_NUMERIC_CHECK);

header('Content-Type: application/json');
header("Content-length: " . strlen($result));

echo $result;

flush();
?>
