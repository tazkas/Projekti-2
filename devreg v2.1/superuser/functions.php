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

?>