<?php
  include_once("../classes/utilities.class.php");
  include_once("../classes/servers.class.php");
  
  // Get Graph Data
  if(isset($_POST["action"])){ 
    $serversClass = new servers();
    $servers = $serversClass->getAllServersByMonth();
    echo json_encode($servers);
    return;
  }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTD | Servers</title>
  
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
        <!-- CURRENT SERVERS -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Current Servers</h3>
                <div class="box-tools pull-right">
                  <button type="button" onclick="refreshServerStatus()" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-refresh"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div class="table-responsive">
                  <table id="serverTable" class="table table-striped table-bordered table-condensed no-margin">
                    <thead>
                      <tr>
                        <th class='text-center'>Server UID</th>
                        <th class='text-center'>Server Name</th>
                        <th class='text-center'>Server IP</th>
                        <th class='text-center'>Server Created On</th>
                        <th class='text-center'>Reputation Value</th>
                        <th class='text-center'>Bid Value</th>
                      </tr>
                    </thead>
                    <tbody class="text-center">
                      <?php 
                        $serverClass = new servers();
                        $servers = $serverClass->getAllServers();
                        // Append Body
                        if(count($servers) > 0){
                          $bidVal = floatval(file_get_contents("../data/bid_value.txt"));

                          for($i=0; $i<count($servers);$i++){
                            $server = $servers[$i];
                            $serverUID = $server["serverUID"];
                            $serverName = $server["serverName"];
                            $serverIP = $server["serverIP"];
                            $serverCreatedOn = $server["serverCreatedOn"];
                            $reputationValue = $server["reputationValue"];
                            $bidValue = $server["bidValue"];

                            $repLabel = "label-success";
                            $bidLabel = "label-success";

                            // Set reputation label
                            if($reputationValue > 0 && $reputationValue<0.50){
                              $repLabel = "label-danger";
                            } else if($reputationValue>=0.50 && $reputationValue<0.80){
                              $repLabel = "label-warning";
                            } else if($reputationValue>=0.80 && $reputationValue<=1.00){
                              $repLabel = "label-success";
                            }

                            // Set bid label
                            if($bidValue > 0 && $bidValue<$bidVal){
                              $bidLabel = "label-danger";
                            } else if($bidValue>=0.50 && $bidValue<0.80){
                              $bidLabel = "label-warning";
                            } else if($bidValue>=0.80 && $bidValue<=1.00){
                              $bidLabel = "label-success";
                            }

                            // Create Table Row
                            echo "<tr>";
                              echo "<td>".$serverUID."</td>";
                              echo "<td>".$serverName."</td>";
                              echo "<td>".$serverIP."</td>";
                              echo "<td>".$serverCreatedOn."</td>";
                              echo "<td><span class='label ".$repLabel."'>".$reputationValue."</span></td>";
                              echo "<td><span class='label ".$bidLabel."'>".$bidValue."</span></td>";
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
            <div class="box box-primary">
              <div class="box-header with-border">
                <i class="fa fa-bar-chart-o"></i>

                <h3 class="box-title">Number of Servers Used per Month</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                </div>
              </div>
              <div class="box-body">
                <div id="bar-chart" style="height: 300px;"></div>
              </div>
              <!-- /.box-body-->
            </div>
          </div>
        </div>
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
    $("#serverTable").DataTable({
      destroy:true
    });

    // Get number of servers per month
    var data = {action:"getGraphData"};
    $.ajax({
      type:"POST",
      data:data,
      dataType:"json",
      success:function(result){
        var bar_data_list = [];
        if(result.length > 0){
          for(var i=0; i<result.length; i++){
            var month_count = result[i];
            var bar_data_point = [];
            bar_data_point[0] = month_count["dates"];
            bar_data_point[1] = parseInt(month_count["totalCount"]);
            bar_data_list[i] = bar_data_point;
          }
          createBar(bar_data_list);
        }
      },
      error:function(xhr, status, error){
        console.log(xhr);
        console.log(status);
        console.log(error); 
      }
    }); // end ajax
  });

  /** BAR CHART **/
  function createBar(bar_data_list){
    var bar_data = {
      data: bar_data_list,
      color: "#3c8dbc"
    };
    
    $.plot("#bar-chart", [bar_data], {
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
        tickLength: 0
      }
    });
  }
  /* END BAR CHART */

  // get new server bid values
  function refreshServerStatus(){
    window.location = "servers.php";
  }
</script>
</body>
</html>
