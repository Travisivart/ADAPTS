<?php
	require_once('connect.class.php');
	
	// Switches
	class switches{

		// Get All Switches
		public function getAllSwitches(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM switches");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}	
	}

?>