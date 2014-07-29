<?php 

class pilot {

  public $pilotid;
  public $pilot;

  public function __construct($load=true) {

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
    if ($load === true) {
      $this->pilot = $this->getUserPilot();
    }
  }

  public function getUserPilot() {
    $db = new database();
    $db->query("SELECT ssim_pilot.*,
          ssim_spob.name AS planet,
          ssim_syst.name AS system,
          ssim_govt.name AS government,
          ssim_govt.isoname,
          ssim_govt.color,
          ssim_govt.color2,
          ssim_ship.fueltank,
          ((ssim_ship.shields - ssim_pilot.shielddam) / ssim_ship.shields) *
          100 AS shields,
          ((ssim_ship.armor - ssim_pilot.armordam) / ssim_ship.armor) *
          100 AS armor,
          (ssim_pilot.fuel/ssim_ship.fueltank) * 100 AS fuelmeter,
          ssim_ship.cargobay,
           CASE WHEN (sum(ssim_cargopilot.amount) IS NULL)
           THEN 0
           ELSE sum(ssim_cargopilot.amount)
           END AS cargo,
           CASE WHEN (sum(ssim_cargopilot.amount) IS NULL)
             THEN ssim_ship.cargobay
             ELSE ssim_ship.cargobay - sum(ssim_cargopilot.amount)
           END AS capacity,
           CASE WHEN ((sum(ssim_cargopilot.amount)/ssim_ship.cargobay * 100) IS NULL)
             THEN 0
             ELSE (sum(ssim_cargopilot.amount)/ssim_ship.cargobay * 100)
           END AS cargometer,
           UNIX_TIMESTAMP(ssim_pilot.jumpeta) - UNIX_TIMESTAMP(NOW()) AS remaining
          FROM ssim_pilot
      LEFT JOIN ssim_spob ON ssim_pilot.spob = ssim_spob.id
      LEFT JOIN ssim_syst ON ssim_pilot.syst = ssim_syst.id
      LEFT JOIN ssim_ship ON ssim_pilot.ship = ssim_ship.id
      LEFT JOIN ssim_govt ON ssim_pilot.govt = ssim_govt.id
      LEFT JOIN ssim_cargopilot ON ssim_pilot.id = ssim_cargopilot.pilot
          WHERE user = :user");
    $user = new user();
    $db->bind(":user",$user->id);
    $db->execute();
    $pilots = $db->single();
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
    $fingerprint = hexPrint($name.date('D, d M Y H:i:s'));

    $db = new database();
    $db->query("INSERT INTO ssim_pilot
      (name, user, syst, spob, ship, vessel,
        homeworld, credits, legal, govt, fuel, timestamp, fingerprint)
    VALUES (:name,:user,:syst,:spob,:ship,:vessel,
        :homeworld,:credits,:legal,:govt,:fuel,NOW(),:fingerprint)");
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

  public function refuel() {
    $db = new database();
    //Get the fuel cost
    $spob = new spob($this->pilot->spob);
    //Determine how much fuel we need
    $diff = $this->pilot->fueltank - $this->pilot->fuel;
    if ($diff <= 0) {
      return "You cannot refuel at this time.";
    }
    //Calculate the price
    $cost = $spob->fuelcost * $diff;
    if ($cost > $this->pilot->credits) {
      return "You can't afford to refuel";
    }
    //Refuel...
    $db->query("UPDATE ssim_pilot
      SET fuel = fuel + :fuel
      WHERE id = :id");
    $db->bind(':fuel',$diff);
    $db->bind(':id',$this->pilot->id);
    $db->execute();
    $this->subtractCredits($cost);
    return "Refueled for ".$cost." credits";
  }

  private function subtractCredits($credits) {
    $db = new database();
    $db->query("UPDATE ssim_pilot
      SET credits = credits - :credits
      WHERE id = :id");
    $db->bind(':credits',$credits);
    $db->bind(':id',$this->pilot->id);
    $db->execute();
    return $db->rowcount();
  }

}
