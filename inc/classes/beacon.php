<?php 

class beacon {
  public function getBeacons($syst) {
    $db = new database();
    $db->query("SELECT * FROM ssim_beacon WHERE syst = :syst");
    $db->bind(':syst',$syst);
    $db->execute();
    return $db->resultSet();
  }
}