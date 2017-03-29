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

			 THE TEMPLATE FUNCTIONS DON'T RETURN ANYTHING, !! DO NOT ECHO THESE FUNCTIONS  !! (as of now)
	
	*/
	
	require_once "db.php";
	
	$models = "";
	$query = "select distinct model from $table_device";
	if($result = mysqli_query($conn,$query)){
		while($row = mysqli_fetch_assoc($result)) $models .= "'$row[model]',";
		$models = substr_replace($models, '', strlen($models)-1, 1);
	}

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

	  	<!-- FullCalendar -->
	  	<link rel='stylesheet' href='https://fullcalendar.io/js/fullcalendar-3.2.0/fullcalendar.css' />
		<script src='//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js'></script>
		<script src='//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
	  	<script src='https://fullcalendar.io/js/fullcalendar-3.2.0/fullcalendar.min.js'></script>
	  	<script 
		  src='http://code.jquery.com/ui/1.12.0/jquery-ui.min.js'
		  integrity='sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E='
		  crossorigin='anonymous'>
		</script>

		<script>
		
			/* -- Loan Confirm & Decline Scripts -- */
			
			function handleLoan(loanGroup, condit, tRow){
				$.post('AJAX/loan.php',
					{
						lg: loanGroup,
						condition: condit
					},
					function(data){
						
						if(data == 'OK'){
							
							//Taulukoiden päivitys
							var curRow = tRow.parentNode.parentNode;
							if(condit == \"confirm\"){
								var resCells = curRow.cells;
								var loanRow = document.getElementById(\"T1\").insertRow(1);
								var j = 0;
								for(var i = 0; i < resCells.length - 3; i++){
									loanRow.insertCell(i).innerHTML = resCells[j].innerHTML;
									j++;
									if(j == 1){
										j++;
									}
								}
							}
							
							
							
							var resTable = document.getElementById(\"T2\");
							//resTable.deleteRow(curRow.rowIndex);
							document.getElementById(\"T2\").deleteRow(curRow.rowIndex);
							resTable = document.getElementById(\"T2\");
							
							//Tyhjän Varaukset-taulukon poisto
							if(resTable.rows.length <= 1){
								var curParent = resTable.parentNode;
								curParent.removeChild(resTable);

								var resWell = document.createElement(\"DIV\");
								resWell.className = \"well\";
								//curParent.appendChild(resWell);
								document.getElementById(\"mainContainer\").appendChild(resWell);
								resWell.innerHTML = \"Ei alustavia varauksia\";
							}
						}
					}
				);
			}
		
		</script>
		
		<script>
			function returnAlert(id, tRow) {
				if (confirm(\"Oletko varma?\") == true){
					
					$.post('AJAX/return.php',
					{
						id: id,
					},
					function (data){
						if (data == 'OK'){
							//Taulukoiden päivitys
							var curRow = tRow.parentNode.parentNode;
							
							document.getElementById(\"T1\").deleteRow(curRow.rowIndex);
							var resTable = document.getElementById(\"T1\");
						}
					}
					);
				}
			}
		</script>
		
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
		
	  });
	  </script>

	  <!-- FullCalendar Script -->
	  <script>

	  		// Get current model
	  		var model = decodeURIComponent(window.location.href.split('=')[1]);
	  		if(model.includes('#bottom')) model = model.split('#bottom')[0];

	  		function reserve(modelName){
				var newEvent =  $('#calendar').fullCalendar('clientEvents', 'newEvent')[0];
				var endSetVal = true;

				// TIMES ARE IN MILLISECONDS
				var start_time = newEvent.start;
				var end_time   = newEvent.end;

				if(end_time == null) {
					end_time = (start_time + 90000000-3600000) / 1000;
					endSetVal = false;
				}
				
				var oID = $('#select-owner').val();
				var amnt = $('#amount').val();

				$.post('AJAX/reserve.php',
					{
						reserve: true,
						start: start_time.toString(),
						end: end_time.toString(),
						endSet: endSetVal,
						model: modelName,
						oid: oID,
						amount: amnt
					},
					function(data){
						alert(data);
					}
				);
			} 

			function today(){

				var dt = new Date();
				var y = dt.getFullYear().toString();
				var m = (dt.getMonth() + 1).toString();
				var d = dt.getDate().toString();
				  
				if(m.length == 1) m = '0' + m;
				if(d.length == 1) d = '0' + d;
				  
				return(y + '-' + m + '-' + d);
			}

			function fetchEvents(){
				$.post('AJAX/fetchEvents.php',
				{
					fetchEvents: true,
					modelName: model
				}, function(data) {
					data = data.split(';');
					for(var i=0;i<data.length;i++){
						var event = JSON.parse(data[i]);
						$('#calendar').fullCalendar('renderEvent', event, true);
					}
				});
			}

			fetchEvents();
			
			$(document).ready(function(){

				// EVENTS ( DRAG N DROP )
				$('#external-events .fc-event').each(function() {

					// store data so the calendar knows to render an event upon drop
					$(this).data('event', {
						title: $.trim($(this).text()), // use the element's text as the event title
						stick: true, // maintain when user navigates (see docs on the renderEvent method)
						id: 'newEvent'
					});

					// make the event draggable using jQuery UI
					$(this).draggable({
						zIndex: 999,
						revert: true,      // will cause the event to go back to its
						revertDuration: 0  //  original position after the drag
					});

				});

				$('#calendar').fullCalendar({
					height: 800,
					locale: 'fi',
					header: 
					{
						left: 'prev,next today',
						center: 'title',
						right: 'month,agendaWeek,agendaDay'
					},
					defaultDate: today(),
					defaultView: 'agendaWeek',
					editable: true,
					droppable: true,
					events: 
					[
						/*
						{
							title: 'ktlahsa',
							start: '2017-02-23 10:00',
							end: '2017-02-24 10:00'
						}
						*/
					],
					buttonText: 
					{
						today: 'nykyhetki',
						month: 'kuukausi',
						week: 'viikko',
						day: 'päivä',
						list: 'lista'
					},
					monthNames:
					[
						'Tammikuu',
						'Helmikuu',
						'Maaliskuu',
						'Huhtikuu',
						'Toukokuu',
						'Kesäkuu',
						'Heinäkuu',
						'Elokuu',
						'Syyskuu',
						'Lokakuu',
						'Marraskuu',
						'Joulukuu'
					],
					monthNamesShort:
					[
						'Tammi',
						'Helmi',
						'Maalis',
						'Huhti',
						'Touko',
						'Kesä',
						'Heinä',
						'Elo',
						'Syys',
						'Loka',
						'Marras',
						'Joulu'
					],
					dayNames:
					[
						'Sunnuntai',
						'Maanantai',
						'Tiistai',
						'Keskiviikko',
						'Torstai',
						'Perjantai',
						'Lauantai'
					],
					dayNamesShort:
					[
						'Su',
						'Ma',
						'Ti',
						'Ke',
						'To',
						'Pe',
						'La'
					],
					allDayText: 'koko päivä',
					displayEventEnd: true,
					timeFormat: 'HH:mm',
					firstDay: 1,
					columnFormat: 'ddd D.M.',
					slotLabelFormat: 'HH:mm',
					titleFormat: 'D. MMM YYYY',

					// When a new event is dropped
					eventReceive: function(event){
					    event.title = 'Alustava varaus';
					    event.color = '#00aa38';
					    $('#calendar').fullCalendar('updateEvent',event);
					}
								
				});

				// Make new event width same as box width
				$('.fc-event').css('width', $('.fc-day').css('width'));

			});
		
	  </script>
	  
	  <script>
		  $( function() {
		    var availableTags = [$models];
		    $( '#search-device-searchbar' ).autocomplete({
		      source: availableTags
		    });
		  } );
		  </script>" . "<script>
		    $(document).ready(function() {
		    $( '#klo1' ).selectmenu().selectmenu( 'menuWidget' ).addClass( 'overflow' );
			$( '#klo2' ).selectmenu().selectmenu( 'menuWidget' ).addClass( 'overflow' );
			});
  	  </script>";

	$h2 = "
	</head>


	<body>

		<div class='jumbotron text-center' style='background-color:rgb(206,45,123);margin-bottom:0;padding:5px;'> <!-- TOP JUMBOTRON -->
			<div class='span12'>
			<a href='index.php'><img class='img-responsive center-block img-rounded' src='images/savonia_logo.jpg' style='max-width: 300px;display:inline-block;'></a>
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

$calendar = "
				<div class='row'>
					<div class='col-md-12'>
						<div id='external-events' class='col-md-2 col-md-offset-5 text-center'>
						    <a href='#bottom'><h4>Kalenterivaraus</h4></a>
						    <div class='fc-event' style='cursor: pointer; margin: 0 auto;'>
						    	<span>Vedä minut!</span>
						    </div>
						    <br/>
						</div>
						<div id='calendar' style='display: inline-block;'></div>
					</div>
				</div><div id='bottom'></div><br/> <br/>";

	
	function executeHeader($title="Laiterekisteri", $active_tab="", $container="container", $script=""){
		global $h1, $h2, $h3;
		$title = "<title>$title</title>";
		switch ($active_tab) {
			case 'index':
				$active_tab = "<li class='active'><a href='index.php'>Laitehaku</a></li><li><a href='lainat.php'>Lainat</a></li><li><a href='hallinta.php'>Hallinta</a></li>";
				break;
			case 'lainat':
				$active_tab = "<li><a href='index.php'>Laitehaku</a></li><li class='active'><a href='lainat.php'>Lainat</a></li><li><a href='hallinta.php'>Hallinta</a></li>";
				break;
			case "hallinta":
				$active_tab = "<li><a href='index.php'>Laitehaku</a></li><li><a href='lainat.php'>Lainat</a></li><li class='active'><a href='hallinta.php'>Hallinta</a></li>";
				break;
			default:
				$active_tab = "<li><a href='index.php'>Laitehaku</a></li><li><a href='lainat.php'>Lainat</a></li><li><a href='hallinta.php'>Hallinta</a></li>";
				break;
		}
		$container = "<div class='$container' id='mainContainer'> <!-- MAIN CONTAINER -->";
		echo $h1 . $title . $h2 . $active_tab . $h3 . $container;		
	}

	function executeFooter(){
		global $f1;
		echo $f1;
	}
	
	function executeSearchbar(){
		echo "<form action='' method='GET'>
    <div class='input-group'>
      <input type='text' class='form-control' placeholder='Etsi laitteen nimellä' name='q' id='search-device-searchbar' required>
      <div class='input-group-btn'>
        <button class='btn btn-default' type='submit'><i class='glyphicon glyphicon-search'></i></button>
      </div>
    </div>
  </form><br/><br/>";
	}

	function executeCalendar($script = ""){
		global $calendar;
		echo $script . $calendar;
	}

?>