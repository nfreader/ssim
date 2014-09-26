<?php 

class pilot {

  public $pilotid;
  public $pilot; //BIGASS PILOT OBJECT
  public $fingerprint;
  public $capacity;
  public $credits;

  public $syst;

  public function __construct($load=true, $fast=false, $id=NULL) {
    //Sanity check: If a pilot starts jumping and logs out in mid-jump,
    //They'll stay in space until they log back in. This forces all pilots
    //with expired jump times to land.
    $db = new database();
    $db->query("UPDATE ssim_pilot SET status = 'S'
      WHERE UNIX_TIMESTAMP(jumpeta) < UNIX_TIMESTAMP(NOW())
      AND status = 'J'");
    $db->execute();

    //By default, a new instance of pilot() will call the getUserPilot()
    //method and set that returned object as the pilot property
    if ($load === true && $fast === false && $id === null) {
      $this->pilot = $this->getUserPilot();
       $this->capacity = $this->pilot->capacity;
    }
    //If $fast is true, just do a basic query based on the session user id
    elseif ($load === true && $fast === true && $id === null) {
      $this->pilot = $this->getUserPilotFast();
    }
    //Or, quickly load a pilot's data by pilot id
    elseif ($load === true && $fast === true && isset($id)) {
      $this->pilot = $this->getPilotDataFast($id);
    }
    //Or, skip all that and don't do a damn thing in cases where we need a very
    //small amount of data. This is super fast[citation needed].
    elseif ($load == false && $fast == false && $id === null) {
      return;
    }

    if (isset($this->pilot)) {
      $this->fingerprint = hexPrint($this->pilot->name.$this->pilot->timestamp);
      $this->credits = $this->pilot->credits;
      $this->syst = $this->pilot->syst;
    }
  }

  public function isLanded() {
    if ($this->pilot->status === 'L') {
      return true;
    }
  }

  public function isInSpace() {
    if($this->pilot->status === 'S' && $this->pilot->spob === null) {
      return true;
    }
  }

  public function userHasPilot($user) {
    $db = new database();
    $db->query("SELECT id, name FROM ssim_pilot WHERE user = :user");
    $db->bind(':user',$user);
    if ($db->execute()) {
      return $db->single();
    } else {
      return false;
    }
  }

  public function getSystPilots() {
    $db = new database();
    $db->query("SELECT
          ssim_pilot.id,
          ssim_pilot.name,
          ssim_pilot.timestamp,
          ssim_pilot.legal,
          ssim_pilot.govt,
          ssim_pilot.vessel,
          ssim_pilot.ship,
          ssim_govt.name AS government,
          ssim_govt.isoname,
          ssim_govt.color,
          ssim_govt.color2,
          ssim_ship.name AS shipname,
          ssim_ship.class,
          ssim_ship.shipwright,
          ((ssim_ship.shields - ssim_pilot.shielddam) / ssim_ship.shields) *
          100 AS shields,
          ((ssim_ship.armor - ssim_pilot.armordam) / ssim_ship.armor) *
          100 AS armor,
          (ssim_pilot.fuel/ssim_ship.fueltank) * 100 AS fuelmeter
          FROM ssim_pilot
      LEFT JOIN ssim_ship ON ssim_pilot.ship = ssim_ship.id
      LEFT JOIN ssim_govt ON ssim_pilot.govt = ssim_govt.id
      WHERE ssim_pilot.syst = :syst
      AND ssim_pilot.status = 'S'
      AND ssim_pilot.id != :pilot");
    $db->bind(':syst',$this->pilot->syst);
    $db->bind(':pilot',$this->pilot->id);
    $db->execute();
    return $db->resultset();
  }

  public function getUserPilot() {
    $db = new database();
    $db->query("SELECT ssim_pilot.*,
          ssim_spob.name AS planet,
          ssim_spob.type AS spobtype,
          ssim_syst.name AS system,
          ssim_govt.name AS government,
          ssim_govt.isoname,
          ssim_govt.color,
          ssim_govt.color2,
          ssim_ship.fueltank,
          ssim_ship.name AS shipname,
          ssim_ship.class,
          ssim_ship.shipwright,
          ((ssim_ship.shields - ssim_pilot.shielddam) / ssim_ship.shields) *
          100 AS shields,
          ((ssim_ship.armor - ssim_pilot.armordam) / ssim_ship.armor) *
          100 AS armor,
          (ssim_pilot.fuel/ssim_ship.fueltank) * 100 AS fuelmeter,
          ssim_ship.cargobay,
          (SELECT sum(ssim_cargopilot.amount) FROM ssim_cargopilot WHERE ssim_cargopilot.pilot = ssim_pilot.id) AS commodcargo,
              (SELECT sum(ssim_misn.amount) FROM ssim_misn WHERE ssim_misn.pilot = ssim_pilot.id) AS misncargo,
              (SELECT commodcargo) + (SELECT misncargo) AS cargo,
              ssim_ship.cargobay,
              ssim_ship.cargobay - (SELECT cargo) AS capacity,
              floor(((SELECT cargo) / ssim_ship.cargobay) * 100) AS cargometer,
          UNIX_TIMESTAMP(ssim_pilot.jumpeta) - UNIX_TIMESTAMP(NOW())
          AS remaining
          FROM ssim_pilot
      LEFT JOIN ssim_spob ON ssim_pilot.spob = ssim_spob.id
      LEFT JOIN ssim_syst ON ssim_pilot.syst = ssim_syst.id
      LEFT JOIN ssim_ship ON ssim_pilot.ship = ssim_ship.id
      LEFT JOIN ssim_govt ON ssim_pilot.govt = ssim_govt.id
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

  public function getUserPilotFast() {
    $db = new database();
    $db->query("SELECT * FROM ssim_pilot WHERE user = :user");
    $user = new user();
    $db->bind(":user",$user->id);
    $db->execute();
    return $db->single();
  }

  public function getPilotDataFast($id) {
    $db = new database();
    $db->query("SELECT * FROM ssim_pilot WHERE id = :id");
    $db->bind(":id",$id);
    $db->execute();
    return $db->single();
  }

  public function getPilotNameByID($id) {
    $db = new database();
    $db->query("SELECT name FROM ssim_pilot WHERE id = :id");
    $db->bind(':id',$id);
    $db->execute();
    return $db->single()->name;
  }

  public function getPilotLocation($id){
    $db = new database();
    $db->query("SELECT ssim_pilot.name,
            ssim_pilot.id,
            ssim_spob.id AS spobid,
            ssim_spob.name AS planet,
            ssim_syst.id AS systid,
            ssim_syst.name AS system
            FROM ssim_pilot
            LEFT JOIN ssim_spob ON ssim_pilot.spob = ssim_spob.id
            LEFT JOIN ssim_syst ON ssim_spob.parent = ssim_syst.id
            WHERE ssim_pilot.id = :pilot");
    $db->bind(':pilot',$id);
    $db->execute();
    return $db->single();
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
    $db->bind(':spob', NULL);
    $db->bind(':ship',$ship);
    $db->bind(':vessel',$vessel);
    $db->bind(':homeworld',$homeworld);
    $db->bind(':credits',STARTING_CREDITS);
    $db->bind(':legal',STARTING_LEGAL);  
    $db->bind(':govt',$govt);
    $db->bind(':fuel',$fuel);
    $db->bind(':fingerprint',$fingerprint);
    if($db->execute()) {
      return "Your pilot's license has been issued.";
    }
  }

  public function refuel() {
    if($this->pilot->status != 'L') {
      return "You must dock or land before you can refuel";
    }
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
    $this->deductCredits($cost);
    $game = new game();
    $game->logEvent('R',"Refueled for ".$cost." credits. ".$diff." units.");
    return "Refueled for ".$cost." credits";
  }

  public function liftoff(){
    if($this->isLanded()) {
      $db = new database();
      $syst = new syst($this->pilot->syst);
      $db->query('UPDATE ssim_pilot
        SET status = "S", spob = null
        WHERE id = :id');
      $db->bind(':id',$this->pilot->id);
      if($db->execute()) {
        $game = new game();
        $game->logEvent('D','Lifted off.');
        return "You lifted off";
      }
    } else {
      return "Unable to lift off.";
    }
  }

  public function land($spob) {
    $spob = new spob($spob);
    $db = new database();
    //Shields recharge automatically on land.
    //Hull damage has to be repaired and paid for.
    $db->query('UPDATE ssim_pilot
      SET status = "L", spob = :spob, shielddam = 0
      WHERE id = :id');
    $db->bind(':spob', $spob->spob->id);
    $db->bind(':id', $this->pilot->id);
    if($db->execute()) {
      $game = new game();
      $game->logEvent('A', landVerb($spob->spob->type,'past')." ".$spob->spob->name);
      return "You have ".landVerb($spob->spob->type,'past')." ".$spob->spob->name;
    }
  }

  public function deductCredits($credits) {
    if ($credits > 0) {
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

  public function addCredits($credits) {
    if ($credits > 0) {
      $db = new database();
      $db->query("UPDATE ssim_pilot
        SET credits = credits + :credits
        WHERE id = :id");
      $db->bind(':credits',$credits);
      $db->bind(':id',$this->pilot->id);
      $db->execute();
      return $db->rowcount();
    }
  }

  public function subtractLegal($legal) {
    if ($legal > 0) {
      $db = new database();
      $db->query("UPDATE ssim_pilot
        SET legal = legal - :legal
        WHERE id = :id");
      $db->bind(':legal',$legal);
      $db->bind(':id',$this->pilot->id);
      $db->execute();
      return $db->rowcount();
    }
  }

  public function jump($target) {
    $syst = new syst();
    $jump = $syst->getJumpData($target, $this->pilot->syst);
    if (!$jump) {
      return "Invalid coordinates specified. Unable to jump.";
    } elseif ($this->pilot->fuel < 1) {
      return 'Insufficent fuel. Unable to jump.';
    } elseif (!$this->isInSpace()) {
      return 'Gravimetric disturbance detected. Unable to jump.';
    } else {
      $distance = $jump->distance;
      $time = floor(rand($distance-3, $distance+3))*FTL_MULTIPLIER;
      $eta = time()+$time;
      $diff = $eta-time();
      $db = new database(); 
      $db->query("UPDATE ssim_pilot SET
      status = 'J',
      jumpeta = NOW() + INTERVAL :seconds SECOND,
      lastjump = NOW(),
      syst = :syst,
      fuel = fuel - 1 
      WHERE id = :pilot");
      $db->bind(':syst',$jump->dest);
      $db->bind(':pilot',$this->pilot->id);
      $db->bind(':seconds',$diff);
      if ($db->execute()) {
        $game = new game();
        $game->logEvent('J','Initiated Bluespace jump to '.$jump->dest_name);
        return 'Initiated Bluespace jump to '.$jump->dest_name.'! Estimated arrival in: '.floor(($diff + 1)).' seconds.';
      } else {
        return 'Unknown error. Unable to jump.';
      }
    }
  }
  public function jumpComplete() {
    if (strtotime($this->pilot->jumpeta) <= time()) {
      $db = new database();
      $db->query("UPDATE ssim_pilot
        SET status = 'S'
        WHERE id = :id");
      $db->bind(':id',$this->pilot->id);
      
      if ($db->execute()) {
        return 'Jump complete! Welcome to '.$this->pilot->system.'!';
      } else {
        return "Unknown error. Unable to complete jump.";
      }
    } else {
      return "Jump incomplete.";
    }
  }
  public function renameVessel($name) {
    if (trim($name) === '') {
      return 'You cannot have an empty vessel name!';
    } elseif (strip_tags($name) === '') {
      return 'You cannot have an empty vessel name!';
    } else {
      $name = htmlspecialchars($name);
      $db = new database();
      $db->query("UPDATE ssim_pilot SET vessel = :name WHERE id = :id");
      $db->bind(':name',$name);
      $db->bind(':id',$this->pilot->id);
      if($db->execute()) {
        $game = new game();
        $game->logEvent('RV','You are now piloting the '.$name);
        return 'You are now piloting the '.$name;
      }
    }
  }
  public function getPilotCargo() {
    return false;
  }

  public function hasCargoRow($commod) {
    $db = new database();
    $db->query("SELECT ssim_cargopilot.*
    FROM ssim_cargopilot
    WHERE ssim_cargopilot.commod = :commod
    AND ssim_cargopilot.pilot = :pilot");
    $db->bind(':commod',$commod);
    $db->bind(':pilot',$this->pilot->id);
    $db->execute();
    return $db->single();
  }

  public function newPilotCargo($commod, $amount) {
    $db = new database();
    $db->query("INSERT INTO ssim_cargopilot 
    (pilot, commod, amount, lastsyst, lastchange) VALUES
    (:pilot, :commod, :amount, :lastsyst, NOW())
    ON DUPLICATE KEY
    UPDATE amount = amount + :amount, lastsyst = :lastsyst, 
    lastchange = NOW()");
    $db->bind(':pilot',$this->pilot->id);
    $db->bind(':commod',$commod);
    $db->bind(':amount',$amount);
    $db->bind(':lastsyst',$this->syst);
    if ($db->execute()) {
      return true;
    }
  }

  public function addPilotCargo($commod, $amount) {
    $db = new database();
    $db->query("UPDATE ssim_cargopilot SET amount = amount + :amount, lastchange = NOW(), lastsyst = :lastsyst 
      WHERE commod = :commod 
      AND pilot = :pilot");
    $db->bind(':pilot',$this->pilot->id);
    $db->bind(':commod',$commod);
    $db->bind(':amount',$amount);
    $db->bind(':lastsyst',$this->syst);
    if ($db->execute()) {
      return true;
    } 
  }
    
  public function subtractPilotCargo($commod, $amount) { 
    $db = new database();
    $db->query("UPDATE ssim_cargopilot SET amount = amount - :amount,
      lastchange = NOW(), lastsyst = :lastsyst 
      WHERE commod = :commod 
      AND pilot = :pilot");
    $db->bind(':pilot',$this->pilot->id);
    $db->bind(':commod',$commod);
    $db->bind(':amount',$amount);
    $db->bind(':lastsyst',$this->pilot->syst);
    if ($db->execute()) {
      return true;
    } 
  }
  public function getPilotCargoStats($id=null) {
    $db = new database();
    $db->query("SELECT ssim_ship.cargobay,
    (SELECT sum(ssim_cargopilot.amount) FROM ssim_cargopilot WHERE ssim_cargopilot.pilot = :pilot) AS commodcargo,
    (SELECT sum(ssim_misn.amount) FROM ssim_misn WHERE ssim_misn.pilot = :pilot) AS misncargo,
    (SELECT commodcargo) + (SELECT misncargo) AS cargo,
    ssim_ship.cargobay,
    ssim_ship.cargobay - (SELECT cargo) AS capacity,
    floor(((SELECT cargo) / ssim_ship.cargobay) * 100) AS cargometer
    FROM ssim_pilot
    LEFT JOIN ssim_ship ON ssim_pilot.ship = ssim_ship.id
    WHERE ssim_pilot.id = :pilot");
    if(isset($this->pilot->id)){
      $db->bind(':pilot', $this->pilot->id);
    } else {
      $db->bind(':pilot', $id);
    }
    $db->execute();
    return $db->single();
  }

}