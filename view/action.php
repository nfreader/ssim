<?php
include '../inc/config.php';
$msg = '';
$user  = new user();
$action = $_GET['action'];
if (!$user->isLoggedIn()){
  switch ($action) {

  case 'register':
      if ($_POST['password'] != $_POST['password-again']) {
        $msg = "Sorry, your passwords must match.";
      } else {
        $msg = $user->register($_POST['username'],$_POST['password'],$_POST['password-again'],$_POST['email']);
      }
      break;
  //Login
  case 'login':
    $msg = $user->logIn($_POST['username'],$_POST['password']);
    break;
  }
} elseif ($user->isLoggedIn()) {
    switch ($action) {

    //Pilot actions
    case 'newPilot':
      $pilot = new pilot();
      $msg = $pilot->newPilot($_POST['firstname'], $_POST['lastname']);
      break;
  
    case 'renameVessel':
      $vessel = new vessel();
      $msg = $vessel->renameVessel($_GET['vesselName']);
      break;
  
    //end pilot actions
    //Spob actions
    case 'refuel':
      $pilot = new pilot();
      $msg = $pilot->refuel();
      break;
    //End spob actions
    
    //Navigation actions
    case 'liftoff':
      $pilot = new pilot();
      $msg = $pilot->liftoff();
      break;
    
    case 'land':
      $pilot = new pilot();
      $msg = $pilot->land($_GET['spob']);
      break;
    
    case 'jump':
      $pilot = new pilot();
      $msg = $pilot->jump($_GET['target']);
      break;
    
    case 'jumpcomplete':
      //Hack because we're not clicking a button here...
      $pilot = new pilot();
      echo "<script>jumpComplete('".$pilot->jumpComplete()."');</script>";
      break;

    case 'buyShip':
      $pilot = new pilot();
      $msg = $pilot->buyShip($_GET['ship']);
      break;
    //End navigation actions
    
    //Beacon space actions
    case 'distressBeacon':
      $beacon = new beacon();
      $msg = $beacon->newDistressBeacon();
      break;
    //End space actions
  
    //Commodity actions
    case 'buyCommod':
      $commod = new commod();
      $msg = $commod->buyCommod($_GET['commod'],floor($_POST['amount']));
      break;
    case 'sellCommod':
      $commod = new commod();
      $msg = $commod->sellCommod($_GET['commod'],floor($_POST['amount']));
      break;
    //End commodity actions
  
    //Message actions 
    case 'sendMsg':
      $message = new message();
      $msg = $message->newPilotMessage($_GET['to'], $_POST['message']);
      break;
    case 'deleteMessage':
      $message = new message();
      $msg = $message->deleteMessage($_GET['msgid']);
      break;
    case 'deleteThread':
      $message = new message();
      $msg = $message->deleteMessageThread($_GET['from']);
      break;
    //End message actions
  
    //Begin mission actions
  
    case 'acceptMission':
      $misn = new misn();
      $msg = $misn->acceptMission($_GET['UID']);
      break;
  
    case 'deliverMission':
      $misn = new misn();
      $msg = $misn->deliverMission($_GET['UID']);
      break;
  
    case 'pirateMission':
      $misn = new misn();
      $msg = $misn->pirateMission($_GET['UID']);
      break;
  
    //End mission actions
  
    case 'addShip':
      $ship = new ship();
      if (!isset($_POST['starter'])) {$_POST['starter'] = 0;}
      $msg = $ship->addShip($_POST);
      break;

    case 'purchaseShip':
      $vessel = new vessel();
      $msg = $vessel->newVessel($_POST['vesselName'],$_POST['regNumber'],$_GET['ship']);
      break;

    case 'test':
      $game = new game();
      $msg = $game->json_test();
      break;

    case 'creditTest':
      $pilot = new pilot(true);
      $msg = $pilot->deductCredits(1);
      break;
      
    //Begin logout action
    case 'logout':
      $msg = $user->logOut();
      break;
    //End logout action
  }
} else {
 $msg = array(
  "message"=>"You must be logged in! This incident has been reported!",
  "level"=>"emergency"
  );
}

if (isset($msg['message'])) {
  $tmp = $msg;
  $msg = '';
  $msg[] = array(
    'message'=>$tmp['message'],
    'level'=>$tmp['level']
  );
}
if (is_array($msg)) {
  $msg[] = array(
    "message"=>"Return arrays are depreciated. Please switch to JSON concatenation.",
    "level"=>0
  );
  echo json_encode(array_reverse($msg),JSON_FORCE_OBJECT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
} elseif (is_string($msg)) {
  echo str_replace('}{','},{',"[$msg]");
}

