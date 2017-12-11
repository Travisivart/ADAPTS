<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTD | Attack History</title>
  
  <?php include_once("../includes/css.php"); ?>
  <!-- <link href="../plugins/datatables/css/jquery.dataTables.css" rel="stylesheet" /> -->
  <link href="../plugins/datatables/css/dataTables.bootstrap.css" rel="stylesheet" />


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
              <h3 class="box-title">Attack History</h3>
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
                    <th class="text-center">Attack No</th>
                    <th class="text-center">Source IP</th>
                    <th class="text-center">Destination IP</th>
                    <th class="text-center">Time To Live (TTL)</th>
                    <th class="text-center">Start Time</th>
                  </tr>
                  </thead>
                  <tbody class="text-center">
                  <!-- append via jquery -->
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
    loadAttackData();
  });

  function getJSONData(){
    var jsondata = [];
    for(var i=0; i<100; i++){
      var obj = {};
      obj["attack_no"] = i+1;
      obj["source"] = "192.168.1."+(i+1);
      obj["destination"] = "192.168.1."+((i*10)+1);
      obj["ttl"] = "128";
      obj["start_time"] = "2017-04-01 00:00:00";
      jsondata[i] = obj;
    }
    return jsondata;
  }

  function loadAttackData(){
    var attackdata = getJSONData();
    if(attackdata.length == 0){
      $("#attackHistory").find("tbody").empty().append("<tr><td colspan='5'>NO DATA AVAILABLE</td></tr>")
    } else{
      for(var i=0; i<attackdata.length; i++){
        var record = attackdata[i];
        var attack_no = record["attack_no"];
        var source = record["source"];
        var destination = record["destination"];
        var ttl = record["ttl"];
        var start_time = record["start_time"];

        // Append to table
        $("#attackHistory").find("tbody").append("<tr></tr>");
        $("#attackHistory").find("tbody tr:last").append("<td>"+attack_no+"</td>");
        $("#attackHistory").find("tbody tr:last").append("<td>"+source+"</td>");
        $("#attackHistory").find("tbody tr:last").append("<td>"+destination+"</td>");
        $("#attackHistory").find("tbody tr:last").append("<td>"+ttl+" seconds</td>");
        $("#attackHistory").find("tbody tr:last").append("<td>"+start_time+"</td>");

      }
    }

    // Activate Datatable
    $("#attackHistory").DataTable({
      //info:false,
      //paging:false,
      //searching:false,
      destroy:true
    });
  }
</script>

</body>
</html>
