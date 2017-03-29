<?php

	require_once "db.php";


	// Fetch calendar events
	if(isset($_POST["fetchEvents"])){

		$model = $_POST["modelName"];
		$groups = array();
		$return = "";
		$query1 = "select loan_group, username, loan_date, end_date  from loan where device_model='$model' and loan_type='loan';";
		$query2 = "select loan_group, count(*) as count from loan group by loan_group order by count desc";
		
		
		if($q2_result = mysqli_query($conn, $query2)){
			
			while($row = mysqli_fetch_assoc($q2_result)){
				$groups[ $row["loan_group"] ] = $row["count"];
			}
			
			$used_groups = array();
		
			if($q1_result = mysqli_query($conn, $query1)){
				while($row = mysqli_fetch_assoc($q1_result)){
					if(!in_array($row["loan_group"], $used_groups)){
						$return .= "{ \"title\": \"VARATTU " . $groups[$row["loan_group"]] . " kpl, $row[username]\", \"start\": \"$row[loan_date]\", \"end\": \"$row[end_date]\", \"editable\": false, \"color\": \"#ff993f\" };";
						$used_groups[] = $row["loan_group"];
					}	
				}
				$return = substr($return, 0, strlen($return)-1);
			}
			
			echo $return;
			
		}
		
	}	
	

?>