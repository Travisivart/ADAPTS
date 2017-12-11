<?php
  include_once("../classes/utilities.class.php");
  include_once("../classes/packetLogs.class.php");
  include_once("../classes/devices.class.php");
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
                <div class="row">
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
                </div>
                <div class="row" style="padding:15px">
                  <div id="suspiciousness" style="height: 300px;"></div>
                </div>
              </div>
            </div>
          </div>      
        </div>
        <!-- /.row -->

        <!-- CURRENT SUSPECT SCORES -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Suspect Scores</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive">
                  <table id="deviceTable" class="table table-striped table-bordered table-condensed no-margin">
                    <thead>
                      <tr>
                        <th class='text-center'>Device ID</th>
                        <th class='text-center'>Device Name</th>
                        <th class='text-center'>Device Suspect Scores</th>
                      </tr>
                    </thead>
                    <tbody class="text-center">
                      <?php 
                        $devices = new devices();
                        $deviceList = $devices->getAllDevices();

                        // Append Body
                        if(count($deviceList) > 0){
                          for($i=0; $i<count($deviceList);$i++){
                            $device = $deviceList[$i];
                            $deviceID = $device["deviceID"];
                            $name = $device["name"];
                            $ss = $device["suspectScore"];

                            // Create Table Row
                            echo "<tr>";
                              echo "<td>".$deviceID."</td>";
                              echo "<td>".$name."</td>";
                              echo "<td>".$ss."</td>";
                            echo "</tr>";
                          }
                        } 
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>      
        </div>
        <!-- /.row -->

        <!-- CURRENT CONNECTIONS -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Number of Connections per IP</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive">
                  <table id="connectionsTable" class="table table-striped table-bordered table-condensed no-margin">
                    <thead>
                      <tr>
                        <th class='text-center'>Source IP</th>
                        <th class='text-center'>Total Number of Connections</th>
                      </tr>
                    </thead>
                    <tbody class="text-center">
                      <?php 
                        $packetLogs = new packetLogs();
                        $packetLogsList = $packetLogs->getIPConnPerDevice();

                        // Append Body
                        if(count($packetLogsList) > 0){
                          for($i=0; $i<count($packetLogsList);$i++){
                            $packetLog = $packetLogsList[$i];
                            $src_ip = $packetLog["ip_src"];
                            $total = $packetLog["total"];

                            // Create Table Row
                            echo "<tr>";
                              echo "<td>".$src_ip."</td>";
                              echo "<td>".$total."</td>";
                            echo "</tr>";
                          }
                        } 
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>      
        </div>
        <!-- /.row -->

        <!-- CURRENT FLOWS -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Flows per Device</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive">
                  <table id="flowsTable" class="table table-striped table-bordered table-condensed no-margin">
                    <thead>
                      <tr>
                        <th class='text-center'>Source IP</th>
                        <th class='text-center'>Destination IP</th>
                        <th class='text-center'>Total Flows</th>
                      </tr>
                    </thead>
                    <tbody class="text-center">
                      <?php 
                        $packetLogs = new packetLogs();
                        $packetLogsList = $packetLogs->getFlowsPerDevice();

                        // Append Body
                        if(count($packetLogsList) > 0){
                          for($i=0; $i<count($packetLogsList);$i++){
                            $packetLog = $packetLogsList[$i];
                            $src_ip = $packetLog["ip_src"];
                            $dst_ip = $packetLog["ip_dst"];
                            $total = $packetLog["total"];

                            // Create Table Row
                            echo "<tr>";
                              echo "<td>".$src_ip."</td>";
                              echo "<td>".$dst_ip."</td>";
                              echo "<td>".$total."</td>";
                            echo "</tr>";
                          }
                        }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>      
        </div>
        <!-- /.row -->

        <!-- CURRENT BYTES -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Number of Bytes per IP</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive">
                  <table id="bytesTable" class="table table-striped table-bordered table-condensed no-margin">
                    <thead>
                      <tr>
                        <th class='text-center'>Source IP</th>
                        <th class='text-center'>Destination IP</th>
                        <th class='text-center'>Frame Length</th>
                        <th class='text-center'>Total</th>
                      </tr>
                    </thead>
                    <tbody class="text-center">
                      <?php 
                        $packetLogs = new packetLogs();
                        $packetLogsList = $packetLogs->getNumberOfBytesPerIP();

                        // Append Body
                        if(count($packetLogsList) > 0){
                          for($i=0; $i<count($packetLogsList);$i++){
                            $packetLog = $packetLogsList[$i];
                            $src_ip = $packetLog["ip_src"];
                            $dst_ip = $packetLog["ip_dst"];
                            $frame_len = $packetLog["frame_length"];
                            $total = $packetLog["total"];

                            // Create Table Row
                            echo "<tr>";
                              echo "<td>".$src_ip."</td>";
                              echo "<td>".$dst_ip."</td>";
                              echo "<td>".$frame_len."</td>";
                              echo "<td>".$total."</td>";
                            echo "</tr>";
                          }
                        }
                      ?>
                    </tbody>
                  </table>
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

<!-- DATATABLES -->
<script src="../plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/js/dataTables.bootstrap4.min.js"></script>

<!-- DATEPICKER -->
<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>

<script>

  $(document).ready(function(){
    $("#startDate").datepicker({
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
    });
  });

  // Reset Dates
  function resetDate(id){
    $("#"+id).datepicker("setDate",null);
  }

  // If fieldtype changes
  function updateGraph(){
    var graphType = $("#fieldType option:selected").val();
    if(graphType != "na"){
      var startDate = $("#startDate").val();
      var endDate = $("#endDate").val();
      var data = {action:"getBytes", type: graphType};
      if(startDate != "" && endDate != ""){
        data["startDate"] = startDate;
        data["endDate"] = endDate;
      } 

      // Get json array
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
    } else {
      alert("Select a valid type");
      return;
    }
  }

  /* OVERALL SUSPICIOUSNESS SCORE GRAPH */
  function createSuspiciousnessGraph(result){
    var plotter = [];
    var xlabels = [];
    var max_ylabel = 0;
    var record;
    var timestamp;
    var bytes;
    for(var i=0; i<result.length; i++){
      var plot = [];
      var xlabel = [];
      record = result[i];
      timestamp = record["timestamp"];
      bytes = record["bytes"];
      if(parseInt(bytes) > max_ylabel){
        max_ylabel = parseInt(bytes);
      }
      plot[0] = i;
      plot[1] = parseInt(bytes);
      plotter.push(plot);

      xlabel[0] = i;
      xlabel[1] = timestamp;
      xlabels.push(xlabel);

      // Draw plot after reaching the last index
      if(i == result.length-1){
        drawGraph("suspiciousness", plotter, xlabels, max_ylabel);
      }
    }
  }   
// END OVERALL UTILIZATION

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
      show: true
    },
    xaxis: {
      show: true
    }
  });
}

var updateInterval = 500; //Fetch data ever x milliseconds
var realtime = "off"; //If == to on then fetch data every x seconds. else stop fetching
/*$("#realtime .btn").each(function(){
  if($(this).prop("data-toggle")=="on"){
    $(this).removeClass("active");
  } else {
    $(this).addClass("active");
  }
});*/

function update() {
  interactive_plot.setData([getRandomData()]);

  // Since the axes don't change, we don't need to call plot.setupGrid()
  interactive_plot.draw();
  if (realtime === "on")
    setTimeout(update, updateInterval);
}

//INITIALIZE REALTIME DATA FETCHING
if (realtime === "on") {
  update();
}

//REALTIME TOGGLE
$("#realtime .btn").click(function () {
  if ($(this).data("toggle") === "on") {
    realtime = "on";
  } else {
    realtime = "off";
  }
  update();
});

/*
 * Flot Interactive Chart
 * -----------------------
 */
// We use an inline data source in the example, usually data would
// be fetched from a server
var data = [], totalPoints = 100;

function getRandomData() {

  if (data.length > 0)
    data = data.slice(1);

  // Do a random walk
  while (data.length < totalPoints) {

    var prev = data.length > 0 ? data[data.length - 1] : 50,
        y = prev + Math.random() * 10 - 5;

    if (y < 0) {
      y = 0;
    } else if (y > 100) {
      y = 100;
    }

    data.push(y);
  }

  // Zip the generated y values with the x values
  var res = [];
  for (var i = 0; i < data.length; ++i) {
    res.push([i, data[i]]);
  }

  return res;
}

var interactive_plot = $.plot("#suspiciousness", [getRandomData()], {
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
    max: 100,
    show: true
  },
  xaxis: {
    show: true
  }
});

var updateInterval = 500; //Fetch data ever x milliseconds
var realtime = "on"; //If == to on then fetch data every x seconds. else stop fetching
function update() {

  interactive_plot.setData([getRandomData()]);

  // Since the axes don't change, we don't need to call plot.setupGrid()
  interactive_plot.draw();
  if (realtime === "on")
    setTimeout(update, updateInterval);
}
/*
 * END INTERACTIVE CHART
 */

</script>
</body>
</html>
