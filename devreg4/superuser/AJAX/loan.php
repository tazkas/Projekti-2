<?php

	require_once "db.php";
	require_once "functions.php";

	if(isset($_POST["condition"])){

		// $lg = loan_group
		$lg = $_POST["lg"];
		$condition = $_POST["condition"];
		$loan = "reservation";

		if($condition == "confirm"){
			$loan = "loan";
		} else if($condition == "decline"){
			$loan = "declined";
		}
		
		$query = "UPDATE loan SET loan_type = '" . $loan . "' WHERE loan.loan_group = " . $lg;
		
		$result = mysqli_query($conn, $query);

		if($result){
			echo "OK";
		}else{
			echo mysqli_error($conn);
		}
	}
	
?>