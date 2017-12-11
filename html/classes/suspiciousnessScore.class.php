<?php
	require_once('connect.class.php');
	
	// Suspiciousness Score
	class suspiciousnessScore{

		// Get All SS
		public function getAllSuspiciousnessScores(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM suspiciousness_scores");
			$qry->execute();
			$res = $qry->fetchAll(); 
			return $res;
		}

		// get SS by user/device
		public function getSuspiciousnessScoreByDevice($name){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM suspiciousness_scores where name=:name");
			$qry->bindParam(":name",$name,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// get SS by time
		public function getSuspiciousnessScoreByTime(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM suspiciousness_scores_by_time");
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}
	}
?>