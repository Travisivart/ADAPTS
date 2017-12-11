<?php
	require_once('../DBINFO.inc');
	date_default_timezone_set('America/Chicago');
	class connect{
		public $db1;
		public function __construct()
		{
			try
			{
				//require(__DIR__ . '/../config.php');
				$this->db1 = new PDO('mysql:host='.HOSTNAME.';port=3306;dbname='.DBNAME, USERNAME, PASSWORD);
				//Change the dbname value according to what you have named the database. localhost connection like mysql connect.
			}
			catch (PDOException $ex)
			{
				echo 'Connection failed: ' . $ex->getMessage();
			}
		}
	}
?>
