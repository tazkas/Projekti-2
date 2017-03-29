<?php

	/*

		index.php

		This page displays categories and device models.
		Clicking a category will redirect the user to /index?category=**selected category**, where
		the user will be displayed all devices under this category.

		TODO:
			- save queried data to session to prevent duplicate queries

	*/

	session_start();

	// Include all external files
	require_once "db.php";
	require_once "functions.php";
	require_once "templates.php";

	// Display header
	executeHeader("Laiterekisteri", "index", "container");

	// Search bar
	executeSearchbar();
	
	// Page main content variable
	$page_main = "<div class='row'>";

	// User searched for something
	if(isset($_GET["q"])){	
		
		$q = $_GET["q"];
		$query = "select * from $table_device where model like '%$q%' or device_type like '%$q%'";
		if($result = mysqli_query($conn,$query)){
			
				if(mysqli_num_rows($result) != 0){
					// We don't want to display duplicates of the same model.
					// So, we store the models in an array, and check if a new model
					// already exists in the array (models).
					$devices = array();
					$models = array();
					while($row = mysqli_fetch_assoc($result)){
						
						// Check that the model is not a duplicate
						if(!in_array($row["model"], $models)){
							$models[] = $row["model"];
							$devices[] = array("model" => $row["model"], "image" => $row["image"], "tags" => $row["device_type"]);
						}
					}

					// Go through each device(distinct model) and create a div for them.
					// Creates a grid (same as for categories)
					foreach($devices as $d){

						$page_main .= "<div class='col-md-4 col-xs-6'><div class='thumbnail'>" .
										"<a href='laite.php?model=$d[model]'>" . 
										"<img src='images/device/$d[image]' class='rounded mx-auto d-block' style='-ms-interpolation-mode: bicubic;height:150px;width:auto;'>" . 
										"<div class='caption text-center'>" . 
										"<p style=''>$d[model]</p>" . 
										"</div></a></div></div>";
					}
				}
				else $page_main .= "Ei hakutuloksia...";
				
				$page_main .= "</div>";
				echo $page_main;
		}
	}
	
	// Has a category been picked?
	// If yes, display devices of the specified category
	elseif( isset($_GET["category"]) ){

		// Get the category id and execute the query
		$catid = $_GET["category"];
		$query = "select * from $table_device where category = $catid";

		if($result = mysqli_query($conn, $query)){
			// This category has devices!
			if(mysqli_num_rows($result) > 0) {

				// We don't want to display duplicates of the same model.
				// So, we store the models in an array, and check if a new model
				// already exists in the array (models).
				$devices = array();
				$models = array();
				while($row = mysqli_fetch_assoc($result)){
					
					// Check that the model is not a duplicate
					if(!in_array($row["model"], $models)){
						$models[] = $row["model"];
						$devices[] = array("model" => $row["model"], "image" => $row["image"]);
					}
				}

				// Go through each device(distinct model) and create a div for them.
				// Creates a grid (same as for categories)
				foreach($devices as $d){

					$page_main .= "<div class='col-md-4 col-xs-6'><div class='thumbnail'>" .
									"<a href='laite?model=$d[model]'>" . 
									"<img src='images/device/$d[image]' class='rounded mx-auto d-block' style='-ms-interpolation-mode: bicubic;height:100%;max-height:150px;width:auto;'>" . 
									"<div class='caption text-center'>" . 
									"<p style=''>$d[model]</p>" . 
									"</div></a></div></div>";
				}
			}

			// This category has no devices :(
			else {

				$page_main .= "Tässä kategoriassa ei ole laitteita...";
			}
		}

		// Close the 'row' div section
		$page_main .= "</div>";

		// Echo the list of devices!
		echo $page_main;
	}

	// Else, display categories
	else {

		// Execute query for existing categories
		$query = "select * from category";
		if($result = mysqli_query($conn, $query)){
			if(mysqli_num_rows($result) > 0) {

				// Each row represents a category, 
				// so here we create the html for each category
				while($row = mysqli_fetch_assoc($result)) {

					// Create grid for the categories
					$page_main .= "<div class='col-md-4 col-xs-6'><div class='thumbnail'>" .
									"<a href='?category=$row[id]'>" . 
									"<img src='images/category/$row[image]' class='rounded mx-auto d-block' style='-ms-interpolation-mode: bicubic;padding-top:25px;height:100%;max-height:150px;width:auto;'>" . // FIX THE STYLE
									"<div class='caption text-center'>" . 
									"<p>$row[category_name]</p>" . 
									"</div></a></div></div>";
				}

				// Close the 'row' div section
				$page_main .= "</div>";

				// Echo the categories!
				echo $page_main;
			}
		}
	}
	
	// Echo mandatory footer
	executeFooter(); 

?>