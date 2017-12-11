<?php
  include_once("../classes/utilities.class.php");
  include_once("../classes/switches.class.php");
  include_once("../classes/switchDevice.class.php");
  include_once("../classes/devices.class.php");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTD | Switches & Devices</title>
  
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
        <!-- CURRENT SWITCHES -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Current Switches</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive">
                  <table id="switchTable" class="table table-striped table-bordered table-condensed no-margin">
                    <thead>
                      <tr>
                        <th class='text-center'>Switch ID</th>
                        <th class='text-center'>Switch Name</th>
                        <th class='text-center'>Total Ports</th>
                      </tr>
                    </thead>
                    <tbody class="text-center">
                      <?php 
                        $switches = new switches();
                        $switchesList = $switches->getAllSwitches();
                        // Append Body
                        if(count($switchesList) > 0){
                          for($i=0; $i<count($switchesList);$i++){
                            $switch = $switchesList[$i];
                            $switchID = $switch["switchID"];
                            $name = $switch["name"];
                            $totalPorts = $switch["totalPorts"];

                            // Create Table Row
                            echo "<tr>";
                              echo "<td>".$switchID."</td>";
                              echo "<td>".$name."</td>";
                              echo "<td>".$totalPorts."</td>";
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

        <!-- CURRENT DEVICES -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Current Devices</h3>
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
                        <th class='text-center'>IPv4</th>
                        <th class='text-center'>IPv6</th>
                        <th class='text-center'>MAC</th>
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
                            $deviceName = $device["name"];
                            $ipv4 = $device["ipv4"];
                            $ipv6 = $device["ipv6"];
                            $mac = $device["MAC"];

                            // Create Table Row
                            echo "<tr>";
                              echo "<td>".$deviceID."</td>";
                              echo "<td>".$deviceName."</td>";
                              echo "<td>".$ipv4."</td>";
                              echo "<td>".$ipv6."</td>";
                              echo "<td>".$mac."</td>";
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

        <!-- CURRENT SWITCH-DEVICE RELATIONSHIPS -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Current Switch-to-Device Association</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive">
                  <table id="switchDeviceTable" class="table table-striped table-bordered table-condensed no-margin">
                    <thead>
                      <tr>
                        <th class='text-center'>Switch ID</th>
                        <th class='text-center'>Switch Name</th>
                        <th class='text-center'>Port</th>
                        <th class='text-center'>Device Name</th>
                        <th class='text-center'>Device ID</th>
                      </tr>
                    </thead>
                    <tbody class="text-center">
                      <?php 
                        $switchDevice = new switchDevice();
                        $switchDeviceList = $switchDevice->getAllSwitchDevice();
                        
                        // Append Body
                        if(count($switchDeviceList) > 0){
                          for($i=0; $i<count($switchDeviceList);$i++){
                            $switchDevice = $switchDeviceList[$i];
                            $switchID = $switchDevice["switchID"];
                            $deviceID = $switchDevice["deviceID"];
                            $port = $switchDevice["port"];
                            $switch_name = $switchDevice["switch_name"];
                            $device_name = $switchDevice["device_name"];

                            // Create Table Row
                            echo "<tr>";
                              echo "<td>".$switchID."</td>";
                              echo "<td>".$switch_name."</td>";
                              echo "<td>".$port."</td>";
                              echo "<td>".$device_name."</td>";
                              echo "<td>".$deviceID."</td>";
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
    $("#switchTable").DataTable({
      destroy:true
    });

    $("#deviceTable").DataTable({
      destroy:true
    });

    $("#switchDeviceTable").DataTable({
      destroy:true
    });

  });

</script>
</body>
</html>
