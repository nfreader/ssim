<?php
include '../inc/config.php';

$user  = new user();
$pilot = new pilot();

$spob = new spob($pilot->pilot->spob);
$syst = new syst($pilot->pilot->syst);

$action = $_GET['action'];

if ($action == 'newPilot') {
  returnMsg($pilot->newPilot($_GET['firstname'], $_GET['lastname']));
}

if ($action === 'refuel') {
  $msg = urlencode($pilot->refuel());
  directLoad('view/home.php?msg='.$msg);
}

