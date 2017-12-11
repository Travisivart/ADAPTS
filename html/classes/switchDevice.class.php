<?php
	require_once('connect.class.php');
	
	// Switches
	class switchDevice{

		// Get all switch-device relationship
		public function getAllSwitchDevice(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT s.switchID, s.name as switch_name, sd.port as port, s.totalPorts, d.deviceID, d.name as device_name, d.ipv4 FROM mtd.switches s, mtd.switch_devices sd, mtd.devices d WHERE s.switchID = sd.switchID AND sd.deviceID = d.deviceID;");
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC);
			return $res;
		}		

	}

?>