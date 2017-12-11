<?php
	require_once('connect.class.php');
	
	// admin
	class admin{

		// Verify login
		public function login($username, $password){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * from login where username=:username");
			$qry->bindParam(":username",$username,PDO::PARAM_STR);
			$qry->execute();
			$res = $qry->fetch();
			
			// Check if passwords match
			if($res > 0){
				$match = 0;
				$convertedPassword = "";
				if($res["salt"] != ""){
					$db_pass = $res['passwd'];
					$salt = $res['salt'];
					$saltedPassword = $password . $salt;
					$convertedPassword = hash("sha256", $saltedPassword);
					if ($convertedPassword == $db_pass){
						$match = 1;
					} else {
						$res = false;
					}
				}
			}
			return $res;
		}
	}

?>