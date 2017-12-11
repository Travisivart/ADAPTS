<?php
	require_once('connect.class.php');
	
	// Rules
	class rules{

		// Get All Rules
		public function getAllRules(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM rules");
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// Insert new rule
		public function setRule($rule){
			$dbcon= new connect();
			$dbcon->db1->beginTransaction();
			$res = "";
			try{
				$qry=$dbcon->db1->prepare("TRUNCATE rules");
				$res = $qry->execute();

				$qry=$dbcon->db1->prepare("INSERT INTO rules VALUES (:rule, 0)");
				$qry->bindParam(":rule",$rule, PDO::PARAM_STR);
				$res = $qry->execute();
				$dbcon->db1->commit();
			} catch (PDOException $e){
				$err = $e->getMessage();
				$res = false;
			}
			return $res;
		}
	}

?>