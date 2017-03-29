<?php

	/*

		lainat.php
	
		This file displays and handles all loans.

	*/

	require_once "templates.php";
	require_once "db.php";
	require_once "functions.php";


	executeHeader("Laiterekisteri", "lainat", "container");

	// These variables have all the html code for the tables.
	$t1_1 = "<div class='container'>
	<h2>Lainat</h2>
	<table id='T1' class='table table-hover'>
		<thead>
			<tr>
				<th>Käyttäjä</th>
				<th>Lainauksen päivämäärä</th>
				<th>Lainauksen päättymispäivä</th>
				<th>Laite</th>
				<th>Määrä</th>
				<th>Kommentit</th>
				<th>Toiminnot</th>
			</tr>
		</thead>
		<tbody>";
		
	$t1_2 =	"</tbody></table></div>";
	
	$t2_1 = "<div class='container'>
	<h2>Varaukset</h2>
	<table id='T2' class='table table-hover'>
		<thead>
			<tr>
				<th>Käyttäjä</th>
				<th>Varauksen päivämäärä</th>
				<th>Lainauksen aloituspäivä</th>
				<th>Lainauksen päättymispäivä</th>
				<th>Laite</th>
				<th>Määrä</th>
				<th>Kommentit</th>
				<th colspan='2' style='text-align:center;'>Toiminnot</th>
			</tr>
		</thead>
		<tbody>";
		
	$t2_2 = "</tbody></table></div>";
	
	//SQL query for all loans & reservations
	$query1 = "SELECT loan.loan_group, loan.id, loan.loan_type, loan.username,loan.reservation_date ,loan.loan_date, loan.end_date, loan.info, loan.device_model from loan";
	$query2 = "select loan_group, count(*) as count from loan group by loan_group order by count desc";
	
	$groups = array();
	$used_groups = array();

	if($q2_result = mysqli_query($conn, $query2)){
				
		while($row = mysqli_fetch_assoc($q2_result)){
			$groups[ $row["loan_group"] ] = $row["count"];
		}	
	}

	$reservations = array();
	$loans = array();

	//error message incase error occurs during SQL query
	if($result = mysqli_query($conn, $query1)){	

		//read database data and echo reservation infromation on the website
		while ($row = mysqli_fetch_assoc($result))
		{			
			// Store reservations to be echoed later
			if ($row["loan_type"] == "reservation"){
				
				if(!in_array($row["loan_group"], $used_groups)){
					$lg = $row["loan_group"];
					$reservations[] = "<tr class='active'><td>$row[username]</td>" .
					"<td>" . timeFormat($row["reservation_date"], "fin") . "</td><td>" . timeFormat($row["loan_date"], "fin") . "</td>" .
					"<td>" . timeFormat($row["end_date"], "fin") . "</td><td>$row[device_model]</td>" . 
					"<td style='text-align:center;'>" . $groups[$row["loan_group"]] . "</td>" . 
					"<td>$row[info]</td><td><input class='form-control btn-success' type='button' value='Hyväksy' onclick='handleLoan(\"$lg\", \"confirm\", this)'/></td>" .
					"<td><input class='form-control btn-danger' type='button' value='Hylkää' onclick='handleLoan(\"$lg\", \"decline\", this)'/></td></tr>";
					$used_groups[] = $row["loan_group"];
				}
	
			}

			// store loans
			else if($row["loan_type"] == "loan"){

				if(!in_array($row["loan_group"], $used_groups)){
					$id = $row["id"];
					$loans[] = "<tr><td>$row[username]</td><td>" . timeFormat($row["loan_date"], "fin") . 
					"</td><td>" . timeFormat($row["end_date"], "fin") . "</td><td>$row[device_model]</td>" . 
					"<td style='text-align:center;'>" . $groups[$row["loan_group"]] . "</td>" . "<td>$row[info]</td>" . 
					"<td><input class='form-control btn-info' type='button' value='Palautettu' onclick='returnAlert(\"$id\", this)'/></td></tr>";
					$used_groups[] = $row["loan_group"];
				}
				
			}
			
		}
	}
	
	// Echo loans and reservations if there are any,
	// echo infomessage otherwise
	if(!empty($loans)){
		echo $t1_1;
		foreach($loans as $l) echo $l;
		echo $t1_2;
	} else {
		echo $t1_1;
		echo $t1_2;
		//echo "<h2>Lainat</h2><br/><div class='well'>Ei lainassa olevia laitteita</div><br/><br/>";
	}
	
	if(!empty($reservations)){

		echo $t2_1;
		foreach($reservations as $r) echo $r;
		echo $t2_2;
	} else echo "<h2>Varaukset</h2><br/><div class='well'>Ei alustavia varauksia</div><br/><br/>";

	executeFooter(); 
	
?>