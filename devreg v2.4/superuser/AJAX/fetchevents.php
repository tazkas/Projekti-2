<?php

	require_once "db.php";


	// Fetch calendar events
	if(isset($_POST["fetchEvents"])){

		$model = $_POST["modelName"];
		$return = "";
		$query = "select * from loan where device_model='$model' and type='loan';";
		if($result = mysqli_query($conn, $query)){
			while($row = mysqli_fetch_assoc($result)){
				$return .= "{ \"title\": \"VARATTU, $row[username]\", \"start\": \"$row[loan_date]\", \"end\": \"$row[end_date]\", \"editable\": false, \"color\": \"#ff993f\" };";
			}
			$return = substr($return, 0, strlen($return)-1);
		}
		echo $return;
	}	
	

?>