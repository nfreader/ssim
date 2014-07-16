<?php 

class pilot {

  private $pilotid;

  public function __construct() {

    if (isset($_SESSION['pilotid'])) {
      $this->pilotid = $_SESSION['pilotid'];
    } else {
      //No valid session has been initiated so kill it with fire.
      //TODO: I bet we can set up an API if we check for a specific header
      //here... 
    }
    //Sanity check: If a pilot starts jumping and logs out in mid-jump,
    //They'll stay in space until they log back in. This forces all pilots
    //with expired jump times to land.
    $db = new database();
    $db->query("UPDATE ssim_pilot SET status = 'S'
      WHERE UNIX_TIMESTAMP(jumpeta) < UNIX_TIMESTAMP(NOW())
      AND status = 'J'");
    $db->execute();
  }

  public function getUserPilots() {
    $db = new database();
    $db->query("SELECT
      id,
      name,
      user,
      fingerprint
      FROM ssim_pilot
      WHERE user = :user
    ");
    $db->bind(":user",$_SESSION['userid']);
    $db->execute();
    $pilots = $db->resultSet();
    if ($pilots === array()) {
      return false;
    } else {
      return $pilots;
    }
  }

}

?>