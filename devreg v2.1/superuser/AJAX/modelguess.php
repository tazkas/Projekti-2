<?php

	/*
		modelguess.php

		This file guesses the model the user is looking for

	*/

	require_once "db.php";

	if(isset($_REQUEST["editdevice"])){

		$text = $_REQUEST["text"];
		$models = "";
		$query = "select distinct model from $table_device where model like '%$text%'";
		if($result = mysqli_query($conn, $query)){
			while($row = mysqli_fetch_assoc($result)){
				$models .= "<div class='radio'><label><input type='radio' name='edit-device-selected' value='$row[model]' onclick='editdeviceLoadInfo(this.value)'>$row[model]</label></div>\n";
			}
			echo $models;
		}
	}

	elseif(isset($_REQUEST["text"])){

		$text = $_REQUEST["text"];
		$models = "";
		$query = "select distinct model from $table_device where model like '%$text%'";
		if($result = mysqli_query($conn, $query)){
			while($row = mysqli_fetch_assoc($result)){
				$models .= "<div class='radio'><label><input type='radio' name='device-selected' data-toggle='modal' data-target='#new-device-old' form='new-device-old-form' value='$row[model]'>$row[model]</label></div>\n";
			}
			echo $models;
		}
	}

?>