<?php
	require_once('connect.class.php');
	
	// QVM
	class qvm{

		// Get All QVMs
		public function getAllQVMs(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM qvm");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}

		// Get All QVMs in each month
		public function getAllQVMsByMonth(){
			$dbcon = new connect();
			$qry=$dbcon->db1->prepare("SELECT date_format(qvmStartTime,'%M %Y') AS dates, COUNT(qvmUID) AS totalCount
										FROM qvm
										GROUP BY dates");
			$qry->execute();
			$res = $qry->fetchAll();
			return $res;
		}

	}

?>