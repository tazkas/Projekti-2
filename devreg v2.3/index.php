<?php

	$total = glob("*");
	$links = "";

	foreach($total as $t){
		if(is_dir($t))
			$links .= "<a href='$t'><h2>$t</h2></a>\n\n\t\t\t<br/>\n\n\t\t\t";
	}

?>
<!DOCTYPE html>

<html>

	<head>

		<title>bloopy's site</title>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="shortcut icon" type="image/png" href="http://www.tunturisusi.com/nostalgia/armikuusela.jpg">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	</head>

	<body style='font-family: courier new;'>

		<div class='page-header text-center'>

				<h1>devreg v2.1</h1>
				
		</div>

		<div class='container text-center'>

			<h3><b>choose privilege</b></h3>

			<br/>

			<?php echo $links; ?>

		</div>

	</body>

</html>