<?php 

class syst {

public $syst;
public $uninhabited;

public function __construct($id=null) {
  if (isset($id)) {
    $this->syst = $this->getSyst($id);
    $db = new database();
    $db->query("SELECT COUNT(*) AS spobs FROM ssim_spob WHERE parent = :syst");
    $db->bind(':syst',$this->syst->id);
    $db->execute();
    if($db->single()->spobs == 0) {
      $this->uninhabited = true;
    } else {
      $this->uninhabited = false;
    }
  }
}

/* getSyst
 *
 * Gets an object of all systems, or an object of one system if an ID is
 * specified.
 *
 * @id (int) (optional) The system ID
 *
 * @return (obj) An object of all system data, joined with other relevant 
 * tables.
 *
*/

  public function getSyst($id=null,$json=false) {
    $db = new database();
    if ($id === null) { //Get all systems
      $db->query("SELECT ssim_syst.*,
      ssim_govt.name AS government,
      ssim_govt.isoname,
      ssim_govt.color,
      ssim_govt.color2,
      ssim_govt.id AS govid
      FROM ssim_syst
      LEFT JOIN ssim_govt ON ssim_syst.govt = ssim_govt.id");
      $db->execute();
      if($json===false) {
        return $db->resultSet();
      } else {
        return json_encode($db->resultSet());
      }
    } elseif ($id != null) {
      $db->query("SELECT ssim_syst.*,
      ssim_govt.name AS government,
      ssim_govt.isoname,
      ssim_govt.color,
      ssim_govt.color2,
      ssim_govt.id AS govid
      FROM ssim_syst
      LEFT JOIN ssim_govt ON ssim_syst.govt = ssim_govt.id
      WHERE ssim_syst.id = :id");
      $db->bind(':id',$id);
      $db->execute();
      return $db->single();
    }
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
      ssim_syst.id
      FROM ssim_jump
      LEFT JOIN ssim_syst ON ssim_syst.id = ssim_jump.dest
      WHERE ssim_jump.origin = :id");
    $db->bind(':id',$id);
    $db->execute();
    return $db->resultSet();
  }

  public function getMapLines(){
    $db = new database();
    $db->query("SELECT
    dest.coord_x AS x1, 
    dest.coord_y AS y1, 
    origin.coord_x AS x2, 
    origin.coord_y AS y2
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
