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

if ($action === 'liftoff') {
  $msg = urlencode($pilot->liftoff());
  directLoad('view/home.php?msg='.$msg);
}

if ($action === 'land') {
  $msg = urlencode($pilot->land($_GET['spob']));
  directLoad('view/home.php?msg='.$msg);
}

if ($action === 'jump'){
  $msg = urlencode($pilot->jump($_GET['target']));
  directLoad('view/home.php?msg='.$msg);
}

if ($action === 'jumpcomplete'){
  $msg = urlencode($pilot->jumpComplete());
  directLoad('view/home.php?msg='.$msg);
}
