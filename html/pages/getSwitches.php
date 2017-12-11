<?php

include_once("../classes/switches.class.php");

$switches = new switches();

echo json_encode($switches->getAllSwitches());

?>