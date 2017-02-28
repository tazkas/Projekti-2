<?php

	/*

		hallinta.php

		This page is only accessible to admins.
		Here admins can add/edit/delete devices and categories,
		(((and manage loans.)))

	*/

	session_start();

	// Mandatory includements       // Contains
	require_once "db.php";			// database variables, including the connection/link; $conn
	require_once "functions.php";	// Reusable functions
	require_once "templates.php";	// HTML templates

	// Display core html
	executeHeader("Hallintasivut", "hallinta");

	// Load categories to a variable which will be echoed later
	// Also add category names to an array, to prevent duplicates
	$category_options = "<option value='null' selected>Valitse kategoria</option>\n";
	$existing_categories = array();
	$query = "select id, name from $table_category";
	if($result = mysqli_query($conn,$query)){
		while($row = mysqli_fetch_assoc($result)){
			$category_options .= "<option value='$row[id]'>$row[name]</option>\n";
			$existing_categories[] = $row['name'];
		}
	}

	// Load existing ean serials to prevent duplicate eans
	$existing_eans = array();
	$query = "select device_ean from $table_device";
	if($result = mysqli_query($conn, $query)){
		while($row = mysqli_fetch_assoc($result))
			$existing_eans[] = $row["device_ean"];
	}
	
	// Load locations
	$locations = "";
	$query = "select id, address from $table_location";
	if($result = mysqli_query($conn, $query)){
		while($row = mysqli_fetch_assoc($result))
			$locations .= "<option value='$row[id]'>$row[address]</option>\n";
	}

	// FORM HANDLERS \\

	// Save category
	if(isset($_POST["save-category"])){
		
		$ok = true;
		$category_name = $_POST["category-name"];
		$category_img  = "category_default_other_2.jpg";
		$category_img_radio  = $_POST["category-img-radio"];

		// Check that the category name isn't taken
		if(!in_array($category_name, $existing_categories)){

			// Upload a custom image instead of using the default image
			if($category_img_radio == "custom"){
				if($_FILES["category-img-input"]["name"] != ""){
					$category_img  = $_FILES["category-img-input"]["name"];
					$ok = upload("category", "category-img-input");
				}
			}

			// Some error occur, so only if the image upload is successful, wil we
			// insert new data to the database.
			if($ok == true){
				$query = "insert into $table_category (image,name) values('$category_img', '$category_name')";
				if($result = mysqli_query($conn, $query))
					echo "<div class='alert alert-success'><strong>Kategorian lisäys onnistui!</strong></div>";
				else
					echo "<div class='alert alert-danger'><strong>Kategorian lisäys epäonnistui...</strong></div>";
			}
		}
		else
			echo "<div class='alert alert-warning'><strong>Kategoria nimellä '$category_name' on jo olemassa...</strong></div>";
		
	}

	// Edit category
	elseif(isset($_POST["category-edit-submit"])){
		
		if($_POST["category-edit-select"] != "null"){

			$ok = true;
			$name = $_POST["category-edit-name"];
			$img = $_POST["category-edit-image-cur"];
			$nimg = $_FILES["category-edit-image-new"]["name"];
			$id   = $_POST["category-edit-id"];

			if($nimg != ""){
				$img = $nimg;
				$ok = upload("category", "category-edit-image-new");
			}
			if($ok == true){
				$query = "update $table_category set name='$name', image='$img' where id='$id'";
				if($result = mysqli_query($conn, $query))
					echo "<div class='alert alert-success'><strong>Kategorian muokkaus onnistui!</strong></div>";
				else
					echo "<div class='alert alert-danger'><strong>Kategorian muokkaus epäonnistui...</strong></div>";
			}
			
		}	
		else
			echo "<div class='alert alert-info'><strong>Valitse kategoria!</strong></div>";
	}

	// Delete category
	elseif(isset($_POST['category-delete-submit'])){
		$catid = $_POST['category-delete-select'];
		$query = "delete from $table_category where id='$catid'";

		if($result = mysqli_query($conn, $query))
			echo "<div class='alert alert-success'><strong>Kategorian poisto onnistui!</strong></div>";
		else
			echo "<div class='alert alert-danger'><strong>Kategorian poisto epäonnistui...</strong></div>";

	}	

	// Save device
	elseif(isset($_POST["device-save"])){

		$ok = true;
		$category = $_POST["device-category"];
		$model = $_POST["device-model"];
		$type  = $_POST["device-type"];
		$description = $_POST["device-description"];
		$count = $_POST["device-count"];
		$eans  = $_POST["device-ean"];
		$device_img = "noimage.png";
		$device_img_radio  = $_POST["device-img-radio"];
		$device_location = $_POST["device-location"];

		// Check if ean serial already in use
		$used_eans = array();
		foreach($eans as $ean){
			if(in_array($ean, $existing_eans)){
				$ok = false;
				$used_eans[] = $ean;
			}
		}

		if($ok == true){

			// Upload a custom image instead of using the default image
			if($device_img_radio == "custom"){
				if($_FILES["device-img-input"]["name"] != ""){
					$device_img  = $_FILES["device-img-input"]["name"];
					$ok = upload("device", "device-img-input");
				}
			}

			// Some error might occur, so only if the image upload is successful, wil we
			// insert new data to the database.
			if($ok == true){
				$query = "insert into $table_device(device_ean, model, device_type, category, description, image, owner_id, state, location) values ";
				foreach($eans as $ean)
					$query .= "('$ean','$model', '$type','$category','$description','$device_img', '1', 'VAPAA', '$device_location'),";
				$query = substr($query, 0, strlen($query)-1);
				if($result = mysqli_query($conn, $query))
					echo "<div class='alert alert-success'><strong>Laitteen lisäys onnistui!</strong></div>";
				else
					echo "<div class='alert alert-danger'><strong>Virhe! Kantaan lisääminen epäonnistui!</strong></div>";
			}
		}
		else{
			echo "<div class='alert alert-warning'><strong>Lisäys epäonnistui...<br/><br/>Seuraavat ean tunnukset ovat jo käytössä:</strong><br/><ul>";
			foreach($used_eans as $u) echo "<li>$u</li>";
			echo "</ul></div>";
		}
	}
	// Save existing device
	elseif(isset($_POST["device-save-old"])){

		$ok = true;
		$model = $_POST["device-selected"];
		$count = $_POST["device-count-input-old"];
		$eans  = $_POST["device-ean"];
		$location = $_POST["device-location-old"];

		// Check if ean serial already in use
		$used_eans = array();
		foreach($eans as $ean){
			if(in_array($ean, $existing_eans)){
				$ok = false;
				$used_eans[] = $ean;
			}
		}

		if($ok == true){

			$query = "select device_type as type, category as cat, description as des, image as img from $table_device where model='$model'";
			if($result = mysqli_query($conn, $query)){
				$row = mysqli_fetch_assoc($result);
				$query = "insert into $table_device(device_ean, model, device_type, category, description, image, owner_id, state, location) values ";
				foreach($eans as $ean)
					$query .= "('$ean','$model', '$row[type]','$row[cat]','$row[des]','$row[img]', '1', 'VAPAA', '$location'),";
				$query = substr($query, 0, strlen($query)-1);
				if($result = mysqli_query($conn,$query)) echo "<div class='alert alert-success'><strong>Laitteen lisäys onnistui!</strong></div>";
				else echo "<div class='alert alert-danger'><strong>Virhe! Kantaan lisääminen epäonnistui!</strong></div>";
			}
			else echo "<div class='alert alert-danger'><strong>Virhe! Vanhojen tietojen hakeminen kannasta epäonnistui...</strong></div>";
		}
		else{
			echo "<div class='alert alert-warning'><strong>Lisäys epäonnistui...<br/><br/>Seuraavat ean tunnukset ovat jo käytössä:</strong><br/><ul>";
			foreach($used_eans as $u) echo "<li>$u</li>";
			echo "</ul></div>";
		}
	}

	// Edit device
	elseif(isset($_POST["edit-device-submit"])){
		
		$old   = $_POST["edit-device-selected"];
		$model = $_POST["edit-device-model"];
		$desc  = $_POST["edit-device-description"];
		$type  = $_POST["edit-device-type"];
		$cate  = $_POST["edit-device-category"];
		$loca  = $_POST["edit-device-location"];
		$affe = $_POST["edit-device-affected"];
		$query = "";
		$ok = true;

		if($loca == "null") $query = "update $table_device set model='$model', description='$desc', device_type='$type', category='$cate' where model='$old' and ";
		else $query = "update $table_device set model='$model', description='$desc', device_type='$type', category='$cate', location='$loca' where model='$old' and ";

		if($affe != "all"){
			if(isset($_POST["edit-device-affected-eans"])){
				$query .= "(";
				$edit_eans = $_POST["edit-device-affected-eans"];
				foreach($edit_eans as $e) $query .= " device_ean='$e' or";
				$query .= " 1=2)";
			}
			else{
				$ok = false;
			}
			
		}
		else $query .= "1=1";


		if($ok == true){
			if($result = mysqli_query($conn, $query)) echo "<div class='alert alert-success'><strong>Laitteen muokkaus onnistui!</strong></div>";
			else {
				echo "<div class='alert alert-danger'><strong>Virhe! Kannan rivien muokkaaminen epäonnistui..." . mysqli_error($conn) . "</strong></div>";
			}
		}

		else echo "<div class='alert alert-warning'><strong>Muokkausta ei suoritettu!<br/>Et valinnut yhtään laitetta!</strong></div>";
		
	}

	// Delete device
	elseif(isset($_POST["device-delete-submit"])){

		if(isset($_POST["device-delete-todelete"])){
			$eans = $_POST["device-delete-todelete"];
			$query = "delete from $table_device where 1=2 ";
			foreach($eans as $e) $query .= "or device_ean='$e' ";
			if($result = mysqli_query($conn,$query)){
				echo "<div class='alert alert-success'><strong>Laitteen poisto onnistui!</strong></div>";
			}
			else
				echo "<div class='alert alert-danger'><strong>Laitteen poisto epäonnistui...</strong></div>";
		}
		else
			echo "<div class='alert alert-warning'><strong>Et valinnut yhtään poistettavaa laitetta!</strong></div>";
	}


?>

<script>
function generateDeviceInputs(countid, eanid, outid) {
	//alert(countid + " " + eanid + " " + outid);
	var count = document.getElementById(countid).value;
	var ean   = document.getElementById(eanid).value;
    if (count.length == 0 || ean.length == 0) { 
        document.getElementById(countid).innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById(outid).innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "AJAX/generateDeviceInputs.php?count=" + count + "&ean=" + ean, true);
        xmlhttp.send();
    }
}

function generateCategoryInfo(cat) {
    if (cat == "null") { 
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
            	//alert(this.responseText);
                document.getElementById('category-info-list').innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "AJAX/generateCategoryInfo.php?cat=" + cat, true);
        xmlhttp.send();
    }
}

function eanguess(text){
	if(text.length != 0){
		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('device-delete-list').innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "AJAX/eanguess.php?text="+text, true);
        xmlhttp.send();
	}
}

function modelguess(text){
	if(text.length != 0){
		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('device-select').innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "AJAX/modelguess.php?text="+text, true);
        xmlhttp.send();
	}
}
function editdevicemodelguess(text){
	if(text.length != 0){
		var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById('edit-device-search-byname-results').innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "AJAX/modelguess.php?text="+text+"&editdevice", true);
        xmlhttp.send();
	}
}
function editdeviceLoadInfo(model){
	var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('edit-device-search-byname-results-selected').innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "AJAX/editdevice.php?model="+model, true);
    xmlhttp.send();
}
</script>

<div class='row'>
	<div class='col-md-8 col-xs-12'>
		<h2>Laitteenhallinta</h2>
		<br/>
		<button type='button' class='btn btn-info' data-toggle='collapse' data-target='#new-device'>
			Lisää uusi laite
		</button>
		<br/><br/>
		<button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#edit-device'>
			Muokkaa laitetta
		</button>
		<br/><br/>
		<button type='button' class='btn btn-danger' data-toggle='collapse' data-target='#delete-device'>
			Poista laite
		</button>
		<br/><br/>
		<div id='new-device' class='collapse'>
			<div class='well well-lg'>
			<br/>
			<div class='form-group'>
				<label>Hae laite</label>
				<input class='form-control' type='text' placeholder='Laitteen mallin nimi...' onkeyup='modelguess(this.value)'>
				<!--<div class='radio'><label><input type='radio' name='device-selected' checked form='new-device-old-form'>Laitetta ei löydy hausta, lisään uuden laitteen.</label></div>-->
				<div id="device-select">

				</div>
			</div>
			<form action='' method='POST' enctype='multipart/form-data' id='device-new'>
				<br/>
				<label>Tai lisää laite, jota ei ole vielä kannassa</label>
				<br/><br/>
				<div class='form-group'>
				    <label for='device-model'>Laitteen nimi/malli:</label>
				 	<input type='text' class='form-control' id='device-model' placeholder='Esim. Dell - Inspiron 3650, Samsung Galaxy S7...' name='device-model' required>
				</div>
				<br/>
				<div class='form-group'>
					<label for='category-select'>Valitse kategoria (Jos kategoriaa ei löydy, lisää uusi alla)</label>
					<select class='form-control' id='category-select' name='device-category'>
						<?php
							echo $category_options;
						?>
					</select>
				</div>
				<br/>
				<div class='form-group'>
				    <label for='device-type'>Laitteen tyyppi:</label>
				 	<input type='text' class='form-control' id='device-type' placeholder='Esim. Kannettava tietokone, näyttö, järjestelmäkamera...' name='device-type'>
				</div>
				<br/>
				<div class='form-group'>
				    <label for='device-description'>Laitteen kuvaus:</label>
				 	<textarea class='form-control' rows='4' placeholder='Vapaamuotoinen kuvaus' style='resize:none;' name='device-description' form='device-new' id='device-description'></textarea>
				</div>
				<br/>
				<div class='form-group'>
				    <div class='radio'>
				    	<label><input type='radio' name='device-img-radio' value='default' checked>Käytä sivuston omaa kuvaa</label>
				    	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				    	<label><input type='radio' name='device-img-radio' value='custom' data-toggle='modal' data-target='#custom-device-image'>Käytä omaa kuvaa</label>
				    </div>
				    <!-- CUSTOM CATEGORY IMAGE WINDOW -->
				 	<div class='modal fade' id='custom-device-image' role='dialog'>
					    <div class='modal-dialog'>
					      <!-- Modal content-->
					      <div class='modal-content'>
					        <div class='modal-header'>
					          <button type='button' class='close' data-dismiss='modal'>&times;</button>
					          <h4 class="modal-title">Valitse kuva</h4>
					        </div>
					        <div class='modal-body'>
					        	<div class='fileinput fileinput-new' data-provides='fileinput'>
    								<span class='btn btn-default btn-file'><input type='file' name='device-img-input'/></span>
    							</div>
					        </div>
					        <div class='modal-footer'>
					          <button type='button' class='btn btn-default' data-dismiss='modal'>Valmis</button>
					        </div>
					      </div> 
					    </div>
					</div>
				</div>
				<br/>
				<div class='form-group'>
				    <label for='device-location'>Laitteen sijainti/kotipaikka:</label>
				 	<select class='form-control' id='device-location' name='device-location'>
						<?php echo $locations; ?>
					</select>
				</div>
				<br/>
				<div class='form-group'>
					<label for='device-ean-input'>Laitteen EAN-tunnuksen alkuosa:</label>
					<input type='text' class='form-control' name='device-ean-input' id='device-ean-input' placeholder='Esim. op-2035-' onkeyup="generateDeviceInputs('device-count-input','device-ean-input','device-ean-list')"  required>
				</div>
				<br/>
				<div class='form-group'>
					<label for='device-count'>Kuinka monta laitetta lisäät?</label>
					<input type='text' class='form-control' name='device-count' id='device-count-input' placeholder='1, 5, 10...' onkeyup="generateDeviceInputs('device-count-input','device-ean-input','device-ean-list')"  required>
				</div>
				<div class='form-group' id='device-ean-list'>
				</div>
				<br/>
				<div class='form-group'>
				 	<input type='submit' class='btn btn-success' id='device-submit' value='Tallenna laite' name='device-save'>
				</div>
			</form>
			<div class='modal fade' id='new-device-old' role='dialog'>
			    <div class='modal-dialog'>
			      <!-- Modal content-->
			      <div class='modal-content'>
			        <div class='modal-header'>
			          <button type='button' class='close' data-dismiss='modal'>&times;</button>
			          <h4 class="modal-title">Lisää jo olemassaoleva laite</h4>
			        </div>
			        <div class='modal-body'>
			        <form action='' method='POST' id='new-device-old-form'>
			        	<div class='form-group'>
						    <label for='device-location'>Laitteen sijainti/kotipaikka:</label>
						 	<select class='form-control' id='device-location' name='device-location-old'>
								<?php echo $locations; ?>
							</select>
						</div>
						<br/>
						<div class='form-group'>
							<label for='device-ean-input'>Laitteen EAN-tunnuksen alkuosa:</label>
							<input type='text' class='form-control' name='device-ean-input-old' id='device-ean-input-old' placeholder='Esim. op-2035-' onkeyup="generateDeviceInputs('device-count-input-old', 'device-ean-input-old', 'device-ean-list-old')" required>
						</div>
						<br/>
						<div class='form-group'>
							<label for='device-count'>Kuinka monta laitetta lisäät?</label>
							<input type='text' class='form-control' name='device-count-input-old' id='device-count-input-old' placeholder='1, 5, 10...' onkeyup="generateDeviceInputs('device-count-input-old', 'device-ean-input-old', 'device-ean-list-old')" required>
						</div>
						<div class='form-group' id='device-ean-list-old'>
						</div>
						<br/>
						<div class='form-group'>
						 	<input type='submit' class='btn btn-success' id='device-submit' value='Tallenna laite' name='device-save-old'>
						</div>
			        </div>
			        </form>
			        <div class='modal-footer'>
			          <button type='button' class='btn btn-default' data-dismiss='modal'>Peruuta</button>
			        </div>
			      </div> 
			    </div>
			</div>
			</div>
		</div>
		<div id='edit-device' class='collapse'>
			<div class='well well-lg'>

				<label>Muokkaa laitetta</label>
				<br/><br/>
				<label>Etsi nimen mukaan</label>
				<input class='form-control' type='text' id='edit-device-search-byname' placeholder='Esim. Samsung Galaxy S7' onkeyup='editdevicemodelguess(this.value)'>
				<br/>
				<form id='edit-device-form' method='POST' action=''>
					<div id='edit-device-search-byname-results'></div>
					<br/>
					<div id='edit-device-search-byname-results-selected'></div>	
				</form>

			</div>
		</div>
		<div id='delete-device' class='collapse'>
			<div class='well well-lg'>
			<br/>
				<form action='' method='POST' enctype='multipart/form-data' id='device-delete'>
					<label>Etsi poistettavan tuotteen ean/nimi/malli</label>
					<input class='form-control' type='text' name='device-delete-search' onkeyup='eanguess(this.value)'>
					<br/>
					<div class='form-group' id='device-delete-list'>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<br/>

<div class='row'>
	<div class='col-md-8 col-xs-12'>
		<h2>Kategorianhallinta</h2>
		<br/>
		<button type='button' class='btn btn-info' data-toggle='collapse' data-target='#new-category'>
			Lisää uusi kategoria
		</button>
		<br/><br/>
		<button type='button' class='btn btn-warning' data-toggle='collapse' data-target='#edit-category'>
			Muokkaa kategoriaa
		</button>
		<br/><br/>
		<button type='button' class='btn btn-danger' data-toggle='collapse' data-target='#delete-category'>
			Poista kategoria
		</button>
		<br/><br/>
		<div id='new-category' class='collapse'>
			<div class='well well-lg'>
			<br/>
			<form action='' method='POST' enctype='multipart/form-data' id='category-new'>
				<div class='form-group'>
				    <label for='category-name'>Kategorian nimi:</label>
				 	<input type='text' class='form-control' id='category-name' name='category-name' placeholder='Kategorian nimi' required>
				</div>
				<br/>
				<div class='form-group'>
				    <div class='radio'>
				    	<label><input type='radio' name='category-img-radio' value='default' checked>Käytä sivuston omaa kuvaa</label>
				    	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				    	<label><input type='radio' name='category-img-radio' value='custom' data-toggle='modal' data-target='#custom-category-image'>Käytä omaa kuvaa</label>
				    </div>
				    <!-- CUSTOM CATEGORY IMAGE WINDOW -->
				 	<div class='modal fade' id='custom-category-image' role='dialog'>
					    <div class='modal-dialog'>
					      <!-- Modal content-->
					      <div class='modal-content'>
					        <div class='modal-header'>
					          <button type='button' class='close' data-dismiss='modal'>&times;</button>
					          <h4 class="modal-title">Valitse kuva</h4>
					        </div>
					        <div class='modal-body'>
					        	<div class='fileinput fileinput-new' data-provides='fileinput'>
    								<span class='btn btn-default btn-file'><input type='file' name='category-img-input'/></span>
    							</div>
					        </div>
					        <div class='modal-footer'>
					          <button type='button' class='btn btn-default' data-dismiss='modal'>Valmis</button>
					        </div>
					      </div> 
					    </div>
					</div>
				</div>
				<br/>
				<div class='form-group'>
				 	<input type='submit' class='btn btn-success' id='category-submit' value='Tallenna kategoria' name='save-category'>
				</div>
			</form>
			</div>
		</div>
		<div id='edit-category' class='collapse'>
			<div class='well well-lg'>
				<form action='' method='POST' enctype="multipart/form-data"> 
					<div class='form-group'>
						<label for='category-select'>Valitse muokattava kategoria</label>
						<select class='form-control' id='category-edit-select' name='category-edit-select' onchange='generateCategoryInfo(this.value)'>
							<?php
								echo $category_options;
							?>
						</select>
						<br/>
						<div class='form-group' id='category-info-list'>
						</div>
						<br/>
					</div>
					<div class='form-group'>
						<input class='btn btn-success' type='submit' name='category-edit-submit' value='Tallenna muutokset'>
					</div>
				</form>
			</div>
		</div>
		<div id='delete-category' class='collapse'>
			<div class='well well-lg'>
			<br/>
			<form action='' method='POST'>
				<div class='form-group'>
					<label for='category-select'>Valitse poistettava kategoria</label>
					<select class='form-control' id='category-delete-select' name='category-delete-select'>
						<?php
							echo $category_options;
						?>
					</select>
					<br/>
					<div class='form-group'>
					<input type='submit' name='category-delete-submit' value='Poista valittu kategoria' class='btn btn-success'>
					</div>
				</div>
			</form>
			</div>
		</div>
	</div>
</div>

<br/>

<?php

	// Display footer html
	executeFooter();

?>


