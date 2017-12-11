<?php
include_once("../classes/logs.class.php");
include_once("../classes/packetLogs.class.php");



if(isset($_POST["action"])){
  $logs = new logs();
  $switches = $logs->getUniqueSwitches();
  $switchArray = array();
  $action = $_POST["action"];

    // If overall bandwidth or switch bandwidth
  if(strcasecmp($action,"overallBandwidth")==0){
    $bandwidth = $logs->getNetworkBandwidthPerTimestamp();
    echo json_encode($bandwidth);
    return;
  } else if (strcasecmp($action,"switchBandwidth")==0){
    $switches = $logs->getUniqueSwitches();
    $switchArray = array();

      // Get timestamps from each switch
    if(count($switches)>0){
      for($i=0; $i<count($switches);$i++){
        $switch_id = $switches[$i]["switch_id"];

        $switch_bandwidth = $logs->getBandwidthBySwitchID($switch_id);

        $switch_detail = array();
        $switch_detail["switch_id"] = $switch_id;
        $switch_detail["bandwidth"] = $switch_bandwidth;

        array_push($switchArray, $switch_detail);
      }
    }

    echo json_encode($switchArray);
    return;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTD | Dashboard</title>
  
  <?php include_once("../includes/css.php"); ?>

  <!-- DATATABLE CSS-->
  <link href="../plugins/datatables/css/dataTables.bootstrap.css" rel="stylesheet" />
  <link href="../plugins/vis-js/dist/vis-network.min.css" rel="stylesheet" />

  <style>
  .info-box-number{
    font-size:32px;
  }
</style>
</head>
<body class="hold-transition skin-blue sidebar-mini">

  <div class="wrapper">

    <!-- NAVBAR -->
    <?php include_once("../includes/navbar.php"); ?>

    <!-- SIDEBAR -->
    <!-- ALL CLASSES ARE INCLUDED IN THIS FILE -->
    <?php include_once("../includes/sidebar.php"); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
       
        <!-- Info boxes -->
        <div class="row">
          <div class="col-md-4 col-sm-6 col-xs-12">
            <a href="servers.php">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-sitemap"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Currently Available Servers</span>
                  <!-- VALUE OBTAINED FROM SIDEBAR.PHP -->
                  <span class="info-box-number"><?php echo count($servers); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
            </a>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-4 col-sm-6 col-xs-12">
            <a href="users.php">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Current Users</span>
                  <!-- VALUE OBTAINED FROM SIDEBAR.PHP -->
                  <span class="info-box-number"><?php echo count($allUsers); ?></span> 
                </div>
                <!-- /.info-box-content -->
              </div>
            </a>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <!-- /.col -->
          <div class="col-md-4 col-sm-6 col-xs-12">
            <a href="qvm.php">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-users"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">TOTAL QVMs</span>
                  <!-- VALUE OBTAINED FROM SIDEBAR.PHP -->
                  <span class="info-box-number"><?php echo count($qvms); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
            </a>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>

        <!-- BANDWIDTH UTILIZATION -->
        <div class="row">
          <div class="col-xs-12">
            <!-- interactive chart -->
            <div class="box box-primary">
              <div class="box-header with-border">
                <i class="fa fa-line-chart"></i>

                <h3 class="box-title">Bandwidth Utilization</h3>

                  <!-- <div class="box-tools pull-right">
                    Real time
                    <div class="btn-group" id="realtime" data-toggle="btn-toggle">
                      <button type="button" class="btn btn-default btn-xs active" data-toggle="on">On</button>
                      <button type="button" class="btn btn-default btn-xs" data-toggle="off">Off</button>
                    </div>
                  </div> -->
                </div>
                <div class="box-body">
                  <div id="overallBandwidth" style="height: 300px;"></div><!--interactive-->
                </div>
                <!-- /.box-body-->
              </div>
              <!-- /.box -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          <!-- CURRENT NETWORK -->
          <div class="row">
            <div class="col-xs-6">
              <!-- interactive chart -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <i class="fa fa-line-chart"></i>

                  <h3 class="box-title">Root Switch</h3>

                  <!-- <div class="box-tools pull-right">
                    Real time
                    <div class="btn-group" id="realtime" data-toggle="btn-toggle">
                      <button type="button" class="btn btn-default btn-xs active" data-toggle="on">On</button>
                      <button type="button" class="btn btn-default btn-xs" data-toggle="off">Off</button>
                    </div>
                  </div> -->
                </div>
                <div class="box-body">
                  <div id="switch2" style="height: 300px;"></div><!--interactive-->
                </div>
                <!-- /.box-body-->
              </div>
              <!-- /.box -->
            </div>
            <!-- /.col -->

            <div class="col-xs-6">
              <!-- interactive chart -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <i class="fa fa-line-chart"></i>

                  <h3 class="box-title">Slave Switch</h3>

                  <!-- <div class="box-tools pull-right">
                    Real time
                    <div class="btn-group" id="realtime" data-toggle="btn-toggle">
                      <button type="button" class="btn btn-default btn-xs active" data-toggle="on">On</button>
                      <button type="button" class="btn btn-default btn-xs" data-toggle="off">Off</button>
                    </div>
                  </div> -->
                </div>
                <div class="box-body">
                  <div id="switch1" style="height: 300px;"></div><!--interactive-->
                </div>
                <!-- /.box-body-->
              </div>
              <!-- /.box -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
    </div>

    <?php include_once("../includes/footer.php") ?>

  </div>
  <!-- ./wrapper -->

  <?php include_once("../includes/js.php"); ?>
  
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <!-- <script src="../plugins/admin-lte/js/pages/dashboard2.js"></script> -->

  <!-- DATATABLES -->
  <script src="../plugins/datatables/js/jquery.dataTables.min.js"></script>
  <script src="../plugins/datatables/js/dataTables.bootstrap4.min.js"></script>

  <script>
  $(document).ready(function(){
    // Activate Datatable
    $("#attackHistory").DataTable({
      destroy:true,
      order: [[2, "desc"]] // Timestamp
    });

    // Create Overall Bandwidth Line Chart
    setInterval(function(){
      createOverallBandwidthUtilizationGraph();
    },1000);

  });

    /* OVERALL BANDWIDTH UTILIZATION */
    function createOverallBandwidthUtilizationGraph(){
      $.ajax({
        method:"POST",
        dataType:"json",
        data:{action:"overallBandwidth"},
        success:function(result){
          if(result.length > 0){
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
              drawOverallPlot("overallBandwidth", plotter, xlabels, max_ylabel);
              createSwitchGraphs();
            }
          }
        }
      },
      error:function(xhr, status, error){
        console.log(xhr);
        console.log(status);
        console.log(error);
      }
    });
    }   
  // END OVERALL UTILIZATION

  /* CREATE GRAPHS FOR BOTH SWITCHES */
  function createSwitchGraphs(){
    $.ajax({
      method:"POST",
      dataType:"json",
      data:{action:"switchBandwidth"},
      success:function(result){
        if(result.length > 0){

          var record;
          var timestamp;
          var bytes;
          for(var i=0; i<result.length; i++){
            var plotter = [];
            var xlabels = [];
            var max_ylabel = 0;
            record = result[i]; // extract information for each switch
            var switch_id = record["switch_id"];
            var bandwidth = record["bandwidth"];
            
            // Loop each bandwidth information
            for(var j=0; j<bandwidth.length; j++){
              var plot = [];
              var xlabel = [];
              var rec = bandwidth[j];
              var timestamp = rec["timestamp"];
              var bytes = rec["bytes"];
              if(parseInt(bytes) > max_ylabel){
                max_ylabel = parseInt(bytes);
              }
              plot[0] = j;
              plot[1] = parseInt(bytes);
              plotter.push(plot);

              xlabel[0] = j;
              xlabel[1] = dateFormat(timestamp,"HH:MM");
              xlabels.push(xlabel);

              // Draw plot after reaching the last index
              if(j == bandwidth.length-1){
                //console.log(JSON.stringify(xlabels));
                drawOverallPlot("switch"+(i+1), plotter, xlabels, max_ylabel);
              }
            }
          }
        }
      },
      error:function(xhr, status, error){
        console.log(xhr);
        console.log(status);
        console.log(error);
      }
    });
  }   


  
  // Plot Interactive Graph for overall
  function drawOverallPlot(id, plotter, xlabels, max_ylabel){
    // console.log(id);
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
        /*ticks: xlabels,
        tickRotation:90*/
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
    }
    else {
      realtime = "off";
    }
    update();
  });

  /** END OVERALL BANDWIDTH UTILIZATION **/

  /*
   * Custom Label formatter
   * ----------------------
   */
   function labelFormatter(label, series) {
    return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
    + label
    + "<br>"
    + Math.round(series.percent) + "%</div>";
  }
</script>
</body>
</html>
