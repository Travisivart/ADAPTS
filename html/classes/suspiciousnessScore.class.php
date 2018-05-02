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

		// get IPs by time
		public function getIPsOverTimeByDeviceAndTrace($trace_id, $name){
			$dbcon= new connect();
			error_log("$trace_id:".$trace_id);
			error_log("$name:".$name);
			//$qry=$dbcon->db1->prepare("SELECT floor(frame_time/15) as frame_time, COUNT(DISTINCT ip_dst) AS 'ip_dst' FROM packet_logs l, devices d WHERE ip_src <> ' ' AND ip_dst <> ' ' and trace_id=:trace_id and l.ip_src = d.ipv4 and d.name=$name GROUP BY trace_id , floor(frame_time/15)");
			$qry=$dbcon->db1->prepare("SELECT trace_id as name, floor(frame_time/15) as trace_id, COUNT(DISTINCT ip_dst) AS score FROM packet_logs l, devices d WHERE ip_src <> ' ' AND ip_dst <> ' ' and trace_id=:trace_id and l.ip_src = d.ipv4 and d.name=:name GROUP BY trace_id , floor(frame_time/15)");
			$qry->bindParam(":trace_id",$trace_id,PDO::PARAM_STR);
			$qry->bindParam(":name",$name,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// get Flows by time
		public function getFlowsOverTimeByDeviceAndTrace($trace_id, $name){
			$dbcon= new connect();
			error_log("$trace_id:".$trace_id);
			error_log("$name:".$name);
			//$qry=$dbcon->db1->prepare("SELECT floor(frame_time/15) as frame_time, COUNT(DISTINCT ip_dst) AS 'ip_dst' FROM packet_logs l, devices d WHERE ip_src <> ' ' AND ip_dst <> ' ' and trace_id=:trace_id and l.ip_src = d.ipv4 and d.name=$name GROUP BY trace_id , floor(frame_time/15)");
			$qry=$dbcon->db1->prepare("SELECT trace_id as name, floor(frame_time/15) as trace_id, COUNT(*) AS score FROM packet_logs l, devices d WHERE ip_src <> ' ' AND ip_dst <> ' ' and trace_id=:trace_id and l.ip_src = d.ipv4 and d.name=:name GROUP BY trace_id , floor(frame_time/15)");
			$qry->bindParam(":trace_id",$trace_id,PDO::PARAM_STR);
			$qry->bindParam(":name",$name,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// get Flows by time
		public function getBytesOverTimeByDeviceAndTrace($trace_id, $name){
			$dbcon= new connect();
			error_log("$trace_id:".$trace_id);
			error_log("$name:".$name);
			//$qry=$dbcon->db1->prepare("SELECT floor(frame_time/15) as frame_time, COUNT(DISTINCT ip_dst) AS 'ip_dst' FROM packet_logs l, devices d WHERE ip_src <> ' ' AND ip_dst <> ' ' and trace_id=:trace_id and l.ip_src = d.ipv4 and d.name=$name GROUP BY trace_id , floor(frame_time/15)");
			$qry=$dbcon->db1->prepare("SELECT trace_id as name, floor(frame_time/15) as trace_id, SUM(frame_len) AS score FROM packet_logs l, devices d WHERE ip_src <> ' ' AND ip_dst <> ' ' and trace_id=:trace_id and l.ip_src = d.ipv4 and d.name=:name GROUP BY trace_id , floor(frame_time/15)");
			$qry->bindParam(":trace_id",$trace_id,PDO::PARAM_STR);
			$qry->bindParam(":name",$name,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}
	}
?>