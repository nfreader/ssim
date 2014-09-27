<?php 

class beacon {
  public function getBeacons($syst) {
    $db = new database();
    $db->query("SELECT ssim_beacon.*,
    ssim_pilot.name
    FROM ssim_beacon
    LEFT JOIN ssim_pilot ON ssim_beacon.placedby = ssim_pilot.id
    WHERE ssim_beacon.syst = :syst
    AND ssim_beacon.timestamp > ADDDATE(NOW(), INTERVAL -1 WEEK)");
    $db->bind(':syst',$syst);
    $db->execute();
    return $db->resultSet();
  }

  public function hasDistressBeacon($pilot,$syst) {
    $db = new database();
    $db->query("SELECT COUNT(*) AS beacons FROM ssim_beacon
      WHERE placedby = :pilot AND syst = :syst AND type = 'D'");
    $db->bind(':pilot',$pilot);
    $db->bind(':syst',$syst);
    $db->execute();
    return $db->single();
  }

  public function newDistressBeacon() {
    //Mayday mayday mayday this is Firstname Lastname. I am stranded in the 
    //X system with no fuel. I require immediate aid.
    $pilot = new pilot();
    $syst = new syst($pilot->pilot->syst);
    $beacons = $this->hasDistressBeacon($pilot->pilot->id,
      $pilot->pilot->syst);
    if( $beacons->beacons > 0) {
      return "Unable to launch more beacons.";
    } else {
      $msg = "Mayday mayday mayday this is ".$pilot->pilot->name.". I am  stranded in the ".$syst->syst->name." system with no fuel. I require   immediate aid.";
      //Determine neighbors
      $neighbors = $syst->getConnections($syst->syst->id);
      $db = new database();
      $db->query("INSERT INTO ssim_beacon (placedby, syst, content, type,   timestamp)
        VALUES (:placedby, :syst, :content, 'D', NOW())");
      $i=0;
      //Place one in the current system
      $db->bind(':placedby',$pilot->pilot->id);
      $db->bind(':syst',$pilot->pilot->syst);
      $db->bind(':content',$msg);
      if ($db->execute()){
        $i++;
      }
      //And then place one in all the neighboring systems    
      foreach ($neighbors as $neighbor) {
        $db->bind(':placedby',$pilot->pilot->id);
        $db->bind(':syst',$neighbor->id);
        $db->bind(':content',$msg);
        if ($db->execute()){
          $i++;
        }
      }
      $game = new game();
      $game->logEvent('DB',$i." distress beacons have been luanched.");
      return $i." distress beacons have been luanched.";
    }
  }
}