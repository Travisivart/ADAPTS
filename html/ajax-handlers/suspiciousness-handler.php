<?php
	include_once("../classes/packetLogs.class.php");
	include_once("../classes/suspiciousnessScore.class.php");

	// Device handlers
	if(isset($_POST["action"])){
		$action = $_POST["action"];
		$packetLogs = new packetLogs();
		if(strcasecmp($action, "getBytes")==0){
			$bytes = array();
			if(isset($_POST["startDate"]) && isset($_POST["endDate"])){
				$startDate = $_POST["startDate"];
				$endDate = $_POST["endDate"];
				$bytes = $packetLogs->getNumberOfBytesPerIPByDate($startDate, $endDate);
			} else {
				$bytes = $packetLogs->getNumberOfBytesPerIP();
			}
			echo json_encode($bytes);
		} else if(strcasecmp($action, "getSuspiciousnessScoreByTime")==0){
			$suspiciousness = new suspiciousnessScore();
			$graphData = $suspiciousness->getSuspiciousnessScoreByTime();
			echo json_encode($graphData);
		}
		return;
	}
?>