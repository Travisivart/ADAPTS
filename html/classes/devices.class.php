<?php
	require_once('connect.class.php');
	
	// devices
	class devices{

		// Get All Policies
		public function getAllDevices(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM devices");
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// Get device
		public function getDeviceByID($deviceID){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM devices where deviceID = :deviceID");
			$qry->bindParam(":deviceID",$deviceID, PDO::PARAM_INT);
			$qry->execute();
			$res = $qry->fetch(PDO::FETCH_ASSOC);
			return $res;
		}
	}

?>