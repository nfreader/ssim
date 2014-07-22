<?php 

class syst {

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

  public function getSyst($id=null) {
    $db = new database();
    if ($id === null) { //Get all systems
      $db->query("SELECT * FROM ssim_syst");
      $db->execute();
      return $db->resultSet();
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

}
