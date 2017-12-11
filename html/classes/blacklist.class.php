<?php
	require_once('connect.class.php');
	
	// Blacklist
	class blacklist{

		// Get All Blacklisted IPs
		public function getAllBlacklist(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT ipAddress, macAddress, max(blacklistedOn) as blacklistedOn
										FROM blacklist
										GROUP BY ipAddress, macAddress;");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}
	}

?>