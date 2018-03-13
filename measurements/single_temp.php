<!DOCTYPE html>
<?php 
	/*
	ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	*/

    include '../../settings.cfg';  // Settings

	// 12 hours
	$ix = new DateInterval('PT12H');

	// Get label
	$label = $_GET["label"];
	trim($label);
    if ( 0 == strlen($label) ) {
		$label = "Measurement";
	}
	
	// Get sensor GUID
    $guid = $_GET["guid"];
    trim($guid);
    if ( 0 == strlen($guid) ) {
        $guid = 'FF:FF:FF:FF:FF:FF:FF:FF:61:00:08:01:92:AF:A8:10';    
	}
	
?>
<html>
	<head>
		<meta http-equiv="refresh" content="1000">
		<meta charset="utf-8">
		<title>VSCP Temperature</title>
		<style>
			.chart-container {
				width: 90%;
				height: auto;
			}
		</style>

		<!-- Bootstrap core CSS -->
		<link href="js/bootstrap-4.0.0/dist/css/bootstrap.min.css" rel="stylesheet">

		<!-- Custom styles for this template -->
		<link href="measurements.css" rel="stylesheet">

		<script src="js/gauge.min.js"></script>
	</head>
	<body>

		<div class="container">

			<div class="row">	
				
				<div class="mx-auto" >

					<!-- Injecting linear gauge -->
					<canvas id="canvas_gauge"></canvas>
				</div>
			</div>	

			<div class="row">
				<div class="text-muted mx-auto" id="idInfoText"></div>
			</div>

			<div class="row">
              <div class="dropdown mx-auto">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="secoDropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <span data-feather="activity"></span>
                  Select source                  
                </button>
                <div class="dropdown-menu" aria-labelledby="top-seco-menu">
                  <ul class="nav flex-column " id="top-seco-menu">
                  </ul>  
                </div>   
              </div>
			</div>

		</div>
		
		

		<!-- javascript -->
		<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="js/moment.min.js"></script>

		<script src="js/bootstrap-4.0.0/assets/js/vendor/popper.min.js"></script>
    	<script src="js/bootstrap-4.0.0/dist/js/bootstrap.min.js"></script>

		<!-- Icons -->		
    	<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
    	<script>
      		feather.replace()
		</script>

		<!-- Page load handler -->
		<script type="text/javascript">

			var guid = "<?php echo $guid;?>";

			var gauge1 = new LinearGauge({ 
					renderTo: 'canvas_gauge', 
        			width: 160,
        		    height: 600,
					glow: true,
					valueBox: true,
        			borderRadius: 20,
        			borders: 0,
        			barstrokeWidth: 20,
        			minorTicks: 10,
        			majorTicks: [-40,-30,-20,-10,0,10,20,30,40,50],
					highlights: [ 
						{ from: -40, to: 0, color: 'rgba(0, 50, 200, .2)'},
						{ from: 30, to: 50, color: 'rgba(200, 0, 0, .2)'} 
					],
					colors: {
						plate: '#fff'
					},
					minValue: -40,
        			maxValue: 50,
        			value: 0,
					title: 'Temperature',
        			units: '°C',
        			colorValueBoxShadow: true
			});

			gauge1.draw();

			///////////////////////////////////////////////////////////////////////
        	// Fetchdata
        	//

			function fetchData(newguid) {
					
				console.log( newguid );

				// Get current measurement reading
				$.ajax({
			    	url : "<?php echo $MEASUREMENT_HOST;?>get_current.php?guid=" + newguid,
			    	type : "GET",
			    	success : function(data) {

				    	console.log(data);		

						datetime = data[0].date;
						current_value = data[0].value;	

						// Update gauge
						gauge1.value = current_value;	    

						guid = newguid;
				
						$("div#lastReading").text( "Last reading: " + current_value );					
					}
				});
			}

			$(document).ready(function(){

				// Get seco data
        		$.ajax({
          			url : "<?php echo $MEASUREMENT_HOST;?>get_seco.php",
          			type : "GET",
          			success : function(data) {

            			console.log(data);		

            			seco_name = [];
            			seco_description = [];
            			seco_guid = [];

            			for ( var i in data ) {

              				seco_name.push( data[i].name );
              				seco_description.push( data[i].description );
              				seco_guid.push( data[i].guid );
              				 
              				$("#top-seco-menu").append('<li class="nav-item"><a class="nav-link" ' +
                                  'href="javascript:fetchData(\'' + data[i].guid + '\');">' +
                                  '<span data-feather="activity"> </span> ' + data[i].name + '</a></li>');
		
							var guid = "<?php echo $guid; ?>";
							if ( !guid.localeCompare( data[i].guid ) ) {
								$("div#idInfoText").html( "<h2>" + data[i].name + "</h2>" );
							}
            			}
                                   
          			},
    
          			error : function(data) {

          			}

        		}); 				

				
						
				fetchData(guid);
				setInterval( function() { fetchData(guid); }, 10000 );
		
			});

		</script>

	</body>
</html>