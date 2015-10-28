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


  public function __construct($id=null) {
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
      $spob = new spob();
      $this->spobs = $spob->getSystemSpobs($syst->id);
      $beacons = new beacon();
      $this->beacons = $beacons->getBeacons($syst->id);
      $this->connections = $this->getConnections($this->id);
    }
  }

  public function getSysts() {
    $db = new database();
    $db->query("SELECT * FROM tbl_syst");
    $db->execute();
    return $db->resultSet();
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

/* addSyst
 *
 * Adds a system to the galaxy.
 *
 * @name (string) The name of the system
 * @coordx (int) The X coordinate of the system
 * @coordy (int) The Y coordinate of the system
 *
 * @return (str) A count of the number of affected rows.
 *
*/

  public function addSyst($name, $coordx, $coordy, $govt=null) {
    $db = new database();
    $db->query("INSERT INTO ssim_syst (name, coord_x, coord_y, govt) VALUES
    (:name, :coordx, :coordy, :govt)");
    if (empty($name)
      || empty($coordx)
      || empty($coordy)) {
      return false;
    } else {
      $db->bind(':name',$name);
      $db->bind(':coordx',$coordx);
      $db->bind(':coordy',$coordy);
      if ($govt === null) {
        $govt = new govt();
        $govt = $govt->getIndieGovt();
      }
      $db->bind(':govt',$govt);
      $db->execute();
      return true;
    }
  }

  public function getConnections($id) {
    $db = new database();
    $db->query("SELECT
      ssim_jump.dest,
      ssim_syst.name,
      ssim_syst.coord_x,
      ssim_syst.coord_y,
      ssim_syst.id,
      IF (ssim_beacon.type = 'D',COUNT(ssim_beacon.id), 0) AS beacons
      FROM ssim_jump
      LEFT JOIN ssim_syst ON ssim_syst.id = ssim_jump.dest
      LEFT JOIN ssim_beacon ON ssim_beacon.syst = ssim_syst.id
      WHERE ssim_jump.origin = ?
      GROUP BY ssim_jump.dest");
    $db->bind(1,$id);
    $db->execute();
    return $db->resultSet();
  }

  public function getMapLines(){
    $db = new database();
    $db->query("SELECT
    dest.coord_x AS dest_x, 
    dest.coord_y AS dest_y, 
    origin.coord_x AS origin_x, 
    origin.coord_y AS origin_y
    FROM ssim_jump
    LEFT OUTER JOIN ssim_syst AS origin ON ssim_jump.dest = origin.id
    LEFT OUTER JOIN ssim_syst AS dest ON ssim_jump.origin = dest.id");
    $db->execute();
    return json_encode($db->resultset(), JSON_NUMERIC_CHECK);
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
public function addNewSyst($syst) {
  if ($this->canAddNewSyst($syst)) {
    //New syst handler
    $host = $this->getSyst($syst);

    if (floor(rand(1,10)) < 5) {
      $newX = $host->coord_x + floor(rand(-20,20));
      $newY = $host->coord_y + floor(rand(-20,20));
    } else {
      $newX = $host->coord_x - floor(rand(-20,20));
      $newY = $host->coord_y - floor(rand(-20,20));
    }

    if (floor(rand(1,10)) < 5) { //Chance for the new system to inherit the govt
      $govt = $host->govt;
    } else {
      $govt = new govt();
      $govt = $govt->getIndieGovt();
    }

    global $systPrefixes;
    global $systNames;

    if (floor(rand(1,10)) < 3) {
      //This system will have a prefix
      $name = $systPrefixes[array_rand($systPrefixes)] ."-";
      $name.= $systNames[array_rand($systNames)];
    } else {
      $name = $systNames[array_rand($systNames)];
    }

    $this->addSyst($name, $newX, $newY, $govt);
    $this->linkSysts($host->id,NULL,$name);

    } else {
      //Exit silently
      return;
    } 
  }
  public function canAddNewSyst($syst) {
    $db = new database();
    $db->query("SELECT
        COUNT(*) AS connections
        FROM ssim_jump
        LEFT JOIN ssim_syst ON ssim_syst.id = ssim_jump.dest
        WHERE ssim_jump.origin = :syst");
    $db->bind(':syst',$syst);
    $db->execute();

    if ($db->single()->connections < 3) {
      return true;
    } else {
      return false;
    }
  }

  public function linkSysts($origin, $dest=NULL, $destName=NULL) {
    if ($dest==NULL && $destName!=NULL) {
      //We only know the id of the ORIGIN, not the destination and the origin
      //So it's a simple matter of getting the destination id by name!
      //(THIS IS SO DUMB)
      $dest = $this->getSystByName($destName);
      $db = new database();
      $db->query("INSERT INTO ssim_jump 
      (origin, dest) VALUES
      (:origin, :dest)");

      $db->bind(':origin',$origin);
      $db->bind(':dest',$dest->id);
      $db->execute();
  
      $db->bind(':origin',$dest->id);
      $db->bind(':dest',$origin);
      $db->execute();

    } else {
      //This is used by admin/linkSysts.php, which will generate the recipricol
      //links automatically, so there's no need to generate that here
    $db->query("INSERT INTO ssim_jump 
      (origin, dest) VALUES
      (:origin, :dest");
      global $dbh;
      $link = $dbh->prepare(str_replace('ssim_', TBL_PREFIX, $sql));
      $link->execute(array(
        ':origin'=>$origin,
        ':dest'=>$dest
      ));
    }
  }
  public function getSystByName($name) {
    $db = new database();
    $db->query("SELECT * FROM ssim_syst WHERE name = :name");
    $db->bind(':name',$name);
    $db->execute();
    return $db->single();
  }
}
