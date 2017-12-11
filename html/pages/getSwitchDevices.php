<?php

include_once("../classes/switchDevice.class.php");

$switchDevices = new switchDevice();

echo json_encode($switchDevices->getAllSwitchDevice());

?>