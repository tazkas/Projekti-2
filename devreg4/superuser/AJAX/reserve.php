<?php

	require_once "db.php";
	require_once "functions.php";

	if(isset($_POST["reserve"])){

		// RESERVATION OK
		//$username = $_SERVER[];
		$username = "Billy Greenhorn"; // Temp username
		$owner_id = $_POST["oid"]; // Temp owner
		$model    = $_POST["model"];
		$amount   = $_POST["amount"];

		$endSet = $_POST["endSet"];

		// End time
		if ($endSet == "true"){
			$end = explode(" ", $_POST["end"]);
			$end = date("Y-m-d H:i:s", strtotime($end[1] . " " .  $end[2] . " " . $end[3] . " " . $end[4]));
			$end = timeFormat($end, "sql");
		} else{
			$end = $_POST["end"];
			$end = date("Y-m-d H:i:s", $end);
			$end = timeFormat($end, "sql");
		}
		
		// Start time
		$start = explode(" ", $_POST["start"]);
		$start = date("Y-m-d H:i:s", strtotime($start[1] . " " .  $start[2] . " " . $start[3] . " " . $start[4]));
		$start = timeFormat($start, "sql");

		// Reservation date
		$resdate = timeFormat(time(), "sql");
		
		// Check if reservation can be authorized
		// Need to know: date of reservation, available devices on this specified date.
		$result = ReservationOK($conn, $model, $amount, $owner_id, $start, $end);
		if ($result["status"] === true){
			
			$loan_group = generateHash();
		
			$query = "insert into loan (device_model, loan_group, owner_id, loan_type, username, loan_date, end_date, reservation_date) 
			values ";
			
			for($i = 0; $i < $amount; $i++){
				if($i != 0){
					$query .= ",";
				}
				$query .= "('$model', '$loan_group', '$owner_id', 'reservation', '$username', '$start', '$end', '$resdate')";
			}
			
			$result = mysqli_query($conn, $query);

			if($result) echo "OK";
			else echo mysqli_error($conn);
			
		} else {
			
			echo $result["message"];
		}
	}

?>