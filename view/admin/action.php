<?php
require_once('../../inc/config.php');
$user = new user();
if (!$user->isAdmin()) {
  die("You must be an administrator to access this page");
}

$msg = '';

if (isset($_GET['action'])){
  switch ($_GET['action']) {
    default:
      http_response_code(501);
      echo "Action not specified: ".$_GET['action'];
    break;

    case 'changeCredits':
      $pilot = new pilot($_POST['pk']);
      $msg = $pilot->modifyField('credits',$_POST['value']);
    break;
    case 'changeLegal':
      $pilot = new pilot($_POST['pk']);
      $msg = $pilot->modifyField('legal',$_POST['value']);
    break;
    case 'editBeacon':
      $beacon = new beacon($_POST['pk']);
      $msg = $beacon->editBeacon($_POST['value']);
    break;
    case 'deleteBeacon':
      $beacon = new beacon($_GET['beacon']);
      $msg = $beacon->deleteBeacon();

    $msg.=json_encode("You forgot the break!");
    break;
  }
}
echo str_replace('}{','},{',"[$msg]");
