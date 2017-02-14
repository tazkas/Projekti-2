<?php

	/*

		AJAX/generateDeviceInputs.php

		This file creates ean <input> tags when the user is adding/creating more devices.

		Currently support one (1) set of brackets. 


	*/

	if(isset($_REQUEST["count"]) && isset($_REQUEST["ean"])){

		// Assign all needed variables
		$inputs = "<div class='form-group'><label>Laitteiden yksilöidyt EAN-tunnukset:</label>";
		$count = intval($_REQUEST["count"]);
		$ean1 = $_REQUEST["ean"];
		$ean2 = '';
		$num = '';

		// Bracket logic:
		// If round brackets are found, ordered numbers will
		// be generated automatically.
		// eg. op-2035-1, op-2035-2, op-2035-3...op-2035-10
		if (strpos($ean1, '(') !== false && strpos($ean1, ')') !== false){
			// Get the first value for $num
			for($i=strpos($ean1, '(')+1; $i < strpos($ean1, ')'); $i++)
				$num .= $ean1[$i];
			if($num != ''){
				$num_idx = strpos($ean1, '(');
				$ean1 = str_replace("($num)", '', $ean1);
				$len = strlen($ean1);
				$ean2 = substr($ean1, $num_idx, $len-$num_idx);
				$ean1 = substr($ean1,0,$num_idx);
			}
		}

		// Generate inputs
		for($i=1; $i<=$count;$i++){
			$inputs .= "<input type='text' class='form-control' id='device-ean' name='device-ean[]' placeholder='Laitteen ean tunnus' value='$ean1$num$ean2'>";
			if($num != '') $num = strval(intval($num)+1); // increment the identifying part
		}

		$inputs .= "</div>";
		echo $inputs;
	}

?>