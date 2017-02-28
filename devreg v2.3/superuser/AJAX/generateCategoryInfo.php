<?php

	/*

		generateCategoryInfo.php?cat=category_id

	*/

	require_once "db.php";

	if(isset($_REQUEST["cat"])){
		$id = $_REQUEST["cat"];
		$query = "select name, image from $table_category where id='$id'";
		if($result = mysqli_query($conn, $query)){
			$row = mysqli_fetch_assoc($result);
			$nme = $row["name"];
			$img = $row["image"];

			echo "<label>Kategorian nimi:</label><input type='text' class='form-control' value='$nme' placeholder='Kategorian nimi' name='category-edit-name'><br/>";
			echo "<label>Kategorian nykyinen kuva:</label><input type='text' class='form-control' value='$img' placeholder='Kategorian kuva' name='category-edit-image-cur' readonly><br/>";
			echo "<input type='hidden' value='$id' name='category-edit-id'>";

			echo "<div class='thumbnail'><img src='images/category/$img' style=''></img></div>";
			
			echo "<input type='button' class='btn btn-info' data-toggle='modal' data-target='#custom-category-edit-image' value='Valitse uusi kuva'>";

			echo "<div class='modal fade' id='custom-category-edit-image' role='dialog'>
					    <div class='modal-dialog'>
					      <!-- Modal content-->
					      <div class='modal-content'>
					        <div class='modal-header'>
					          <button type='button' class='close' data-dismiss='modal'>&times;</button>
					          <h4 class='modal-title'>Valitse uusi kuva</h4>
					        </div>
					        <div class='modal-body'>
					        	<div class='fileinput fileinput-new' data-provides='fileinput'>
    								<span class='btn btn-default btn-file'><input type='file' name='category-edit-image-new' value='test-value'/></span>
    							</div>
					        </div>
					        <div class='modal-footer'>
					          <button type='button' class='btn btn-default' data-dismiss='modal'>Valmis</button>
					        </div>
					      </div> 
					    </div>
					</div>";
			
		}
	}

?>

