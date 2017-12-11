<?php
  include_once("../classes/utilities.class.php");

  extract($_POST);

  // Update threshold value if sent from jquery
  if(isset($_POST["action"]) && isset($_POST["shell_command"])){
    $shell = $_POST["shell_command"];
    $comm_output = shell_exec ($shell);
    echo $comm_output;
    return;
  } 

  // Update threshold value if sent from jquery
  if(isset($_POST["action"]) && isset($_POST["bid"])){
    $file = fopen("../data/bid_value.txt","w");
    $bid = $_POST["bid"];
    fwrite($file, $bid);
    fclose($file);
    return;
  } 

  // Upload Policy
  if(isset($_POST["uploadJSONPolicyID"]) && isset($_POST["policy"])){
    $policyID = $_POST["uploadJSONPolicyID"]; // SHOULD REFER TO WHICH FILE TO CALL
    $policy = $_POST["policy"]; // JSON
    $utilities = new utilities();
    $method = "POST";
    $url = "http://pcvm3-3.geni.it.cornell.edu:9000/repeater/update_json";
    $restAPI = $utilities->CallAPI($method, $url, $policy);
    echo json_encode($restAPI);
    return;
  }


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>MTD | Settings</title>
    
    <?php include_once("../includes/css.php"); ?>

    <style>
      .info-box-number{
        font-size:32px;
      }
      .nav-pills > li{
        background-color:#eee;
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
	  <!-- THRESHOLD -->
          <div class="row">
            <div class="col-md-12">
              <!-- Horizontal Form -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Command Line</h3>
                </div>
                <!-- /.box-header -->
                
                <!-- form start -->
                <form class="form-horizontal">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="shell_command" class="col-sm-2 control-label">Run Command</label>

                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="shell_command" placeholder="e.g. sudo -u username" />
                      </div>
                    </div>
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer">
                    <button type="button" class="btn btn-default">Cancel</button>
                    <button type="button" onclick="shellScript();" class="btn btn-success pull-right">Run Shell</button>
                  </div>
                  <!-- /.box-footer -->
                </form>
              </div>
              <!-- /.box -->
            </div>
            <!-- /.col-md-12 -->
          </div>
          <!-- END THRESHOLD-->
          <!-- THRESHOLD -->
          <div class="row">
            <div class="col-md-12">
              <!-- Horizontal Form -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Set Threshold</h3>
                </div>
                <!-- /.box-header -->
                
                <!-- form start -->
                <form class="form-horizontal">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="cthreshold" class="col-sm-2 control-label">Current Threshold</label>

                      <div class="col-sm-10">
                        <?php
                          $bid = number_format(floatval(file_get_contents("../data/bid_value.txt")),2);
                        ?>
                        <input type="text" class="form-control" id="cthreshold" placeholder="Current Threshold" value="<?php echo $bid ?>" readonly />
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="nthreshold" class="col-sm-2 control-label">New Threshold</label>

                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="nthreshold" placeholder="New Threshold" />
                      </div>
                    </div>
                  </div>
                  <!-- /.box-body -->
                  <div class="box-footer">
                    <button type="button" class="btn btn-default">Cancel</button>
                    <button type="button" onclick="saveNewBid();" class="btn btn-success pull-right">Update</button>
                  </div>
                  <!-- /.box-footer -->
                </form>
              </div>
              <!-- /.box -->
            </div>
            <!-- /.col-md-12 -->
          </div>
          <!-- END THRESHOLD-->
          <!-- THRESHOLD -->
          <div class="row">
            <div class="col-md-12">
              <!-- Horizontal Form -->
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Set Policies</h3>
                </div>
                <!-- /.box-header -->
                
                <!-- form start -->
                <div class="box-body">
                  <ul class="nav nav-pills">
                    <li role="presentation"><a id="1" href="#">Open All Connections</a></li>
                    <li role="presentation"><a id="2" href="#">Redirect Attacker 1</a></li>
                    <li role="presentation"><a id="3" href="#">Redirect Attacker 2</a></li>
                    <li role="presentation"><a id="4" href="#">Redirect Attacker 3</a></li>
                    <li role="presentation"><a id="5" href="#">Redirect All Attackers</a></li>
                    <li role="presentation"><a id="6" href="#">Block All Connections</a></li>
                  </ul>

                  <textarea id="policies" rows="10" placeholder=" Enter JSON" style="width:100%; margin-top:10px"></textarea>

                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                  <button type="button" onclick="clearArea()" class="btn btn-default">Clear</button>
                  <button type="button" onclick="uploadJSONPolicies()" class="btn btn-success pull-right">Submit New Policy</button>
                </div>
                <!-- /.box-footer -->
              </div>
              <!-- /.box -->
            </div>
            <!-- /.col-md-12 -->
          </div>
          <!-- END THRESHOLD-->
        <!-- /.row -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    

    <?php include_once("../includes/footer.php") ?>
  </div>
  <!-- ./wrapper -->

  <?php include_once("../includes/js.php"); ?>
  <!-- <script src="../js/json/block_attacker1.json" type="text/javascript"></script> -->
  
  <script>
  $(".nav-pills li").on('click',function(event){
    event.preventDefault();
    var clicked = parseInt($(this).find('a').prop("id"));
    var count = 1;
    $(this).parent(".nav-pills").find('li').each(function(){
      if(clicked == count){
        $(this).addClass("active");
        if(count == 1){
          getJsonData("all_port_access");
        } else if(count == 2){
          getJsonData("block_attacker1");
        } else if(count == 3){
          getJsonData("block_attacker2");
        } else if(count == 4){
          getJsonData("block_attacker3");
        } else if(count == 5){
          getJsonData("block_all_attackers");
        } else if(count == 6){
          getJsonData("block_everyone");
        }
      } else {
        $(this).removeClass("active");
      }
      count++;
    });
  });

  // Get JSON data
  function getJsonData(filename){
    $("#policies").text("");
    $.getJSON("../js/json/"+filename+".json",function(data){
      $("#policies").text("");
      $("#policies").text(JSON.stringify(data));
    });
  }

  // Upload JSON policies
  function uploadJSONPolicies(){  
    var active = 0;
    var policy = $("#policies").text();

    // Get ID of redirection
    $(".nav-pills li").each(function(){
      if($(this).hasClass("active")){
        active = $(this).find("a").prop("id");
      }
    });
    
    if(policy.length > 0){
      // Upload Policy
      $.ajax({
        method:"POST",
        data:{uploadJSONPolicyID:active, policy:policy},
        dataType:"json",
        success:function(result){
          console.log("NEW: " + JSON.stringify(result));
        },
        error:function(xhr, status, error){
          console.log(xhr);
          console.log(status);
          console.log(error);
        }
      });
    }
  }

  // Clear Text Area
  function clearArea(){
    $("#policies").text("");
    $(".nav-pills li").each(function(){
      $(this).removeClass("active");
    });
  }

  // Save New Bid to file
  function shellScript(){
    var shell_command = $("#shell_command").val();
    if(cthreshold != ""){
      var data = {action: "shell", shell_command:shell_command};
      // Send new threshold to top of this page
      $.ajax({
        method:"POST",
        type:"text",
        data: data,
        success:function(result){
          console.log(result);
          // window.location="settings.php";
        }, 
        error:function(xhr, status, error){
          console.log("XHR: " + xhr);
          console.log("Status: " + status);
          console.log("Error: " + error);
        }
      });
    }
  }

  // Save New Bid to file
  function saveNewBid(){
    var cthreshold = $("#cthreshold").val();
    var nthreshold = $("#nthreshold").val();
    if(parseInt(cthreshold) == parseInt(nthreshold)){
      var data = {action: "Update", bid: nthreshold};
      // Send new threshold to top of this page
      $.ajax({
        method:"POST",
        type:"text",
        data: data,
        success:function(result){
          window.location="settings.php";
        }, 
        error:function(xhr, status, error){
          console.log("XHR: " + xhr);
          console.log("Status: " + status);
          console.log("Error: " + error);
        }
      });
    }
  }
  </script>

  </body>
</html>
