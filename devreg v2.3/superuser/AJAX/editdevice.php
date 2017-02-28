<?php

	/*
		editdevice.php
	*/

	require_once "db.php";
	require_once "templates.php";

	if(isset($_REQUEST["model"])){

		$inputs = "";
		$model = $_REQUEST["model"];
		$query = "select * from $table_device where model = '$model'";
		if($result = mysqli_query($conn, $query)){
			$row = mysqli_fetch_assoc($result);

			// Model/name, type and description
			$inputs .= "<label>Laitteen nimi</label><input class='form-control' type='text' value='$row[model]' placeholder='Esim. iPhone 7' name='edit-device-model'><br/>\n";
			$inputs .= "<label>Laitteen tyyppi</label><input class='form-control' type='text' value='$row[device_type]' placeholder='Esim. älypuhelin' name='edit-device-type'><br/>\n";
			$inputs .= "<label>Laitteen kuvaus</label><textarea rows='5' class='form-control' style='resize:none;' placeholder='Esim. laitteen speksit' name='edit-device-description'>$row[description]</textarea><br/>\n";

			// Categories
			$category_options = "";
			$query = "select id, name from $table_category";
			if($result = mysqli_query($conn,$query)){
				while($row2 = mysqli_fetch_assoc($result)){
					if($row2["id"] == $row["category"]) $category_options .= "<option value='$row2[id]' selected>$row2[name]</option>\n";
					else $category_options .= "<option value='$row2[id]'>$row2[name]</option>\n";
				}
			}
			$inputs .= "<label>Laitteen kategoria</label><select class='form-control' name='edit-device-category'>$category_options</select><br/>\n";

			// Locations
			$locations = "";
			$query = "select id, address from location";
			if($result = mysqli_query($conn, $query)){
				while($row3 = mysqli_fetch_assoc($result)){
					//if($row3["id"] == $row["location"]) $locations .= "<option value='$row3[id]' selected>$row3[address]</option>\n";
					$locations .= "<option value='$row3[id]'>$row3[address]</option>\n";
				}
			}
			$inputs .= "<label>Laitteen sijainti</label><select class='form-control' name='edit-device-location'>\n<option value='null' selected>Älä muuta sijaintia</option>\n$locations</select><br/><br/>\n";

			// Affected devices
			$inputs .= "<label>Muokkausten kohteet</label><div class='radio'>
				    	<label><input type='radio' name='edit-device-affected' value='all' required>Kaikki</label>
				    	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				    	<label><input type='radio' name='edit-device-affected' value='custom' data-toggle='modal' data-target='#edit-device-select-affected'>Valitut laitteet</label>
				    </div><br/>";
			$eans = "<div class='form-group'>";
			$query = "select device_ean as ean from $table_device where model='$model' order by ean";
			if($result = mysqli_query($conn, $query)){
				$i = 4;
				while($row = mysqli_fetch_assoc($result)){
					if($i % 4 == 0){
						if($i != 4) $eans .= "</div>";
						$eans .= "<div class='row'>";
					}
					$eans .= "<div class='col-md-3'><label class='checkbox-inline'><input type='checkbox' name='edit-device-affected-eans[]' value='$row[ean]'>$row[ean]</label></div>\n";
					$i++;
				}
				$eans .= "</div>";
			}
			$eans .= "</div>";
			$inputs .= generateModal("edit-device-select-affected","Valitse laitteet",$eans);

			// Submit button
			$inputs .= "<input class='btn btn-success' type='submit' value='Tallenna muutokset' name='edit-device-submit'><br/>\n";

			echo $inputs;
		}


	}

?>