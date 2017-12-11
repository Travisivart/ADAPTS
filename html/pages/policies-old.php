<?php
include_once("../classes/utilities.class.php"); 
include_once("../classes/policies.class.php");
include_once("../classes/devices.class.php");

extract($_POST);
error_reporting(1);

// Update threshold value if sent from jquery
if(isset($_POST["action"])){ //check for action
  $action = $_POST["action"];
  if(strcasecmp($action, "bid")==0){
    $file = fopen("../data/bid_value.txt","w");
    $bid = $_POST["bid"];
    fwrite($file, $bid);
    fclose($file);
  } else if(strcasecmp($action, "shell")==0){
    // nothig right now
  } else if(strcasecmp($action, "updatePolicyTable")==0){
    $policy = $_POST["policies"];
    $policies = new policies();
    $insertPolicy = $policies->setPolicy($policy);
    echo $insertPolicy;
  } else if(strcasecmp($action, "updateTable")==0){
    $policies = new policies();
    $getPolicies = $policies->getAllPolicy();
    echo json_encode($getPolicies);
  }
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
    <title>MTD | Policies</title>
    
    <?php include_once("../includes/css.php"); ?>
    <link href="../plugins/datatables/css/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="../plugins/vis-js/dist/vis-network.min.css" rel="stylesheet" />

    <style>
    .info-box-number{
      font-size:32px;
    }
    .nav-pills > li{
      background-color:#eee;
    }
    #mynetwork {
      width: 100%;
      height: 400px;
      border: 1px solid lightgray;
    }

    #policyTableBody{
      max-height:400px;
      overflow-y:auto;
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
        <!-- POLICY TABLE -->
        <div class="row">
          <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
              <div class="box-header with-border">
                <div class="row">
                  <div class="col-md-6 text-left" style="padding-top:5px">
                    <h3 class="box-title">Policy Table</h3>
                  </div>
                  <div class="col-md-6 text-right">
                    <button class="btn btn-primary" onclick="addNewPolicy()">Add New Policy</button>
                  </div>
                </div>
              </div>
              <!-- /.box-header -->
              <h4 id="update_success" class='text-center hidden' style="background-color:#00a65a; margin-top:0; padding:10px; color:white">Successfully Updated</h4>
              <h4 id="update_fail" class='text-center hidden' style="background-color:#e44343; margin-top:0; padding:10px; color:white">Failed to Update</h4>

              <!-- form start -->
              <div id="policyTableBody" class="box-body">
                <table id="policyTable" class="table table-responsive table-striped">
                  <thead>
                    <tr>
                      <th>Device</th>
                      <th width="65%">Filter Policies</th>
                      <th width="5%">Loaded</th>
                      <th width="5%">Remove</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $devices = new devices();
                    $deviceList = $devices->getAllDevices();

                    $policies = new policies();
                    $policyList = $policies->getAllPolicies();
                    if(count($policyList)>0){
                      for ($i=0; $i<count($policyList); $i++){
                        $policy = $policyList[$i];
                        $devID = $policy["deviceID"];
                        $polID = $policy["policyID"];
                        $pols = $policy["policy"];
                        $loaded = $policy["loaded"];

                        echo "<tr policy-id='".$polID."'><td>";
                        echo "<select class='form-control device'>";
                        echo "<option value='na'>Select One</option>";
                        for($j=0; $j<count($deviceList);$j++){
                          $device = $deviceList[$j];
                          $id = $device["deviceID"];
                          $name = $device["name"];
                          $ip = $device["ipv4"];
                          $mac = $device["MAC"];
                          if($devID == $id){
                            echo "<option value='".$id."' selected>".$name." (".$ip.")"."</option>";
                          } else {
                            echo "<option value='".$id."'>".$name."(".$ip.")"."</option>";
                          }
                        }
                        echo "</select></td>";
                        echo "<td><textarea class='form-control policy' rows='4' placeholder='Enter Filter Policies'>".$pols."</textarea></td>";
                        echo "<td class='text-center loaded'>".$loaded."</td>";
                        echo "<td><button class='btn btn-danger' onclick='removePolicy($(this))'><span class='fa fa-times'></span></button></td>";
                        echo "</tr>";
                      }
                    } else {
                      $utilities = new utilities();
                      $polID = strtoupper($utilities->randomNumber());
                      echo "<tr policy-id='".$polID."'><td>";
                      echo "<select class='form-control device'>";
                      echo "<option value='na'>Select One</option>";
                      
                      for($i=0; $i<count($deviceList);$i++){
                        $device = $deviceList[$i];
                        echo "<option value='".$device["deviceID"]."'>".$device["name"]."</option>";
                      }
                      echo "</select></td>";
                      echo "<td><textarea class='form-control policy' placeholder='Enter Filter Policies' rows='4'></textarea></td>";
                      echo "<td class='text-center loaded'>0</td>";
                      echo "<td><button class='btn btn-danger' onclick='removePolicy($(this))'><span class='fa fa-times'></span></button></td></tr>";
                    }
                    ?>
                    <!-- JQUERY -->
                  </tbody>
                </table>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="button" onclick="updatePolicy()" class="btn btn-success pull-right">Update Policy</button>
                <!-- <button type="button" onclick="insertNetKAT()" class="btn btn-success pull-right">Insert NetKAT</button> -->
              </div>
              <!-- /.box-footer -->
            </div>
            <!-- /.box -->
          </div>
          <!-- /.col-md-12 -->
        </div>
        <!-- END POLICY TABLE-->

        <!-- NETKAT -->
        <div class="row">
          <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Send Policy Code</h3>
              </div>
              <!-- /.box-header -->
              <!-- form start -->
              <h4 id="insert_success" class="hidden" style="background-color:#00a65a; margin-top:0; padding:10px; color:white">Successfully Inserted</h4>
              <h4 id="insert_fail" class="hidden" style="background-color:#dd4b39; margin-top:0; padding:10px; color:white">Unable to Insert</h4>
              <div class="box-body">
                <textarea id="netkat" rows="10" placeholder=" Enter NetKAT" style="width:100%; margin-top:10px"></textarea>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="button" onclick="clearArea('netkat')" class="btn btn-default">Clear</button>
                <button type="button" onclick="insertNetKAT()" class="btn btn-success pull-right">Insert NetKAT</button>
              </div>
              <!-- /.box-footer -->
            </div>
            <!-- /.box -->
          </div>
          <!-- /.col-md-12 -->
        </div>
        <!-- END NETKAT-->

        <!-- network graph info -->
        <div class="row">
          <div class="col-xs-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <i class="fa fa-line-chart"></i>
                <h3 class="box-title">Network Graph</h3>
              </div>
              <div class="box-body">
                <div class="col-xs-12 col-md-8">
                  <div id="mynetwork"></div>
                </div>
                <div id="network-info-box" class="col-xs-12 col-md-4">
                  <table id="network-info" class="table table-responsive table-bordered">
                    <thead>
                      <tr>
                        <th>No Data</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>Found !</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- THRESHOLD -->
       <!--  <div class="row">
          <div class="col-md-12">
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Set Threshold</h3>
              </div>
              <form class="form-horizontal" action="#">
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
                <div class="box-footer">
                  <button type="button" class="btn btn-default">Cancel</button>
                  <button type="button" onclick="saveNewBid();" class="btn btn-success pull-right">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div> -->
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

  <script src="../plugins/vis-js/dist/vis-network.min.js"></script>
  <script src="../resources/js/network-diagram.js"></script>
  <script>
    $(document).ready(function(){
      draw(); // js/network-diagram.js
    });
    
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
      $("#policies").val("");
      $.getJSON("../js/json/"+filename+".json",function(data){
        $("#policies").val("");
        $("#policies").val(JSON.stringify(data));
      });
    }

    // Upload JSON policies
    function uploadJSONPolicies(){  
      var active = 0;
      var policy = $("#policies").val();

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
            // console.log(JSON.stringify(result));
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
    function clearArea(textarea){
      $("#"+textarea).val("");
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
            // console.log(result);
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
        var data = {action: "bid", bid: nthreshold};
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

    // Insert NetKAT to database
    function insertNetKAT(){
      var netkat = $("#netkat").val();
      if(netkat.length > 0){
        var data = {action:"netkat", netkat: netkat};
        // Send new threshold to top of this page
        $.ajax({
          method:"POST",
          type:"text",
          data: data,
          success:function(result){
            // window.location="settings.php";
            // console.log(result);
            if(result == 1){
              $("#insert_success").removeClass("hidden");
              $("#insert_fail").addClass("hidden");
            } else {
              $("#insert_success").addClass("hidden");
              $("#insert_fail").removeClass("hidden");
            }
          }, 
          error:function(xhr, status, error){
            console.log("XHR: " + xhr);
            console.log("Status: " + status);
            console.log("Error: " + error);
          }
        }); 
      }
    }

    // Add New Policy Row
    function addNewPolicy(){
      var tbody_length = $("#policyTable tbody tr").length;
      var newRow = "";
      if(tbody_length > 0){
        newRow = $("#policyTable tbody tr:first").html();
        var guid = uuidv4().toUpperCase();
        $("#policyTable tbody").append("<tr policy-id='"+guid+"'>");
        $("#policyTable tbody tr:last").append(newRow);
        $("#policyTable tbody").append("</tr>");

        cleanLastRow();
      } else{
        var deviceList = "";
        $.ajax({
          url:"../ajax-handlers/device-handler.php",
          method:"POST",
          data:{action:"getDeviceList"},
          dataType:"json",
          success:function(array){
            var deviceList = array["deviceList"];
            var policyID = array["policyID"];
            policyID = policyID.toUpperCase();
            var device = "";
            var row = "<tr policy-id='"+policyID+"'><td><select class='form-control'><option value='na'>Select One</option>";
            for(var i=0; i < deviceList.length; i++){
              device = deviceList[i];
              row += "<option value='" + device["deviceID"] + "'>" + device["name"] + "</option>";
            }
            row += "</select></td><td><textarea class='form-control policy' placeholder='Enter Filter Policies' rows='4'></textarea></td><td><button class='btn btn-danger' onclick='removePolicy($(this))'><span class='fa fa-times'></span></button></td></tr>";
            $("#policyTable tbody").append(row);
          }, 
          error:function(xhr, status, error){
            console.log("XHR: " + xhr);
            console.log("Status: " + status);
            console.log("Error: " + error);
          }
        });
      }
    }

    // Generate Global UID
    function uuidv4() {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
      });
    }

    function cleanLastRow(){
      var row = $("#policyTable tbody tr:last");
      row.find(".device").val("na");
      row.find(".policy").val("");
      row.find(".loaded").text("0");
    }

    // Delete Row
    function removePolicy(row){
      var tbody_length = $("#policyTable tbody tr").length;
      if(tbody_length == 1){
        return;
      } else{
        row.parents("tr").remove();
      }
    }

    // Update policy to database
    function updatePolicy(){
      var policies = [];
      var policy = {};
      var error = 0;
      $("#policyTable tbody tr").each(function(){
        if(error == 0){
          policy = {};
          var polID = $(this).attr("policy-id");
          var device = $(this).find(".device option:selected").val();
          var pols = $(this).find(".policy").val();
          var loaded = $(this).find("");

          // Validate policy
          if(pols != "" && (!pols.includes("Filter(SwitchEq(") || !pols.includes("SetPort("))){
            alert("Invalid policy");
            error = 1;
            // return;
          } else if (device == "na"){
            alert("Please select a valid device");
            error = 1;
            // return;
          } else {
            policy["policyID"] = polID;
            policy["deviceID"] = device;
            policy["policyValue"] = pols;
            policy["loaded"] = loaded;
            policies.push(policy);
          }
        } // end if
      });

      // alert(JSON.stringify(policies));
      // return;

      if(error == 0){
        $.ajax({
          data:{action:"updatePolicyTable", policies:policies},
          method:"POST",
          dataType:"json",
          success:function(result){
            // console.log(result);
            console.log(JSON.stringify(result));
            // return;
            if(result){
              $("#update_success").removeClass("hidden");
              $("#update_fail").addClass("hidden");
            }else{
              $("#update_success").addClass("hidden");
              $("#update_fail").removeClass("hidden");
            }
          },
          error:function(xhr, status, error){
            console.log("XHR: " + JSON.stringify(xhr));
            console.log("Status: " + JSON.stringify(status));
            console.log("Error: " + JSON.stringify(error));
          }
        });
      }
    }

    // Load Database
    // setInterval(function(){
      /*$.ajax({
        data:{action:"updateTable", updateTable:"update"},
        dataType:"json",
        method:"POST",
        success:function(result){
          var policy = "";
          $("#policyTable tbody").empty();
          if(result.length > 0){
            for(var i=0; i<result.length; i++){
              policy = result[i];
              $("#policyTable tbody").append("<tr><td>"+policy["rule"]+"</td><td>"+policy["loaded"]+"</td></tr>");
            }
          } else {
            $("#policyTable tbody").append("<tr><td colspan='2'>No rule</td></tr>");
          }
        },
        error:function(xhr, status, error){
          console.log("XHR: " + xhr);
          console.log("Status: " + status);
          console.log("Error: " + error);
        }
      });*/
    // },1000);
  </script>

</body>
</html>
