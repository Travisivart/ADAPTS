<?php
  include_once("../classes/utilities.class.php");
  include_once("../classes/users.class.php");

  
  // Get Graph Data
  if(isset($_POST["action"])){ 
    $users = new users();
    $userList = $users->getAllUsersByMonth();
    
    $finalArr = array();
    for($i=0; $i<count($userList);$i++){
      $obj = array();
      $datetime = $userList[$i]["dates"];
      $count = $userList[$i]["totalCount"];
      $obj["datetime"] = $datetime;
      $obj["count"] = $count;
      array_push($finalArr,$obj);
    }
    echo json_encode($finalArr);
    return;
  }

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTD | Users</title>
  
  <?php include_once("../includes/css.php"); ?>
  <link href="../plugins/datatables/css/dataTables.bootstrap.css" rel="stylesheet" />

  <style>
    .info-box-number{
      font-size:32px;
    }
    span.label{
      font-size:12px;
    }
  </style>
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
              <h3 class="box-title">Current Users</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="userTable" class="table table-striped table-bordered table-condensed no-margin">
                  <thead>
                  <tr>
                    <th class='text-center'>User ID</th>
                    <th class='text-center'>User Name</th>
                    <th class='text-center'>Source IP</th>
                    <th class='text-center'>Connection Start Time</th>
                    <th class='text-center'>Connection Stop Time</th>
                  </tr>
                  </thead>
                  <tbody class="text-center">
                    <?php
                      $users = new users();
                      $userList = $users->getAllUsers();
                      // Append Body
                      if(count($userList) > 0){
                        for($i=0; $i<count($userList);$i++){
                          $user = $userList[$i];
                          $userUID = $user["userUID"];
                          $username = $user["username"];
                          $ipAddress = $user["ipAddress"];
                          $connectionStartTime = $user["connectionStartTime"];
                          $connectionStopTime = $user["connectionStopTime"];

                          // Create Table Row
                          echo "<tr>";
                          echo "<td>".$userUID."</td>";
                          echo "<td>".$username."</td>";
                          echo "<td>".$ipAddress."</td>";
                          echo "<td>".$connectionStartTime."</td>";
                          echo "<td>".$connectionStopTime."</td>";
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

              <h3 class="box-title">Number of Users per Month</h3>

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
    $("#userTable").DataTable({
      order: [[ 3, "desc" ]],
      destroy:true
    });

    // Get number of users per month
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
            bar_data_point[0] = month_count["datetime"];
            bar_data_point[1] = parseInt(month_count["count"]);
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
    /* END BAR CHART */
  }
</script>
</body>
</html>
