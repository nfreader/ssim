<?php

class beacon {
  public function getBeacons($syst) {
    $this->beaconCleanUp();
    $db = new database();
    $db->query("SELECT tbl_beacon.*,
    (ADDDATE(tbl_beacon.timestamp, INTERVAL 1 WEEK)) AS expires,
    tbl_pilot.name
    FROM tbl_beacon
    LEFT JOIN tbl_pilot ON tbl_beacon.placedby = tbl_pilot.uid
    WHERE tbl_beacon.syst = ?");
    $db->bind(1,$syst);
    $db->execute();
    return $db->resultSet();
  }

  public function beaconCleanUp() {
    $db = new database();
    $db->query("DELETE FROM tbl_beacon
      WHERE tbl_beacon.timestamp < SUBDATE(NOW(), INTERVAL 1 WEEK)
      AND tbl_beacon.type = 'D';");
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
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single()->beacons;
  }

  public function newBeacon($content) {
    $pilot = new pilot(null,FALSE);
    if (!$pilot->vessel->beacons->can) {
      return returnError("Unable to launch beacons");
    }
    if (!$pilot->flags->inSpace) {
      return returnError("You must be in space to launch a beacon");
    }
    $db = new database();
    $db->query("INSERT INTO tbl_beacon (placedby, type, content, syst)
    VALUES (?,'R',?,?)");
    $db->bind(1, $pilot->uid);
    $db->bind(2,$content);
    $db->bind(3, $pilot->syst);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $return = $pilot->subtractBeacon();
    $return.= returnSuccess("Beacon launched!");
    return $return;
  }

  public function newDistressBeacon() {
    $this->beaconCleanUp();
    $pilot = new pilot(NULL);
    if($this->hasDistressBeacon($pilot->uid,
      $pilot->syst)) {
      return returnError("Unable to launch more beacons.");
    } else {
      $msg = "Pan-pan, pan-pan, pan-pan, this is <em> ".$pilot->vessel->name."</em> transmitting on the blind guard. I am out of fuel in the $pilot->systname system and require immediate assistance. Any vessel receiving, please respond.";
      $db = new database();
      $db->query("INSERT INTO tbl_beacon (placedby, syst, content, `type`)
        VALUES (:placedby, :syst, :content, 'D')");
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

  public function newAdminBeacon($syst, $content){
    $db = new database();
    $db->query("INSERT INTO tbl_beacon (syst, content, `type`) VALUES
    (?, ?, 'A')");
    $db->bind(1,$syst);
    $db->bind(2,$content);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $game = new game();
    $game->logEvent('AB',"Launched admin beacon at $syst");
    return returnSuccess("Admin beacon deployed");
  }
}
