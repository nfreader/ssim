<?php

class beacon {
  public function getBeacons($syst) {
    $this->beaconCleanUp();
    $db = new database();
    $db->query("SELECT ssim_beacon.*,
    (ADDDATE(ssim_beacon.timestamp, INTERVAL 1 WEEK)) AS expires,
    ssim_pilot.name
    FROM ssim_beacon
    LEFT JOIN ssim_pilot ON ssim_beacon.placedby = ssim_pilot.uid
    WHERE ssim_beacon.syst = ?");
    $db->bind(1,$syst);
    $db->execute();
    return $db->resultSet();
  }

  public function beaconCleanUp() {
    $db = new database();
    $db->query("DELETE FROM tbl_beacon
      WHERE tbl_beacon.timestamp < ADDDATE(NOW(), INTERVAL 1 WEEK)
      AND tbl_beacon.type = 'D'");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function hasDistressBeacon($pilot,$syst) {
    $db = new database();
    $db->query("SELECT COUNT(*) AS beacons FROM tbl_beacon
      WHERE placedby = ? AND syst = ? AND type = 'D'");
    $db->bind(1,$pilot);
    $db->bind(2,$syst);
    $db->execute();
    return $db->single();
  }

  public function newDistressBeacon() {
    $this->beaconCleanUp();
    $pilot = new pilot();
    $beacons = $this->hasDistressBeacon($pilot->uid,
      $pilot->syst);
    if( $beacons->beacons > 0) {
      return returnError("Unable to launch more beacons.");
    } else {
      $msg = "Pan-pan, pan-pan, pan-pan, this is <em>BSV ".$pilot->vessel->name."</em> transmitting on the blind guard. I am out of fuel in the $pilot->systname system and require immediate assistance. Any vessel receving, please respond.";
      $db = new database();
      $db->query("INSERT INTO ssim_beacon (placedby, syst, content, type,   timestamp)
        VALUES (:placedby, :syst, :content, 'D', NOW())");
      //Place one in the current system
      $db->bind(':placedby',$pilot->uid);
      $db->bind(':syst',$pilot->syst);
      $db->bind(':content',$msg);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      $game = new game();
      $game->logEvent('DB',"Launched distress beacon at $pilot->systname ($pilot->syst)");
      return returnSuccess("Distress beacon deployed");
    }
  }
}
