<?php 

class syst {

public $syst;

public function __construct($id=null) {
  if (isset($id)) {
    $this->syst = $this->getSyst($id);
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

  public function addSyst($name, $coordx, $coordy) {
    $db = new database();
    $db->query("INSERT INTO ssim_syst (name, coord_x, coord_y) VALUES
    (:name, :coordx, :coordy)");
    if (empty($name)
      || empty($coordx)
      || empty($coordy)) {
      return false;
    } else {
      $db->bind(':name',$name);
      $db->bind(':coordx',$coordx);
      $db->bind(':coordy',$coordy);
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

}
