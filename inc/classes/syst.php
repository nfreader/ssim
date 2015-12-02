<?php

class syst {
  public $id;
  public $name;
  public $coord_x;
  public $coord_y;

  public $coords;
  public $fingerprint;

  public $govt;
  public $spobs;
  public $beacons;

  public $connections;


  public function __construct($id=null,$short=FALSE) {
    if (isset($id)){
      $syst = $this->getSyst($id);
      $this->id = $syst->id;
      $this->name = $syst->name;
      $this->coord_x = $syst->coord_x;
      $this->coord_y = $syst->coord_y;
      $this->govt = new stdclass();
      $this->govt->name = $syst->govtname;
      $this->govt->color1 = $syst->color1;
      $this->govt->color2 = $syst->color2;
      $this->govt->iso = $syst->isoname;
      $this->govt->id = $syst->govt;
      $this->coords = "($this->coord_x,$this->coord_y)";
      $this->fingerprint = hexPrint($syst->name.$syst->coord_x.$syst->coord_y);
      if (FALSE === $short){
        $spob = new spob();
        $this->spobs = $spob->getSystemSpobs($syst->id);
        $beacons = new beacon();
        $this->beacons = $beacons->getBeacons($syst->id);
        $this->connections = $this->getConnections($this->id);
      }
    }
  }

  public function getSyst($id) {
    $db = new database();
    $db->query("SELECT tbl_syst.*,
    tbl_govt.name AS govtname,
    tbl_govt.color1 AS color1,
    tbl_govt.color2 AS color2,
    tbl_govt.isoname
    FROM tbl_syst
    LEFT JOIN tbl_govt ON tbl_syst.govt = tbl_govt.id
    WHERE tbl_syst.id = ?");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function addSyst($name, $coordx, $coordy, $govt=null, $id=null) {
    $db = new database();
    $db->query("INSERT INTO ssim_syst (name, coord_x, coord_y, govt, id) VALUES
    (:name, :coordx, :coordy, :govt, :id)");
    if (empty($name)
      || empty($coordx)
      || empty($coordy)) {
      return false;
    } else {
      $db->bind(':name',$name);
      $db->bind(':coordx',$coordx);
      $db->bind(':coordy',$coordy);
      if(isset($id)){
        $db->bind(':id',$id);
      } else {
        $db->bind(':id',null);
      }
      if (null === $govt) {
        $govt = new govt();
        $govt = $govt->getIndieGovt();
      }
      $db->bind(':govt',$govt);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      return true;
    }
  }
  public function getConnections($id) {
    $db = new database();
    $db->query("SELECT
      tbl_jump.dest,
      tbl_syst.name,
      tbl_syst.coord_x,
      tbl_syst.coord_y,
      tbl_syst.id,
      IF (tbl_beacon.type = 'D' AND tbl_beacon.timestamp > ADDDATE(NOW(), INTERVAL 1 WEEK),COUNT(tbl_beacon.id), 0) AS beacons,
      IF (tbl_spob.id IS NOT NULL, count(DISTINCT tbl_spob.id), 0) AS ports
      FROM tbl_jump
      LEFT JOIN tbl_syst ON tbl_syst.id = tbl_jump.dest
      LEFT JOIN tbl_beacon ON tbl_beacon.syst = tbl_syst.id
      LEFT JOIN tbl_spob ON tbl_syst.id = tbl_spob.parent
      WHERE tbl_jump.origin = ?
      GROUP BY tbl_jump.dest");
    $db->bind(1,$id);
    $db->execute();
    return $db->resultSet();
  }
  public function getJumpData($dest, $origin) {
    $db = new database();
    $db->query("SELECT ssim_jump.*,
    dest.coord_x AS dest_x,
    dest.coord_y AS dest_y,
    dest.name AS dest_name,
    dest.id AS dest,
    origin.coord_x AS origin_x,
    origin.coord_y AS origin_y,
    origin.name AS origin_name,
    origin.id AS origin,
    floor(sqrt(pow(dest.coord_x-origin.coord_x, 2)+(pow(dest.coord_y-origin.coord_y, 2))))*1 AS distance
    FROM ssim_jump
    LEFT OUTER JOIN ssim_syst AS dest ON ssim_jump.dest = dest.id
    LEFT OUTER JOIN ssim_syst AS origin ON ssim_jump.origin = origin.id
    WHERE origin.id = :origin
    AND dest.id = :dest");
    $db->bind(':origin',$origin);
    $db->bind(':dest',$dest);
    if($db->execute()) {
      return $db->single();
    }
  }
  public function listSysts() {
    $db = new database();
    $db->query("SELECT tbl_syst.*,
    tbl_govt.color1,
    tbl_govt.color2,
    GROUP_CONCAT(tbl_jump.dest) AS jumps,
    count(tbl_spob.id) AS ports
    FROM tbl_syst
    LEFT JOIN tbl_govt ON tbl_syst.govt = tbl_govt.id
    LEFT JOIN tbl_jump ON tbl_syst.id = tbl_jump.origin
    LEFT JOIN tbl_spob ON tbl_syst.id = tbl_spob.parent
    GROUP BY tbl_syst.id");
    $db->execute();
    return $db->resultset();
  }

  public function listConnections(){
    $db = new database();
    $db->query("SELECT tbl_jump.*,
    origin.coord_x AS originx,
    origin.coord_y AS originy,
    origin.name AS originname,
    dest.coord_x AS destx,
    dest.coord_y AS desty,
    dest.name AS destname,
    floor(sqrt(pow(dest.coord_x-origin.coord_x, 2)+(pow(dest.coord_y-origin.coord_y, 2))))*1 AS distance
    FROM tbl_jump
    LEFT JOIN tbl_syst AS dest ON tbl_jump.dest = dest.id
    LEFT JOIN tbl_syst AS origin ON tbl_jump.origin = origin.id;");
    $db->execute();
    return $db->resultSet();
  }

  public function linkSyst($origin,$dest) {
    $db = new database();
    $db->query("INSERT IGNORE INTO tbl_jump (origin, dest) VALUES
      (?,?)");
    $db->bind(1,$origin);
    $db->bind(2,$dest);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $db->bind(1,$dest);
    $db->bind(2,$origin);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }
}
