<?php

	/*
		functions.php
		
		All external reusable functions are placed here.
		
	*/
	
	// Upload function used to upload files to specified folders.
	// Returns true if upload succeeds, false otherwise
	function upload($folder, $name){

		$target_file = "images/" . $folder . "/" . basename($_FILES[$name]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		// Check if image file is a actual image or fake image
		// Some images cannot be uploaded:
		// 			For some reason the $_FILES[$name]["tmp_name"] is empty.
		if(!empty($_FILES[$name]["tmp_name"])){
			$check = getimagesize($_FILES[$name]["tmp_name"]);
		    if($check !== false) {
		        $uploadOk = 1;
		    } else {
		        $uploadOk = 0;
		    }

		    // Allow certain image formats
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "JPG")
			    $uploadOk = 0;

			if ($uploadOk == 0) {
	    		echo "ERROR: incorrect filetype";
	    		return false;
			// if everything is ok, try to upload file
			} else {
			    if (move_uploaded_file($_FILES[$name]["tmp_name"], $target_file)) {
			        // File uploaded successfully
			        return true;
			    } else {
			        // File upload failed
			        echo "ERROR: move_uploaded_file() failed";
			        return false;
			    }
			}
		}
		else {
			echo "ERROR: £_FILES[$name]['tmp_name'] is empty.";
			return false;
		}
	}


	// Format time
	// toFormat [ fin, sql, timestamp ]
	function timeFormat($time, $toFormat, $seconds=false){
		if(strtotime($time) !== false) $time = strtotime($time);
		switch($toFormat){
			case "fin":
				if($seconds) $time = date("d.m.Y H:i:s", $time);
				else $time = date("d.m.Y H:i", $time);
				break;
			case "sql":
				if($seconds) $time = date("Y-m-d H:i:s", $time);
				else $time = date("Y-m-d H:i", $time);
				break;
			case "timestamp":
				$time = strtotime($time);
				break;
			default:
				$time = "Invalid argument.";
				break;
		}
		return $time;
	}

	// Generates a hash based on a random number and 
	// microtime unix epoch timestamp
	function generateHash(){
		return (int)mt_rand() . (int)round(microtime(true));
	}
	
	// Check if devices available, returns bool status and string message
	function ReservationOK($conn, $model, $amount, $ownerID, $start, $end){
			
		$result = ["status" => false, "message" => "ERROR: Query failed."];
			
		$query1 = "select * from loan where 
				device_model = '$model' and 
				owner_id = '$ownerID' and 
				loan_type = 'loan' and 
				end_date >= '$start'";
				
		$query2 = "SELECT count(model) as dcount from device where owner_id = '$ownerID' and model = '$model'";
		
		// First we get the total amount of devices the owner owns, then we 
		// check if any of them are available
		if($q2_result = mysqli_query($conn, $query2)){
			$row = mysqli_fetch_assoc($q2_result);
			$total_amount = intval($row["dcount"]);
			
			if($q1_result = mysqli_query($conn, $query1)){
				
				// check if there are enough devices available at that time
				// num_rows == number of loaned devices
				if($total_amount - mysqli_num_rows($q1_result) >= $amount) {
					$result["status"] = true;
				} else {
					$result["message"] = "Varausta ei hyväksytty, ei tilaa.";
				}
				
			}
		}
				
		return $result;
	}
	
	
	
	
	
	
	
	
	
	
?>