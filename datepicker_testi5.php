<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>jQuery UI Datepicker - Display month &amp; year menus</title>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  

  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $(document).ready(function() {
    $( ".datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true,
	  firstDay: 1,
	  dateFormat: "dd.mm.yy",
	  dayNames: [ "Sunnuntai", "Maanantai", "Tiistai",  "Keskiviikko", "Torstai", "Perjantai", "Lauantai" ],
	  dayNamesMin: [ "Su", "Ma",  "Ti", "Ke", "To", "Pe", "La" ],
	  dayNamesShort: [ "Sun", "Maa", "Tii", "Kes", "Tor", "Per", "Lau"],
	  monthNames: [ "Tammikuu", "Helmikuu", "Maaliskuu", "Huhtikuu", "Toukokuu", "Kesäkuu", "Heinäkuu", "Elokuu", "Syyskuu", "Lokakuu", "Marraskuu", "Joulukuu"],
	  monthNamesShort: [ "Tammi", "Helmi", "Maalis", "Huhti", "Touko", "Kesä", "Heinä", "Elo", "Syys", "Loka", "Marras", "Joulu"]
    });
  });
  </script>
  
  <script>
    $(document).ready(function() {
    $( "#klo1" ).selectmenu().selectmenu( "menuWidget" ).addClass( "overflow" );
	$( "#klo2" ).selectmenu().selectmenu( "menuWidget" ).addClass( "overflow" );
	});
  </script>
</head>
<body>
	<form class="form-inline"> 
		<p>Aloitus: <input type="text" class="datepicker form-control" id="dp1"> Klo:
		<select type="text" class="klo" id="klo1">
		<?php
			for ($i = 7; $i < 24; $i++) {			
				echo "<option>";
				if ($i < 10) {
					echo "0";
				}
				echo $i . ":00</option>";
			}
			for ($i = 0; $i < 7; $i++) {			
				echo "<option>";
				if ($i < 10) {
					echo "0";
				}
				echo $i . ":00</option>";
			}
		?>
		</select></p>
		<p>Lopetus: <input type="text" class="datepicker form-control" id="dp2"> Klo:
		<select type="text" class="klo" id="klo2">
		
		<?php
			for ($i = 7; $i < 24; $i++) {			
				echo "<option>";
				if ($i < 10) {
					echo "0";
				}
				echo $i . ":00</option>";
			}
			for ($i = 0; $i < 7; $i++) {			
				echo "<option>";
				if ($i < 10) {
					echo "0";
				}
				echo $i . ":00</option>";
			}
		?>
		</select></p>
	</form>
</body>
</html>