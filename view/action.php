<?php
include '../inc/config.php';
$msg = '';
$user  = new user();
if ($user->isLoggedIn()) {
  
  $action = $_GET['action'];
    switch ($action) {
    //Pilot actions
    case 'newPilot':
      $pilot = new pilot();
      $msg = $pilot->newPilot($_POST['firstname'], $_POST['lastname']);
      break;
  
    case 'renameVessel':
      $pilot = new pilot();
      $msg = $pilot->renameVessel($_GET['vesselName']);
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
      if (!methodRequires(
        "name,shipwright,cost,class,mass,accel,turn,fuel,cargo"
        ."expansion,armor,shields",$_POST
      )) {
        $msg = returnError("Data format invalid.");
      }
      $msg = $ship->addShip(
        $_POST['name'],
        $_POST['shipwright'],
        $_POST['cost'],
        $_POST['class'],
        $_POST['mass'],
        $_POST['accel'],
        $_POST['turn'],
        $_POST['fuel'],
        $_POST['cargo'],
        $_POST['expansion'],
        $_POST['armor'],
        $_POST['shields']
      );
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



if(is_string($msg)) {
  $string['message'] = $msg ." (PS I need to be an array!)";
  $string['level'] = 'normal';
  echo "[".json_encode($string, JSON_FORCE_OBJECT)."]";
} elseif(isset($msg['message'])) {
  $tmp = $msg;
  $msg = '';
  $msg[] = array(
    'message'=>$tmp['message'],
    'level'=>$tmp['level']
  );
} else {
  echo json_encode($msg, JSON_FORCE_OBJECT);
}

