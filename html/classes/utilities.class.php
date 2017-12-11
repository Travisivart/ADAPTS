<?php
	// Utility Functions
	class utilities{

		// Requires filename with extension
		public function readCSVFile($filename){
			$fid = fopen("../data/".$filename,"r");
			$filearray = array();
			// Check if file exists
			if ($fid !== FALSE) {
				// Get each row and add row to array
			    while (($data = fgetcsv($fid, 1000, ",")) !== FALSE) {
			    	array_push($filearray,$data);
		      	}
			    fclose($fid);
		  	}
		  	return $filearray;
		}


		// Method: POST, PUT, GET etc
		// Data: array("param" => "value") ==> index.php?param=value
		// Call API to 
		public function CallAPI($method, $url, $data = false)
		{
		    $curl = curl_init();

		    switch ($method)
		    {
		        case "POST":
		            curl_setopt($curl, CURLOPT_POST, 1);

		            if ($data)
		                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		            break;
		        case "PUT":
		            curl_setopt($curl, CURLOPT_PUT, 1);
		            break;
		        default:
		            if ($data)
		                $url = sprintf("%s?%s", $url, http_build_query($data));
		    }

		    // Optional Authentication:
		    #    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		    #    curl_setopt($curl, CURLOPT_USERPWD, "username:password");

		    curl_setopt($curl, CURLOPT_URL, $url);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		    $result = curl_exec($curl);

		    curl_close($curl);

		    return $result;
		}

		// Get Random UID
		public function randomNumber(){
		    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		        // 32 bits for "time_low"
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

		        // 16 bits for "time_mid"
		        mt_rand( 0, 0xffff ),

		        // 16 bits for "time_hi_and_version",
		        // four most significant bits holds version number 4
		        mt_rand( 0, 0x0fff ) | 0x4000,

		        // 16 bits, 8 bits for "clk_seq_hi_res",
		        // 8 bits for "clk_seq_low",
		        // two most significant bits holds zero and one for variant DCE1.1
		        mt_rand( 0, 0x3fff ) | 0x8000,

		        // 48 bits for "node"
		        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		    );
		}
	}

?>