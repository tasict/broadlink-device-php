<?
include("include/broadlink.class.php");
$path = realpath(dirname($_SERVER['SCRIPT_FILENAME']));

$command = isset($_GET['command']) ? $_GET['command'] : "";
$device = isset($_GET['device']) ? $_GET['device'] : "";

$json = array();

$json['code'] = -1;

if(strcmp($command, 'temperature') == 0){
	if(file_exists($path . "/config/broadlink/$device" . ".json")){

		$info = json_decode(file_get_contents($path . "/config/broadlink/$device" . ".json"), true);
		
		if(Broadlink::getdevtype($info['devtype']) == 2 || Broadlink::getdevtype($info['devtype']) == 3){

			$rm = Broadlink::CreateDevice($info['host'], $info['mac'], 80, $info['devtype']);
			$rm->Auth();

			$json['temperature'] = $rm->Check_temperature();

		}
		else{
			$json['temperature'] = 0;
		}

		
		$json['code'] = 1;

	}

}
else if(file_exists($path . "/config/broadlink/command/$command" . ".command") && file_exists($path . "/config/broadlink/$device" . ".json")){

	$info = json_decode(file_get_contents($path . "/config/broadlink/$device" . ".json"), true);
	$data = file_get_contents($path . "/config/broadlink/command/$command" . ".command");

	$json['code'] = -1;	

	if(Broadlink::getdevtype($info['devtype']) == 2){

		$rm = Broadlink::CreateDevice($info['host'], $info['mac'], 80, $info['devtype']);

		$rm->Auth();
		$rm->Send_data($data);

		$json['code'] = 1;

	}

}

$result = json_encode($json, JSON_NUMERIC_CHECK);

header('Content-Type: application/json');
header("Content-length: " . strlen($result));

echo $result;

flush();
?>
