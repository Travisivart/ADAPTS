<?php
include_once("../classes/utilities.class.php");
include_once("../classes/packetLogs.class.php");
include_once("../classes/devices.class.php");
include_once("../classes/suspiciousnessScore.class.php");
include_once("../classes/policies.class.php");

session_start();
extract($_POST);

$devices = new devices();
$deviceList = $devices->getAllDevices();


  // Get Suspiciousness score
if(isset($_POST["action"])){
	$action = $_POST["action"];
	if(strcasecmp($action, "bar-graph")==0){
		$name = $_POST["name"];
		$suspiciousness = new suspiciousnessScore();
		$scores = $suspiciousness->getSuspiciousnessScoreByDevice($name);
		echo json_encode($scores);
		return;
	} else if(strcasecmp($action, "updatePolicy")==0){
	    $deviceID = $_POST["deviceID"];
	    $policies = new policies();
	    $blockDevice = $policies->blockPolicy($deviceID);
	    echo $blockDevice;
	    return;
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>MTD | Suspiciousness Score</title>

	<?php include_once("../includes/css.php"); ?>
	<link href="../plugins/datatables/css/dataTables.bootstrap.css" rel="stylesheet" />
	<link href="../plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

	<style>
	.info-box-number{
		font-size:32px;
	}
	span.label{
		font-size:12px;
	}
	.flot-x-axis .flot-tick-label {
	    white-space: nowrap;
	    transform: translate(-9px, 0) rotate(-70deg);
	    text-indent: -100%;
	    transform-origin: top right;
	    text-align: right !important;
	}
</style>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<!-- NAVBAR -->
		<?php include_once("../includes/navbar.php"); ?>

		<!-- SIDEBAR -->
		<?php include_once("../includes/sidebar.php"); ?>

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Main content -->
			<section class="content">
				<!-- SUSPECT SCORES BAR GRAPH -->
				<div class="row">
					<div class="col-md-12">
						<div class="box box-default">
							<div class="box-header with-border">
								<h3 class="box-title">Suspiciousness Score Trend by Device</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
									<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body">
								<div class="row">
									<div class="col-md-2 col-xs-12" style="margin-bottom:5px">
										<select id="device" class="form-control">
											<?php 
											$devices = new devices();
											$deviceList = $devices->getAllDevices();

	                        				// Append Body
											if(count($deviceList) > 0){
												for($i=0; $i<count($deviceList);$i++){
													$deviceID = $deviceList[$i]["deviceID"];
													$deviceName = $deviceList[$i]["name"];
													if(strcasecmp($deviceName, "user1")==0){
														echo "<option device-id='".$deviceID."' value='".$deviceName."' selected>".$deviceName."</option>";	
													} else {
														echo "<option device-id='".$deviceID."' value='".$deviceName."'>".$deviceName."</option>";
													}
												}
											}
											?>
										</select>
									</div>
									<div id="adapt" class="col-md-4 col-xs-12 hidden">
										<button class="btn btn-danger" onclick="updatePolicy()">ADAPT</button>
									</div>
								</div>
								<div class="row" style="padding:15px; padding-bottom:0">
									<div id="bar-chart" style="height: 300px;"></div>
								</div>
							</div>
						</div>
					</div>      
				</div>
				<!-- /.row -->

				<!-- SUSPECT SCORES GRAPH -->
				<div class="row">
					<div class="col-md-12">
						<div class="box box-default">
							<div class="box-header with-border">
								<h3 class="box-title">Suspiciousness Graph</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
									</button>
									<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
								</div>
							</div>
							<div class="box-body">
                <!-- <div class="row">
                  <div class="col-md-2 col-xs-12" style="margin-bottom:5px">
                    <select id="fieldType" class="form-control">
                      <option value="na">Select Type</option>
                      <option value="bytes">Bytes</option>
                      <option value="flows">Flows</option>
                      <option value="ip">IP</option>
                    </select>
                  </div>
                  <div class="col-md-4 col-xs-12 " style="margin-bottom:5px">
                    <div class="input-group">
                      <input id="startDate" class="form-control" type="text" placeholder="Start Date" readonly />
                      <span class="input-group-btn">
                        <button id="startDateButton" class="btn btn-default" type="button" onclick="resetDate('startDate')"><span class="fa fa-times"></span></button>
                      </span>
                    </div>
                  </div>
                  <div class="col-md-4 col-xs-12" style="margin-bottom:5px">
                    <div class="input-group">
                      <input id="endDate" class="form-control" type="text" placeholder="End Date" readonly/>
                      <span class="input-group-btn">
                        <button id="endDateButton" class="btn btn-default" type="button" onclick="resetDate('endDate')"><span class="fa fa-times"></span></button>
                      </span>
                    </div>
                  </div>
                  <div class="col-md-2 col-xs-12" style="margin-bottom:5px">
                    <button class="btn btn-default form-control" onclick="updateGraph()">Update</button>
                  </div>
              </div> -->
              <div class="row" style="padding:15px">
              	<div id="suspiciousness" style="height: 300px;"></div>
              </div>
          </div> 
      </div>
  </div>      
</div>
<!-- /.row -->
<!-- END OF CHART -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
</div>

<?php include_once("../includes/footer.php") ?>

</div>
<!-- ./wrapper -->

<?php include_once("../includes/js.php"); ?>

<!-- FLOT CHARTS -->
<script src="../plugins/flot/jquery.flot.min.js"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="../plugins/flot/jquery.flot.resize.min.js"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="../plugins/flot/jquery.flot.pie.min.js"></script>
<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
<script src="../plugins/flot/jquery.flot.categories.min.js"></script>
<!-- LABELS PLUGIN -->
<script src="../plugins/flot/jquery.flot.axislabels.js"></script>

<!-- DATATABLES -->
<script src="../plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/js/dataTables.bootstrap4.min.js"></script>

<!-- DATEPICKER -->
<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>

<script>
  	// Document ready function
  	$(document).ready(function(){
	    /*$("#startDate").datepicker({
	      dateFormat:"yy-mm-dd"
	    });
	    $("#endDate").datepicker({
	      dateFormat:"yy-mm-dd"
	    });

	    // Enable datatable
	    $("#deviceTable").DataTable({
	      destroy:true,
	      "order": [[ 2, "desc" ]]
	    });

	    $("#connectionsTable").DataTable({
	      destroy:true,
	      "order": [[ 1, "desc" ]]
	    });

	    $("#flowsTable").DataTable({
	      destroy:true,
	      "order": [[ 2, "desc" ]]
	    });

	    $("#bytesTable").DataTable({
	      destroy:true,
	      "order": [[ 2, "desc" ]]
	  	});*/

	    var deviceName = $("#device").val();
	    createBarGraph(deviceName);

    	createLineGraph();
	});

  	// Reset Dates
  	function resetDate(id){
	  	$("#"+id).datepicker("setDate",null);
  	}

  	// If fieldtype changes
  	function createLineGraph(){
		// Get json array
		var data = {action: "getSuspiciousnessScoreByTime"};
		$.ajax({
			url:"../ajax-handlers/suspiciousness-handler.php",
			data:data,
			method:"POST",
			dataType:"json",
			success:function(result){
				createSuspiciousnessGraph(result);
			},
			error:function(xhr, status, error){
				console.log(JSON.stringify(xhr));
				console.log(JSON.stringify(status));
				console.log(JSON.stringify(error));
			}
		});
	}

	/* OVERALL SUSPICIOUSNESS SCORE GRAPH */
	function createSuspiciousnessGraph(result){
		var plotter = [];
		var xlabels = [];
		var max_ylabel = 0;
		var record;
		var frame_time;
		var score;
		// for(var i=0; i<result.length; i++){
		for(var i=0; i<result.length; i++){
			var plot = [];
			var xlabel = [];
			record = result[i];
			frame_time = new Date(1000*record["frame_time"]);

			var month = frame_time.getMonth().toString();
			if(month.length == 1){
				month = "0" + month;
			}
			var day = frame_time.getUTCDate().toString();
			if(day.length == 1){
				day = "0" + day;
			}
			var hour = frame_time.getHours().toString();
			if(hour.length == 1){
				hour = "0" + hour;
			}
			var minutes = frame_time.getMinutes().toString();
			if(minutes.length == 1){
				minutes = "0" + minutes;
			}
			var seconds = frame_time.getSeconds().toString();
			if(seconds.length == 1){
				seconds = "0" + seconds;
			}


			frame_time = hour + ":" + minutes + ":" + seconds; // month + "-" + day + " " + 
			score = record["score"];
			if(parseInt(score) > max_ylabel){
				max_ylabel = parseInt(score);
			}
			plot[0] = i;
			plot[1] = parseInt(score);
			plotter.push(plot);

			xlabel[0] = i;
			xlabel[1] = frame_time;
			xlabels.push(xlabel);

	      	// Draw plot after reaching the last index
	      	if(i == result.length-1){
	      		drawGraph("suspiciousness", plotter, xlabels, max_ylabel);
	      	}
  		}
	}

	// Plot Interactive Graph for overall
	function drawGraph(id, plotter, xlabels, max_ylabel){
		var interactive_plot = $.plot("#"+id, [plotter], {
			grid: {
				borderColor: "#f3f3f3",
				borderWidth: 1,
				tickColor: "#f3f3f3"
			},
			series: {
		      	shadowSize: 0, // Drawing is faster without shadows
			    color: "#3c8dbc"
		  	},
			lines: {
				fill: true, //Converts the line chart to area chart
			    color: "#3c8dbc"
			},
			yaxis: {
				min: 0,
			  	max: max_ylabel,
			  	show: true,
        		axisLabelPadding: 30
		  	},
			xaxis: {
				show: true,
				ticks: xlabels,
				axisLabel: "Timestamp",
        		axisLabelUseCanvas: true,
        		axisLabelFontSizePixels: 15,
        		axisLabelFontFamily: 'Calibri, Arial',
        		axisLabelPadding: 70

		  	}
		});
	}
	/*
	 * END INTERACTIVE CHART
	 */

	 /*
	 * BAR CHART
	 * ---------
	 */
	// Get data
	var prev = "na"; // Revert to previously selected dropdown

	$("#device").on("change",function(){
		var deviceName = $(this).val();
		if(deviceName != "na"){
			createBarGraph(deviceName);
		}
	});

	function createBarGraph(deviceName){
		var data = {action:"bar-graph", name:deviceName};
		$.ajax({
			method:"POST",
			data:data,
			dataType:"json",
			success:function(result){
	        // If no values returned alert
	        if(result.length == 0){
	        	alert("No data found");
	        	$("#device").val(prev);
	        	return;
	        }

	        // If result found
	        prev = deviceName;
	        data = [];
	        var hideAdapt = 0;
	        for(i=0; i<result.length;i++){
	        	var row = result[i];
	        	var traceID = row["traceID"];
	        	var score = parseFloat(row["score"]);
	        	if(parseInt(score) > 80){
	        		hideAdapt = parseInt(hideAdapt) + 1;
	        	}
	        	var val = [traceID, score];
	        	data.push(val);
	        }

	        if(hideAdapt > 0){
	        	$("#adapt").removeClass("hidden");
	        } else{
	        	$("#adapt").addClass("hidden");
	        }

	        // Set values and graph color
	        var bar_data = {
	        	data: data,
	        	color: "#3c8dbc"
	        };

	        // Plot the bar graph
	        $.plot("#bar-chart", [bar_data], {
	        	axisLabels: {
	        		show: true
	        	},
	        	grid: {
	        		borderWidth: 1,
	        		borderColor: "#f3f3f3",
	        		tickColor: "#f3f3f3"
	        	},
	        	series: {
	        		bars: {
	        			show: true,
	        			barWidth: 0.5,
	        			align: "center"
	        		}
	        	},
	        	xaxis: {
	        		mode: "categories",
	        		tickLength: 0,
	        		axisLabel: "Trace IDs",
	        		axisLabelUseCanvas: true,
	        		axisLabelFontSizePixels: 15,
	        		axisLabelFontFamily: 'Calibri, Arial',
	        		axisLabelPadding: 10
	        	},
	        	yaxis: {
	        		position: "left",
	        		axisLabel: "Suspiciousness Score",
	        		axisLabelFontSizePixels: 15,
	        		axisLabelFontFamily: 'Calibri, Arial',
	        		axisLabelPadding: 10
	        	}
	        });
	    },
	    error:function(xhr, status, error){
	    	console.log(JSON.stringify(xhr));
	    	console.log(JSON.stringify(status));
	    	console.log(JSON.stringify(error));
	    }
	});
	}


	var bar_data = {
		data: [["January", 10], ["February", 8], ["March", 4], ["April", 13], ["May", 17], ["June", 9]],
		color: "#3c8dbc"
	};
	/* END BAR CHART */

	function updatePolicy(){
		var deviceID = $("#device option:selected").attr("device-id");
		$.ajax({
			method: "POST",
			data: {action: "updatePolicy", deviceID: deviceID},
			dataType: "text",
			success: function(result){
				if(result){
					alert("Successfully blocked!");
				}
			},
			error: function(xhr, status, error){
				console.log(xhr);
				console.log(status);
				console.log(error);
			}
		});

	}

</script>
</body>
</html>
