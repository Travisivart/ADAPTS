<?php
  /*include_once("../classes/utilities.class.php");*/
  include_once("../classes/blacklist.class.php");


  if(isset($_POST["action"])){
    $blacklist = new blacklist();
    $blacklisted = $blacklist->getAllBlacklist();
    echo json_encode($blacklisted);
    return;
  }
  
  /*for($i=0; $i<count($blacklisted);$i++){
    $record = $blacklisted[$i];
    $ip = $record["ipAddress"];
    $mac = $record["macAddress"];

    echo "<tr>";
    echo "<td>".$mac."</td>";
    echo "<td>".$ip."</td>";
    echo "</tr>";
  }*/
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- REFRESH PAGE EVERY ** SECOND -->
  <!-- <meta http-equiv="refresh" content="5"> -->

  <title>MTD | Blacklisted IP</title>
  
  <?php include_once("../includes/css.php"); ?>
  <link href="../plugins/datatables/css/dataTables.bootstrap.css" rel="stylesheet" />
  <!-- <link href="../plugins/datatables/css/jquery.dataTables.css" rel="stylesheet" /> -->

  <style>
    .info-box-number{
      font-size:32px;
    }
    .row{
      margin-left:0;
      margin-right:0;
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
      <!-- END OF CHART -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Blacklisted IPs</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="attackHistory" class="table table-bordered table-condensed table-striped">
                  <thead>
                    <tr>
                      <th class="text-center">IP Address</th>
                      <th class="text-center">Mac Address</th>
                      <th class="text-center">Blacklisted On</th>
                    </tr>
                  </thead>
                  <tbody id="tableBody" class="text-center">
                    <!-- GET FROM JQUERY -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
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
<!-- DATATABLES -->
<script src="../plugins/datatables/js/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
<script>
  $(document).ready(function(){
    // Get table at first load
    getBlacklist();

    // Every 2 seconds reload the table
    setInterval(function(){
      getBlacklist();
    },2000);      

    // Activate Datatable
    $("#attackHistory").DataTable({
      //info:false,
      //paging:false,
      //searching:false,
      destroy:true,
      order:[[2,"desc"]]
    });
  });

  // Load blacklist on table
  function getBlacklist(){
    $.ajax({
      method:"POST",
      dataType:"json",
      data:{action:"getBlacklist"},
      success:function(result){
        // console.log(JSON.stringify(result));

        $("#tableBody").empty();
        if(result.length > 0){
          for(var i=0; i<result.length; i++){
            var blacklist = result[i];
            var ip = blacklist["ipAddress"];
            var mac = blacklist["macAddress"];
            var blacklistedOn = blacklist["blacklistedOn"];

            // Add to table body
            $("#tableBody").append("<tr>");
            $("#tableBody").find("tr:last").append("<td>"+ip+"</td><td>"+mac+"</td><td>"+blacklistedOn+"</td>");
            $("#tableBody").append("</tr>");
          }
        } else {
          $("#tableBody").append("<tr><td colspan='3'>No Data Available</td></tr>");
        }
      },
      error:function(xhr, status, error){
      }
    });
  }
</script>

</body>
</html>
