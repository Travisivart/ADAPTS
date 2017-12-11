<?php
	include_once("../classes/devices.class.php");
	include_once("../classes/utilities.class.php");

	// Device handlers
	if(isset($_POST["action"])){
		$action = $_POST["action"];
		if(strcasecmp($action, "getDeviceList")==0){
			$devices = new devices();
			$deviceList = $devices->getAllDevices();

			$utilities = new utilities();
			$policyID = $utilities->randomNumber();

			$arr["deviceList"] = $deviceList;
			$arr["policyID"] = $policyID;
			echo json_encode($arr);
		}
		return;
	}
?>