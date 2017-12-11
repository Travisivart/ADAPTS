<?php
  include_once("../classes/utilities.class.php");
  include_once("../classes/servers.class.php");
  include_once("../classes/qvm.class.php");
  include_once("../classes/users.class.php");
  include_once("../classes/userMigration.class.php");

  $utilities = new utilities();
  $bidvalue = floatval(file_get_contents("../data/bid_value.txt"));

  // Checks if username is in session, which lasts until browser is closed. Have to login again if closed
  session_start();
  if(!isset($_SESSION["username"])){
    header("location:../index.php");
  }

?>
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="treeview">
          <a href="../pages/dashboard.php">
            <i class="fa fa-home"></i>
            <span>Home</span>
          </a>
        </li>
        <li class="header">MAIN NAVIGATION</li>
        <li class="treeview">
          <a href="../pages/servers.php">
            <i class="fa fa-laptop"></i>
            <span>All Servers</span>
            <span class="pull-right-container">
              <?php 
                // Servers
                $serverClass = new servers();
                $servers = $serverClass->getAllServers();
                $serverStatus = 0;
                for($i=0; $i<count($servers); $i++){
                  $server = $servers[$i];
                  if($server["bidValue"] <= $bidvalue){
                    $serverStatus++;
                  }
                }
                // Show if servers exist
                if($serverStatus > 0){
                  echo ("<span class='label label-danger pull-right'>".$serverStatus."</span>");
                }
              ?>
            </span> 
          </a>
        </li>
        <li class="treeview">
          <a href="../pages/users.php">
            <i class="fa fa-user"></i>
            <span>All Connected Users</span>
            <?php
              // USERS
              $users = new users();
              $allUsers = $users->getAllUsers();
            ?>
            <span class="pull-right-container">
              <?php 
                if(count($users)<=200){
                  echo ("<span class='label label-success pull-right'>".count($allUsers)."</span>");
                } else {
                  echo ("<span class='label label-danger pull-right'>".count($allUsers)."</span>");
                }
              ?>
            </span>
          </a>
        </li>
        <li class="treeview">
          <a href="../pages/user-migration.php">
            <i class="fa fa-users"></i>
            <span>User Migration</span>
            <?php
              // USERS
              $um = new userMigration();
              $allMigrations = $um->getAllUserMigrations();
            ?>
            <span class="pull-right-container">
              <?php 
                echo ("<span class='label label-primary pull-right'>".count($allMigrations)."</span>");
              ?>
            </span>
          </a>
        </li>
        <!-- <li class="treeview">
          <a href="../pages/attack_history.php">
            <i class="fa fa-table"></i>
            <span>Attack History</span>
          </a>
        </li> -->
        <li class="treeview">
          <a href="../pages/qvm.php">
            <i class="fa fa-cloud"></i>
            <?php 
              // QVMs
              $qvmClass = new qvm();
              $qvms = $qvmClass->getAllQVMs();
            ?>
            <span>Quarantined VMs</span>
            <span class="pull-right-container">
              <?php 
                if(count($qvms)<200){
                  echo ("<span class='label label-primary pull-right'>".count($qvms)."</span>");
                } else {
                  echo ("<span class='label label-danger pull-right'>".count($qvms)."</span>");
                }
              ?>
            </span>
          </a>
        </li>
        <li class="treeview">
          <a href="../pages/blacklist.php">
            <i class="fa fa-ban"></i>
            <span>Blacklisted IPs</span>
          </a>
        </li>
        <li class="treeview">
          <a href="../pages/attack-history.php">
            <i class="fa fa-history"></i>
            <span>Attack History</span>
          </a>
        </li>
        <li class="treeview">
          <a href="../pages/suspicious.php">
            <i class="fa fa-search-plus"></i>
            <span>Internal Suspiciousness Scores</span>
          </a>
        </li>
        <li class="treeview">
          <a href="../pages/switches.php">
            <i class="fa fa-feed"></i>
            <span>Switches And Devices</span>
          </a>
        </li>
        <li class="treeview">
          <a href="../pages/policies.php">
            <i class="fa fa-file-text"></i>
            <span>Policies</span>
          </a>
        </li>
        <li class="header"></li>
        <li class="treeview">
          <a href="../pages/logout.php">
            <i class="fa fa-sign-out"></i>
            <span>Logout</span>
          </a>
        </li>
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>