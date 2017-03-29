<?php 

	/*
	
		laite.php
		
		This page generates a device based on the 'model' it receives in
		the url ( GET ).
		
		Example:

			laite.php?model=lenovo      - Generates a page for the model 'lenovo'

		TODO:
			- save queried data to session to prevent duplicate queries
	
	*/

	session_start();

	// Check if the user is trying to access the page in the wrong way
	if(!isset($_GET["model"])){
		header("Location: index");
		exit();
	}
	
	// Include all required extensions
	require_once "db.php";
	require_once "functions.php";
	require_once "templates.php";

	// Device page displays the main content of the page
	$device_page = "<div class='row'>";

	// Device id given in url
	if(isset($_GET["model"])) {

		$assoc = array();			// Device owner info stored here { $id => {"name" => $name, "device" => {$device1 => $state, $device2 => $state ...}, "image" => $image} }
		$model  = $_GET["model"];   // Model of the device

		// Check if empty string
		if($model == ""){
			header("Location: index");
			exit();
		}

		// Display core html
		executeHeader($model);

		$tmp_img = "";

		$query  = "select device.device_ean as ean, device.model, device.state, device.description, device.image, owner.firstname, owner.lastname, owner.id as oid " . 
		          "from $table_device join $table_owner on device.owner_id = owner.id where device.model='$model'";
       
		// Successful query
		if($result = mysqli_query($conn, $query)) {
			if(mysqli_num_rows($result) > 0) {
				
				// Fetch all owners and eans of the specified model
				$prev = 0;
				$i    = -1;
				while($row = mysqli_fetch_assoc($result)){

					// We don't want duplicate owners, this if statement
					// helps us later (...in the next foreach)
					if($prev != $row["oid"]){
						$i++;
						$prev = $row["oid"];
					}

					$id = $row["oid"];
					$name = $row["firstname"] . " " . $row["lastname"];
					
					$assoc[$i]["id"] = $id; 
					$assoc[$i]["name"] = $name;
					$assoc[$i]["device"][$row["ean"]] = $row["state"];
					$assoc[$i]["image"] = $row["image"];
					$tmp_img = $row["image"];
				}

				$owner_arr = array();
				
				// Count total devices for each owner (total/available)
				foreach($assoc as $owner){
					$total = 0;
					$free  = 0;
					foreach($owner["device"] as $d){
						if($d == "VAPAA"){
							$free += 1;
						}
						$total += 1;
					}
					$owner["count"] = array("total" => $total, "free" => $free);
					$owner_arr[] = $owner;
				}

				// Generate dropdown list for each owner
				$device_page .= "<h3 style='text-align:center;'><b>$model</b></h3><br/><br/><div class='col-md-4'><img class='img-responsive' src='images/device/$tmp_img' style='height:270px;width:auto;'></div>";
				$device_page .= "<div class='col-md-8'><label>Valitse laitteen omistaja</label><select class='form-control' id='select-owner'>";
				foreach($owner_arr as $owner) {
					$device_page .= "<option value='$owner[id]'>$owner[name] | Saatavilla " . $owner['count']['free'] . " kpl | Yhteensä " . $owner['count']['total'] . " kpl</option>\n";
				}
				$device_page .= "</select></div><br/><br/>";

				echo $device_page;
				
				// Load description for model
				$query = "select description from $table_device where model='$model'";
				if($result = mysqli_query($conn, $query)){
					$row = mysqli_fetch_assoc($result);
					echo "<div class='col-md-8'><br/><label>Kuvaus</label>\n<br/><textarea readonly style='border:none;resize:none;width:100%;height:167px;background-color:#f4f7fc;padding:20px;border-radius:25px;'>$row[description]</textarea><br/><br/></div></div>"; // Closes first row div
				}

				// Amount options
				$amount = '';
				for ($i = 0; $i < $owner['count']['total']; $i++){
					$am = $i+1;
					$amount .= "<option>$am</option>\n";
				}
				
				// Datepicker and reservation
				echo "<div class='row'><div class='col-md-8' style='overflow-y:hidden;'>" . 
				"<form class='form-inline'> <p>Lukumäärä: <select type='text' id='amount' class='form-control'>$amount</select></p>" . 
				"<p>Aloitus: &nbsp;&nbsp;<input type='text' class='datepicker form-control'> Klo: <select type='text' class='klo' id='klo1'>";
			
		
				// Hours options
				$hours = '';
				for ($i = 7; $i < 24; $i++) {	
					if($i < 10) $hours .= "<option>0$i:00</option>\n";
					else $hours .= "<option>$i:00</option>\n";
				}
				
				for($i = 0; $i < 7; $i++){
					if($i < 10) $hours .= "<option>0$i:00</option>\n";
					else $hours .= "<option>$i:00</option>\n";
				}
				
				echo "$hours</select></p><p>Lopetus: <input type='text' class='datepicker form-control'> Klo: <select type='text' class='klo' id='klo2'>";
				echo "$hours\n</select></p></form></div>"; // End of datepicker col

				echo "<div class='col-md-4'><br/><br/><input class='form-control btn-success' type='button' value='Vahvista alustava varaus' onclick='reserve(\"$model\")'></div>"; // Reservation col

				echo "</div><br/>"; // End of row

				
				// Display Calendar
				executeCalendar();
				
			}

			else {
				echo "Laitetta '$model' ei ole olemassa!";
			}
		}
	}

	else {

		executeHeader("Laiterekisteri");
	}
	
	// Echo footer template
	executeFooter();

?>