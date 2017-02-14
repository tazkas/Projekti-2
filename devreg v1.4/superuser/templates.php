<?php

	/*
	
		templates.php
		
		The purpose of this file is to store core html chunks
		into php variables, making the code amount in the main files
		shorter.

		h1,h2 and h3 are all parts of the header, and f1 is the footer part.

		USAGE OF FUNCTIONS:

			executeHeader ( **Title of the page** , **Active tab (highlighted button in the nav bar)** , **Container type** )
			 - All the parameters have default values, title = "Laiterekisteri", activetab = "", container = "container"

			executeFooter() 
			 - Doesn't take any parameters

			 THE TEMPLATE FUNCTIONS DON'T RETURN ANYTHING, !! DO NOT ECHO THESE FUNCTIONS  !!
	
	*/

	$h1 = "
<!DOCTYPE html>
<html lang='fi'>


	<head>

		<meta charset='UTF-8'>
		<meta name='viewport' content='width=device-width, initial-scale=1'>

		<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
		<link rel='icon' href='images/favicon.png' type='image/jpg'>
		<link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>

		<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js'></script>
		<script src='http://code.jquery.com/jquery-1.9.1.js'></script>
		<script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
		<script src='https://code.jquery.com/jquery-1.12.4.js'></script>
	  <script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'></script>
	  <script>
	  $(document).ready(function() {
		$( '.datepicker' ).datepicker({
		  changeMonth: true,
		  changeYear: true,
		  firstDay: 1,
		  dateFormat: 'dd.mm.yy',
		  dayNames: [ 'Sunnuntai', 'Maanantai', 'Tiistai',  'Keskiviikko', 'Torstai', 'Perjantai', 'Lauantai' ],
		  dayNamesMin: [ 'Su', 'Ma',  'Ti', 'Ke', 'To', 'Pe', 'La' ],
		  dayNamesShort: [ 'Sun', 'Maa', 'Tii', 'Kes', 'Tor', 'Per', 'Lau'],
		  monthNames: [ 'Tammikuu', 'Helmikuu', 'Maaliskuu', 'Huhtikuu', 'Toukokuu', 'Kesäkuu', 'Heinäkuu', 'Elokuu', 'Syyskuu', 'Lokakuu', 'Marraskuu', 'Joulukuu'],
		  monthNamesShort: [ 'Tammi', 'Helmi', 'Maalis', 'Huhti', 'Touko', 'Kesä', 'Heinä', 'Elo', 'Syys', 'Loka', 'Marras', 'Joulu']
		});
		$( '.klo' ).selectmenu();
	  });
	  </script>";

	$h2 = "
	</head>


	<body>

		<div class='jumbotron text-center' style='background-color:rgb(206,45,123);margin-bottom:0;padding:5px;'> <!-- TOP JUMBOTRON -->
			<div class='span12'>
			<a href='index'><img class='img-responsive center-block img-rounded' src='images/savonia_logo.jpg' style='max-width: 300px;display:inline-block;'></a>
			</div>
		</div>

		<nav class='navbar navbar-default'> <!-- NAVBAR -->
		<div class='container'>
			<div class='navbar-header'>
				<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-ex1-collapse'>
					<span class='sr-only'>Toggle navigation</span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
					<span class='icon-bar'></span>
				</button>
				<span class='navbar-brand'>Navigointi</span>
			</div>

			<div class='collapse navbar-collapse navbar-ex1-collapse'>
				<ul class='nav navbar-nav'>";

	$h3 = "</ul>
			</div>
			</div>
		</nav> <!-- /NAVBAR -->";

	$f1 = "
	      </div> <!-- /MAIN CONTAINER -->

	</body>

</html>";

	
	function executeHeader($title="Laiterekisteri", $active_tab="", $container="container", $script=""){
		global $h1, $h2, $h3;
		$title = "<title>$title</title>";
		switch ($active_tab) {
			case 'index':
				$active_tab = "<li class='active'><a href='index'>Laitehaku</a></li><li><a href='lainat'>Lainat</a></li><li><a href='hallinta'>Hallinta</a></li>";
				break;
			case 'lainat':
				$active_tab = "<li><a href='index'>Laitehaku</a></li><li class='active'><a href='lainat'>Lainat</a></li><li><a href='hallinta'>Hallinta</a></li>";
				break;
			case "hallinta":
				$active_tab = "<li><a href='index'>Laitehaku</a></li><li><a href='lainat'>Lainat</a></li><li class='active'><a href='hallinta'>Hallinta</a></li>";
				break;
			default:
				$active_tab = "<li><a href='index'>Laitehaku</a></li><li><a href='lainat'>Lainat</a></li><li><a href='hallinta'>Hallinta</a></li>";
				break;
		}
		$container = "<div class='$container'> <!-- MAIN CONTAINER -->";
		echo $h1 . $title . $h2 . $active_tab . $h3 . $container;		
	}

	function executeFooter(){
		global $f1;
		echo $f1;
	}


?>