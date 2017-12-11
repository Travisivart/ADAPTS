<?php
	require_once('connect.class.php');
	
	// Servers
	class servers{

		// Get All Servers
		public function getAllServers(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM servers");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}

		// Get All Servers in each month
		public function getAllServersByMonth(){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT date_format(serverCreatedOn,'%M %Y') AS dates, 
													count(serverUID) AS totalCount
										FROM servers
										GROUP BY dates
										ORDER BY dates");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

	}

?>