<?php
header('Content-Type: application/json');
require_once('../../inc/config.php');

$pilot = new pilot(FALSE);
$ping = $pilot->ping($_SESSION['pilotuid']);
$data = new stdClass;
$data->timestamp = time();
$data->ping = $ping;

echo json_encode($data);
