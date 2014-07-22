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

  public function newPilot($firstname, $lastname) {
    //Set pilot name
    if (empty($firstname) || (empty($lastname))) {
      return false;
    }

    $name = $firstname." ".$lastname;

    //Set parent user
    $user = new user();
    $user = $user->id;

    //Set a homeworld
    $spob = new spob();
    $homeworld = $spob->getRandHomeworld();
    $syst = $homeworld->parent;
    $spob = $homeworld->id;
    $homeworld = $homeworld->id;

    //Set a starter ship
    $starter = new ship();
    $starter = $starter->getRandStarter();
    $ship = $starter->id;
    $fuel = $starter->fueltank; //Top it off

    //Set a random vessel name
    $vessel = randVessel();

    //Set a government
    $govt = 0; //TODO: Set a random independent government? 

    //Set fingerprint
    //(because we're going to allow players to override this later on)
    $fingerprint = hexPrint($name.date('D, d M '.$year.' H:i:s'));

    $db = new database();
    $db->query("INSERT INTO ssim_pilot
      (name, user, syst, spob, ship, vessel,
        homeworld, credits, legal, govt, fuel, timestamp, fingerprint) VALUES 
      (':name',':user',':syst',':spob',':ship',':vessel',
        ':homeworld',':credits',':legal',':govt',':fuel',NOW(),':fingerprint')
      ");
    $db->bind(':name',$name);
    $db->bind(':user',$user);
    $db->bind(':syst',$syst);
    $db->bind(':spob',$spob);
    $db->bind(':ship',$ship);
    $db->bind(':vessel',$vessel);
    $db->bind(':homeworld',$homeworld);
    $db->bind(':credits',STARTING_CREDITS);
    $db->bind(':legal',STARTING_LEGAL);  
    $db->bind(':govt',$govt);
    $db->bind(':fuel',$fuel);
    $db->bind(':fingerprint',$fingerprint);
    if($db->execute()) {
      return true;
    }
  }

}
