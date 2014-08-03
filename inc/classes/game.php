<?php

class game {
  public function logEvent($what, $data) {
    $db = new database();
    $db->query("INSERT INTO ssim_log (who, what, timestamp, data)
      VALUES (:who, :what, NOW(), :data)");
    $user = new user();
    $db->bind(':who',$_SESSION['userid']);
    $db->bind(':what',$what);
    $db->bind(':data',$data);
    $db->execute();
  }

  public function getLogs($offset=0,$perpage=30) {
    $db = new database();
    $db->query("SELECT ssim_log.*,
        ssim_user.username,
        ssim_pilot.name
        FROM ssim_log
        LEFT JOIN ssim_user ON ssim_log.who = ssim_user.id
        LEFT JOIN ssim_pilot ON ssim_user.id = ssim_pilot.user
        ORDER BY timestamp DESC
        LIMIT $offset,$perpage");
    $db->execute();
    return $db->resultset();
  }
}