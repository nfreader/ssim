<?php

class beacon {

  public $id;
  public $placedby;
  public $type;
  public $content;
  public $syst;
  public $timestamp;
  public $expires;
  public $pilot;
  public $system;

  public $fullType;
  public $css;
  public $icon;
  public $header;

  public $html;

  public function __construct($id=null){
    if ($id){
      $beacon = $this->getBeacon($id);
      $beacon = $this->parseBeacon($beacon);
      foreach ($beacon as $key => $value){
        $this->$key = $value;
      }
      return $beacon;
    }
  }

  public function parseBeacon(&$beacon){
    $type = $this->beaconType($beacon->type);
    $beacon->fullType = $type['text'];
    $beacon->css = $type['class'];
    $beacon->icon = $type['icon'];
    $beacon->header = $type['header'];

    if ($beacon->type == 'D' || $beacon->type == 'R'){
    $beacon->footer = "<small>Placed by $beacon->pilot";
    $beacon->footer.= $beacon->type=='D'?", Beacon expires ". timestamp($beacon->expires):'';
    $beacon->footer.= "</small>";
    } else {
      $beacon->footer = false;
    }

    $beacon->html = "<div class='beacon $beacon->css' id='beacon-$beacon->id'>";
    $beacon->html.= "<div class='beacon-header'><h3>$beacon->header</h3></div>";
    $beacon->html.= "<div class='beacon-body'>$beacon->content</div>";
    if ($beacon->footer){
      $beacon->html.= "<div class='beacon-footer'>$beacon->footer</div>";
    }
    $beacon->html.= "</div>";

    return $beacon;
  }

  public function beaconType($type){
    $data = array();
    switch ($type) {
      default:
      case 'R':
      $data['class']='regular';
      $data['text']='Regular';
      $data['icon']='';
      $data['header'] = 'Message Beacon';
      break;

      case 'D':
      $data['class']='distress';
      $data['text']='Distress';
      $data['icon']='exclamation-triangle';
      $data['header']=icon($data['icon'],'panic-icon').'Distress Beacon';
      break;

      case 'A':
      $data['class']='admin';
      $data['text']='Important Notice';
      $data['icon']='';
      $data['header']='Automated Message Beacon';
      break;
    }
    return $data;
  }

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

  public function getSystemBeacons($syst) {
    $this->beaconCleanUp();
    $db = new database();
    $db->query("SELECT tbl_beacon.*,
    (ADDDATE(tbl_beacon.timestamp, INTERVAL 1 WEEK)) AS expires,
    tbl_pilot.name AS pilot
    FROM tbl_beacon
    LEFT JOIN tbl_pilot ON tbl_beacon.placedby = tbl_pilot.uid
    WHERE tbl_beacon.syst = ?");
    $db->bind(1,$syst);
    $db->execute();
    $beacons = $db->resultSet();
    foreach ($beacons as &$beacon) {
      $beacon = $this->parseBeacon($beacon);
    }
    return $beacons;
  }

  public function getBeacon($id) {
    $db = new database();
    $db->query("SELECT tbl_beacon.*,
      (ADDDATE(tbl_beacon.timestamp, INTERVAL 1 WEEK)) AS expires,
      tbl_pilot.name AS pilot,
      tbl_syst.name AS system
      FROM tbl_beacon
      LEFT JOIN tbl_pilot ON tbl_beacon.placedby = tbl_pilot.uid
      LEFT JOIN tbl_syst ON tbl_beacon.syst = tbl_syst.id
      WHERE tbl_beacon.id = ?");
    $db->bind(1, $id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
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

  public function deleteBeacon() {
    $db = new database();
    $db->query("DELETE FROM tbl_beacon WHERE id = ?");
    $db->bind(1,$this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $game = new game();
    $game->logEvent('BD',"Deleted $this->pilot's beacon at $this->system");
    return returnSuccess("Beacon deleted");
  }

  public function editBeacon($text=null) {
    if (empty($text)){
      return $this->deleteBeacon();
    }
    $db = new database();
    $db->query("UPDATE tbl_beacon SET content = ? WHERE tbl_beacon.id = ?");
    $db->bind(1, $text);
    $db->bind(2, $this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $game = new game();
    $game->logEvent('BE',"Edited $this->pilot's beacon at $this->system");
    return returnSuccess("Beacon edited");
  }
}
