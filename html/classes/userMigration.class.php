<?php
	require_once('connect.class.php');
	
	// Users
	class userMigration{

		// Get All Users
		public function getAllUserMigrations(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM usermigration");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}

		// Get All Users in each month
		public function getAllUserMigrationsByMonth(){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT date_format(migrationStartTime,'%M %Y') AS dates, 
											count(userIP) AS totalCount
										FROM usermigration
										GROUP BY dates
                                        order by dates");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

	}

?>