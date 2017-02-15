<?php

	/*
		db.php
	*/

	define("shost", "localhost");
	define("suser", "root");
	define("spass", "");
	define("sdb", "devreg");
	
	// Table / View variable names
	$table_device = "device";
	$table_owner  = "owner";
	$table_category = "categories";
	$table_loan = "";
	$table_history = "";
	$table_reservation = "";
	$table_location = "location";
	
	$conn = mysqli_connect(shost, suser, spass, sdb);
	
	if(!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
?>