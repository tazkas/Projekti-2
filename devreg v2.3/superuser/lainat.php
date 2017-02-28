<?php

	/*

		lainat.php
	
		This file displays and handles all loans.

	*/

	require_once "templates.php";
	require_once "db.php";


	executeHeader("Laiterekisteri", "lainat", "container");
?>
					<div class='container'>
						<h2>Lainat</h2>
						<table id="T1" class='table table-hover'>
							<thead>
								<tr>
									<th>Käyttäjä</th>
									<th>Lainauksen päivämäärä</th>
									<th>Lainauksen päättymispäivä</th>
									<th>Laite</th>
									<th>Kommentit</th>
								</tr>
							</thead>
							<tbody>

								<?php
									
									//SQL query for all loans & reservations
									$query = "SELECT loan.type, loan.username,loan.reservation_date ,loan.loan_date, loan.end_date, loan.info, loan.device_model from loan";
									
									$result = mysqli_query($conn, $query);

									$reservations = array();

									//error message incase error occurs during SQL query
									if (!$result)
										echo "Kysely epaonnistui " . mysqli_error($conn);
									
									else
									{	//read database data and echo reservation infromation on the website
										while ($row = mysqli_fetch_assoc($result))
										{			
											// Store reservations to be echoed later
											if ($row["type"] == "reservation"){
												$reservations[] = "<tr class='active'><td>$row[username]</td><td>$row[reservation_date]</td><td>$row[loan_date]</td><td>$row[end_date]</td><td>$row[device_model]</td><td>$row[info]</td></tr>";
											}

											// Echo loans directly
											else{
												echo "<tr><td>$row[username]</td><td>$row[loan_date]</td><td>$row[end_date]</td><td>$row[device_model]</td><td>$row[info]</td></tr>";
											}

										}
									}	
									
								?>

							</tbody>
						</table>
					</div>
					<div class='container'>
						<h2>Varaukset</h2>
						<table id='T2' class='table table-hover'>
							<thead>
								<tr>
									<th>Käyttäjä</th>
									<th>Varauksen päivämäärä</th>
									<th>Lainauksen aloituspäivä</th>
									<th>lainauksen päättymispäivä</th>
									<th>Laite</th>
									<th>Kommentit</th>
								</tr>
							</thead>
							<tbody>

								<?php 

									foreach($reservations as $r) echo $r;
									
								?>

							</tbody>
						</table>
					</div>

<?php executeFooter(); ?>