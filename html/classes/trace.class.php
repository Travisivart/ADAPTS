<?php
	require_once('connect.class.php');
	
	// Trace
	class trace{

		// Get Trace IDs
		public function getAllTraceIDs(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT DISTINCT trace_id FROM packet_logs ORDER BY trace_id");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}
	}
?>