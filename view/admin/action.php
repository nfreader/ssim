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
  }
}
echo str_replace('}{','},{',"[$msg]");
