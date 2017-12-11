<?php
	require_once('connect.class.php');
	
	// Users
	class users{

		// Get All Users
		public function getAllUsers(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM users");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}

		// Get All Users in each month
		public function getAllUsersByMonth(){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT date_format(connectionStartTime,'%M %Y') AS dates, 
											count(userUID) AS totalCount
										FROM users
										GROUP BY dates
										ORDER BY dates");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

	}

?>