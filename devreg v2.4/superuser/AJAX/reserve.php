<?php

	require_once "db.php";
	require_once "functions.php";

	if(isset($_POST["reserve"])){

		//$username = $_SERVER[];
		$username = "Billy Greenhorn"; // Temp username
		$owner_id = 1; // Temp owner
		$model    = $_POST["model"];

		$endSet = $_POST["endSet"];

		if ($endSet == "true"){
			$end = explode(" ", $_POST["end"]);
			$end = date("Y-m-d H:i:s", strtotime($end[1] . " " .  $end[2] . " " . $end[3] . " " . $end[4]));
		}

		else{
			$end = $_POST["end"];
			$end = date("Y-m-d H:i:s", $end);
		}

		$start = explode(" ", $_POST["start"]);
		$start = date("Y-m-d H:i:s", strtotime($start[1] . " " .  $start[2] . " " . $start[3] . " " . $start[4]));

		$resdate = timeFormat(time(), "sql");

		$query = "insert into loan (device_model, owner_id, type, username, loan_date, end_date, reservation_date) 
		values ('$model', $owner_id, 'reservation', '$username', '$start', '$end', '$resdate')";
		
		$result = mysqli_query($conn, $query);

		if($result) echo "OK";
		else echo mysqli_error($conn);
	}

?>