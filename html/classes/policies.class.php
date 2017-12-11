<?php
	require_once('connect.class.php');
	include_once("utilities.class.php");
	include_once("devices.class.php");
	
	// policies
	class policies{

		// Get All Policies
		public function getAllPolicies(){
			$dbcon= new connect();
			$qry=$dbcon->db1->prepare("SELECT * FROM policies");
			$qry->execute();
			$res = $qry->fetchAll(PDO::FETCH_ASSOC); 
			return $res;
		}

		// Insert new policy
		public function setPolicy($policies){
			$dbcon = new connect();
			$dbcon->db1->beginTransaction();
			$res = "";
			try{
				$qry=$dbcon->db1->prepare("DELETE FROM policies");
				$res = $qry->execute();
				if($res){
					// Bind Values
					$insertString = "INSERT INTO policies VALUES (:policyID, :deviceID, :policy, :loaded)";
					for($i=0; $i<count($policies);$i++){
						// Prepare String
						$qry=$dbcon->db1->prepare($insertString);
						
						$pol = $policies[$i];
						$policyID = $pol["policyID"];
						$deviceID = $pol["deviceID"];
						$policyValue = $pol["policyValue"];
						$loaded = "0";

						$k = strval($i);
						$qry->bindParam(":policyID",$policyID, PDO::PARAM_STR);
						$qry->bindParam(":deviceID",$deviceID, PDO::PARAM_STR);
						$qry->bindParam(":policy",$policyValue, PDO::PARAM_STR);
						$qry->bindParam(":loaded",$loaded, PDO::PARAM_STR);
						$res = $qry->execute();
						if(!$res){
							throw new Exception("Unable to insert policy");
							return;
						} 
					}
					$dbcon->db1->commit();
				} else { // delete failed
					throw new Exception("Unable to delete policies from table");
					return;
				}
			} catch (PDOException $e){
				$dbcon->db1->rollBack();
				echo $e->getMessage();
				$res = false;
			} catch(Exception $e){
				$dbcon->db1->rollBack();
				echo $e->getMessage();
				$res = false;
			}
			return json_encode($res);
		}


		// Block policy
		public function blockPolicy($deviceID){
			$dbcon = new connect();
			$dbcon->db1->beginTransaction();
			$res = "";
			try{
				$qry=$dbcon->db1->prepare("DELETE FROM policies WHERE deviceID = :deviceID");
				$qry->bindParam(":deviceID", $deviceID, PDO::PARAM_STR);
				$res = $qry->execute();

				if($res){
					$utilities = new utilities();
					$policyID = $utilities->randomNumber();
					$policyID = strtoupper($policyID);

					$devices = new devices();
					$device = $devices->getDeviceByID($deviceID);
					$ip = $device["ipv4"];
					$newpol = 'Filter(SwitchEq(51570677359425) & IP4SrcEq("'.$ip.'")) >> SetPort(3) | Filter(SwitchEq(99947915534402) & IP4SrcEq("'.$ip.'")) >> SetPort(3)';

					$insertString = "INSERT INTO policies VALUES (:policyID, :deviceID, :policy, :loaded)";
					$qry=$dbcon->db1->prepare($insertString);

					$loaded = "0";

					$qry->bindParam(":policyID", $policyID, PDO::PARAM_STR);
					$qry->bindParam(":deviceID", $deviceID, PDO::PARAM_STR);
					$qry->bindParam(":policy", $newpol, PDO::PARAM_STR);
					$qry->bindParam(":loaded", $loaded, PDO::PARAM_INT);
					$res = $qry->execute();

					if(!$res){
						throw new Exception("Unable to block device");
						return;
					} else {
						$dbcon->db1->commit();
					}
				} else { // delete failed
					throw new Exception("Unable to delete policies from table");
					return;
				}
			} catch (PDOException $e){
				$dbcon->db1->rollBack();
				echo $e->getMessage();
				$res = false;
			} catch(Exception $e){
				$dbcon->db1->rollBack();
				echo $e->getMessage();
				$res = false;
			}
			return json_encode($res);
		}


	}

?>

