<?php
  include_once("../classes/admin.class.php");

  session_start();
  extract($_POST);
  extract($_GET);

  if(isset($_POST["username"]) && isset($_POST["password"])){
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    $user = new admin();
    $match = $user->login($username, $password);
    $login = "";
    if($match != false){
      $_SESSION['username'] = $username;
      $login["valid"] = true;
      // $login["user"] = $match;
    } else{
      $login["valid"] = false;
    } 
    echo json_encode($login);
    return;
  }

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>MTD | Log In</title>
  <?php include_once("../includes/css.php"); ?>
  <style>
    .error{
      background-color:#ff3030;
      margin-top:0;
      padding:10px;
      color:white;
      text-align: center;
    }
  </style>
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <b>MTD</b>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
      <h4 id="login_error" class="error hidden">Username/Password Incorrect</h4>
      <p class="login-box-msg">Sign in to start your session</p>

      <form id="login-form" action="#" method="post">
        <div class="form-group has-feedback">
          <input id="username" type="username" class="form-control" autofocus placeholder="Username">
          <span class="glyphicon glyphicon-user form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
          <input id="password" type="password" class="form-control" placeholder="Password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
          <div class="col-xs-8">
          </div>
          <!-- /.col -->
          <div class="col-xs-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.login-box-body -->
  </div>
  <!-- /.login-box -->

  <!-- JAVASCRIPT -->
  <?php include_once("../includes/js.php"); ?>

  <!-- iCheck -->
  <script src="../plugins/icheck/icheck.min.js"></script>

  <script>
    $("#login-form").submit(function(e){
      e.preventDefault();
      var username = $("#username").val();
      var password = $("#password").val();
      $.ajax({
        data:{username:username, password:password},
        method:"POST",
        dataType:"json",
        success:function(result){
          var valid = result["valid"];
          if(valid){
            window.location="dashboard.php";
          } else {
            $("#login_error").removeClass("hidden");
          }
        },
        error:function(xhr, status, error){
          console.log(JSON.stringify(xhr) + " | " + JSON.stringify(status) + " | " + JSON.stringify(error));
        }
      });
    });
  </script>
</body>
</html>
