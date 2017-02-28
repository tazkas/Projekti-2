<?php

	/*
		eanguess.php

		This file guesses the ean the user is typing in an input field.

	*/

	require_once "db.php";

	if(isset($_REQUEST["text"])){

		$text = strtolower($_REQUEST["text"]);

		$eans = array();

		$query = "select device_ean as ean, model from $table_device where device_ean like '%$text%' or model like '%$text%'";
		if($result = mysqli_query($conn, $query)){
			while($row = mysqli_fetch_assoc($result)){
				
				/* Without wildcards in sql query
				if(strpos(strtolower($row["ean"]), $text) !== false){
					$eans[] = "<div class='checkbox'><label><input type='checkbox' value='$row[ean]' name='device-delete-todelete[]'>$row[ean]  $row[model]</label></div>";
				}
				*/
				
				$eans[] = "<div class='checkbox'><label><input type='checkbox' value='$row[ean]' name='device-delete-todelete[]'>$row[ean]  $row[model]</label></div>";
				
			}
			$eans = implode("\n", $eans);
			echo $eans . "<br/> <input class='btn btn-success' type='submit' name='device-delete-submit' value='Poista valitut laitteet'>";
		}
	}

?>