<?php

class game {
  public function logEvent($what, $data) {
    $db = new database();
    $db->query("INSERT INTO ssim_log (who, what, timestamp, data)
      VALUES (:who, :what, NOW(), :data)");
    $user = new user();
    $db->bind(':who',$_SESSION['userid']);
    $db->bind(':what',$what);
    $db->bind(':data',json_encode($data));
    $db->execute();
  }
}