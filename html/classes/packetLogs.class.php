<?php
	require_once('connect.class.php');
	
	// Packet Logs
	class packetLogs{

		// Get All Logs
		public function getAllPacketLogs(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM packet_logs ORDER BY timestamp desc LIMIT 100");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}

		// Total number of ip connections per device
		public function getIPConnPerDevice(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT ip_src, COUNT(DISTINCT ip_dst) AS total
										FROM mtd.packet_logs 
										WHERE ip_src <> ' ' and ip_dst <> ' ' group by ip_src order by COUNT(DISTINCT ip_dst) desc");
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// Total number of ip connections per device within the date range
		public function getIPConnPerDeviceByDate($startDate, $endDate){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT COUNT(DISTINCT ip_dst) AS total
										FROM mtd.packet_logs 
										WHERE ip_src <> ' ' and ip_dst <> ' ' and from_unixtime(frame_time) between timestamp(:startDate) and timestamp(:endDate) order by COUNT(DISTINCT ip_dst) desc");
			$qry->bindParam(":startDate",$startDate, PDO::PARAM_STR);
			$qry->bindParam(":endDate",$endDate, PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// Total number of flows per device
		public function getFlowsPerDevice(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT ip_src, ip_dst, count(*) as total
										FROM mtd.packet_logs 
										WHERE ip_src <> ' ' and ip_dst <> ' ' group by ip_src, ip_dst order by count(*) desc");
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// Total number of flows per device within the date range
		public function getFlowsPerDeviceByDate($startDate, $endDate){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT count(*) as total
										FROM mtd.packet_logs 
										WHERE ip_src <> ' ' and ip_dst <> ' ' and from_unixtime(frame_time) between timestamp(:startDate) and timestamp(:endDate) order by count(*) desc");
			$qry->bindParam(":startDate",$startDate, PDO::PARAM_STR);
			$qry->bindParam(":endDate",$endDate, PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// Total number of bytes transmitted to each ip
		public function getNumberOfBytesPerIP(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT ip_src, ip_dst, SUM(frame_len) as frame_length, count(*) as total
										FROM mtd.packet_logs where ip_src <> ' ' and ip_dst <> ' ' group by ip_src, ip_dst order by SUM(frame_len) desc");
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// Total number of bytes transmitted to each ip within the date range
		public function getNumberOfBytesPerIPByDate($startDate, $endDate){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT SUM(frame_len) as frame_length, count(*) as total
										FROM mtd.packet_logs where ip_src <> ' ' and ip_dst <> ' ' and from_unixtime(frame_time) between timestamp(:startDate) and timestamp(:endDate) order by SUM(frame_len) desc");
			$qry->bindParam(":startDate",$startDate, PDO::PARAM_STR);
			$qry->bindParam(":endDate",$endDate, PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}
	}

?>