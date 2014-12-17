<?php 

require_once('../inc/config.php');

$pilot = new pilot(true, true);
$db = new database();
$db->query("SELECT * FROM ssim_heartbeat WHERE pilot = :pilot");
$db->bind(':pilot',$pilot->pilot->id);
$db->execute();
$return = $db->single();
if($return == '') {
} else {
  $db->query("DELETE FROM ssim_heartbeat WHERE pilot = :pilot");
  $db->bind(':pilot',$return->pilot);
  //$db->execute();

  //There's some data we need up update.
  $db->query("SELECT * FROM ssim_notify
    WHERE pilot = :pilot");
  $db->bind(':pilot',$return->pilot);
  $db->execute();
  $data = $db->resultSet();
  foreach($data as $notification) {
    $notify[] = array(
      'message' => $notification->message,
      'level' => $notification->level,
      'newvalue' => $notification->newvalue,
      'data' => $notification->data
    );
  }

  //And delete
  $db->query("DELETE FROM ssim_notify WHERE pilot = :pilot");
  $db->bind(':pilot',$return->pilot);
  //$db->execute();

  echo json_encode($notify);
}