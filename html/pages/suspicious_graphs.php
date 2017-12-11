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
  <!-- <link href="../plugins/datatables/css/jquery.dataTables.css" rel="stylesheet" /> -->
  <link href="../plugins/datatables/css/dataTables.bootstrap.css" rel="stylesheet" />

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

        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Suspect Graphs</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive">
                  <table id="travTable" class="table table-striped table-bordered table-condensed no-margin">
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

<script>
  $(document).ready(function(){
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

    $("#travTable").DataTable({
      destroy:true,
      "order": [[ 2, "desc" ]]
    });

  });

</script>
</body>
</html>
