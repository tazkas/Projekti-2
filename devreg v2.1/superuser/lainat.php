<?php

	/*

		lainat.php
	
		This file displays and handles all loans.

	*/

	require_once "templates.php";

	executeHeader("Laiterekisteri", "lainat", "container");
?>

<p>Lainassa olevat laitteet ja tehdyt varaukset näkyvät tässä.</p>
<p>Varaukset voi myös poistaa tai niitä voi muokata.</p>
			
<?php executeFooter(); ?>