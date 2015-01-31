<?php
session_start();
require_once("sfconf.php");
require_once("sfcore.php");

$url = new Engine(DEFAULT_CONTROLLER,SEGMENT_START,$_SERVER['REQUEST_URI']);
$url->include_controller();
?>

