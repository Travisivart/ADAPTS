<?php
	require_once('connect.class.php');
	
	// Logs
	class logs{

		// Get All Logs
		public function getAllLogs(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM logs");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}

		/* Returns total network bandwidth per timestamp */
		public function getNetworkBandwidthPerTimestamp(){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT sum(delta_tx_bytes) as bytes, timestamp 
										FROM logs 
										GROUP BY timestamp
										ORDER BY timestamp desc
										LIMIT 100");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		/* Returns list of unique switches */
		public function getUniqueSwitches(){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT distinct(switch_id) FROM logs");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		/* Returns sum of bytes per timestamp for switch id */
		public function getBandwidthBySwitchID($switch_id){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT timestamp, sum(delta_tx_bytes) as bytes
										FROM logs
										WHERE switch_id = :switch_id
										GROUP BY timestamp
										ORDER BY timestamp desc
										LIMIT 50");
			$qry->bindParam(":switch_id",$switch_id,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		/* Returns total network bandwidth per timestamp and switch */
		public function getNetworkBandwidthPerTimestampSwitch(){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT switch_id, timestamp, sum(delta_tx_bytes) as bytes
										FROM logs 
										GROUP BY timestamp, switch_id
										ORDER BY timestamp, switch_id");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

		/* Returns total network bandwidth per timestamp, switch, and port */
		public function getNetworkBandwidthPerTimestampSwitchPort(){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT sum(delta_tx_bytes) as bytes, timestamp, switch_id, port_id 
										FROM logs 
										GROUP BY timestamp, switch_id, port_id
										ORDER BY timestamp, switch_id, port_id");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

	}

?>