<?php 

class pilot {

  public $name;
  public $uid;
  public $fingerprint;
  public $credits;
  public $legal;
  public $status;
  public $spob;
  public $vessel;
  public $location;

  public $spobname;
  public $spobtype;
  public $systname;

  public $govt;
  public $ship;
  public $fuelgauge;

  public $fullstatus;

  public function __construct($simple=null) {
    if (isset($_SESSION['pilotuid'])) {
      $uid = $_SESSION['pilotuid'];
      $pilot = $this->getPilot($uid);

      $this->name = $pilot->name;
      $this->uid = $pilot->uid;
      $this->fingerprint = $pilot->fingerprint;
      $this->credits = $pilot->credits;
      $this->legal = $pilot->legal;
      $this->status = $pilot->status;
      $this->spob = $pilot->spob;
      $this->vessel = new vessel($pilot->vessel);
      $this->location = $pilot->location;

      $this->spobname = spobName($pilot->spobname,$pilot->spobtype);
      $this->spobtype = $pilot->spobtype;
      $this->systname = $pilot->systname;

      $this->govt = new stdclass();
      $this->govt->name = $pilot->govtname;
      $this->govt->color1 = $pilot->color1;
      $this->govt->color2 = $pilot->color2;
      $this->govt->iso = $pilot->isoname;
      $this->govt->id = $pilot->govt;
      
      switch ($this->status) {
        case 'L':
        default:
        $this->fullstatus = landVerb($this->spobtype,null) ." ".$this->spobname;
        break;

        case 'S':
          $this->fullstatus = "In orbit at $this->systname";
        break;

        case 'B':
          $this->fullstatus = "In bluespace";
        break;
      }

      //FUTUREPROOFING
      if (TRUE != $simple) {}
    }
  }

  public function getPilot($uid) {
    $db = new database();
    $db->query("SELECT tbl_pilot.*, 
      tbl_govt.name AS govtname,
      tbl_govt.color1 AS color1,
      tbl_govt.color2 AS color2,
      tbl_govt.isoname,
      tbl_spob.name AS spobname,
      tbl_spob.type AS spobtype,
      tbl_syst.name AS systname,
      tbl_vessel.name AS vesselname,
      tbl_vessel.ship AS shipid,
      CASE WHEN tbl_pilot.status = 'L' THEN tbl_pilot.spob
      ELSE tbl_pilot.syst
      END AS location
      FROM tbl_pilot
      LEFT JOIN tbl_govt ON tbl_pilot.govt = tbl_govt.id
      LEFT JOIN tbl_spob ON tbl_pilot.spob = tbl_spob.id
      LEFT JOIN tbl_syst ON tbl_pilot.syst = tbl_syst.id
      LEFT JOIN tbl_vessel ON tbl_pilot.vessel = tbl_vessel.id
      WHERE uid = ?");
    $db->bind(1,$uid);
    $db->execute();
    return $db->single();
  }

  public function getUserPilots($user) {
    $db = new database();
    $db->query("SELECT uid, name, credits, legal, fingerprint
      FROM tbl_pilot WHERE user = :user");
    $db->bind(':user',$user);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }

  public function newPilot($firstname, $lastname) {
    //Set pilot name
    if (empty($firstname) || (empty($lastname))) {
      return returnError("Pilots must have a first and last name.");
    }

    $firstname = filter_var($firstname,FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);

    $lastname = filter_var($lastname,FILTER_SANITIZE_STRING,FILTER_FLAG_STRIP_HIGH);

    $name = ucfirst($firstname). " " . ucfirst($lastname);

    if (empty(trim($name))) {
      return returnError("Invalid name. Please try again.");
    }

    //Set parent user
    $user = new user();
    $user = $user->uid;

    //Set a homeworld
    $spob = new spob();
    $homeworld = $spob->getRandHomeworld();
    $syst = $homeworld->parent;
    $spob = $homeworld->id;
    $govt = $homeworld->govt;
    $homeworld = $homeworld->id;

    //(because we're going to allow players to override this later on)
    $fingerprint = hexPrint($name.date('D, d M Y H:i:s'));

    $db = new database();
    $db->query("SELECT count(*) AS count FROM tbl_pilot WHERE tbl_pilot.user = ?;");
    $db->bind(1, $user);
    $db->execute();
    if (3 < $db->single()->count) {
      return returnError("Only three pilots per player.");
    }
    $db->query("INSERT INTO tbl_pilot
      (uid, name, user, syst, spob,
        homeworld, credits, legal, govt, timestamp, fingerprint, status)
    VALUES (substr(sha1(uuid()),4,12),:name,:user,:syst, :spob,
        :homeworld,:credits,:legal,:govt,NOW(),:fingerprint, 'F')");
    $db->bind(':name',$name);
    $db->bind(':user',$user);
    $db->bind(':syst',$syst);
    $db->bind(':spob',$spob);
    $db->bind(':homeworld',$homeworld);
    $db->bind(':credits',STARTING_CREDITS);
    $db->bind(':legal',STARTING_LEGAL);  
    $db->bind(':govt',$govt);
    $db->bind(':fingerprint',$fingerprint);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $game = new game();
    $game->logEvent('NP',"Created a new pilot: $name");
    $return[] = array(
      'message'=>"Your pilot's license has been issued.".
      "You are cleared to proceed at this time.",
      'level'=>1
    );
    return $return;
  }

  public function activatePilot($pilot) {
    $db = new database();
    $db->query("SELECT user, uid, name, fingerprint
      FROM tbl_pilot WHERE uid = ? AND user = ?");
    $user = new user();
    $db->bind(1,$pilot);
    $db->bind(2,$user->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $activated = $db->single();
    $_SESSION['pilotuid'] = $activated->uid;
    return $activated;
    $game = new game();
    $game->logEvent('AP',"$activated->name activated by $user->uid");
  }

  public function getSystPilots() {
    $db = new database();
    $db->query("SELECT
          tbl_pilot.id,
          tbl_pilot.name,
          tbl_pilot.timestamp,
          tbl_pilot.legal,
          tbl_pilot.govt,
          tbl_pilot.vessel,
          tbl_pilot.ship,
          tbl_govt.name AS government,
          tbl_govt.isoname,
          tbl_govt.color,
          tbl_govt.color2,
          tbl_ship.name AS shipname,
          tbl_ship.class,
          tbl_ship.shipwright,
          ((tbl_ship.shields - tbl_pilot.shielddam) / tbl_ship.shields) *
          100 AS shields,
          ((tbl_ship.armor - tbl_pilot.armordam) / tbl_ship.armor) *
          100 AS armor,
          (tbl_pilot.fuel/tbl_ship.fueltank) * 100 AS fuelmeter
          FROM tbl_pilot
      LEFT JOIN tbl_ship ON tbl_pilot.ship = tbl_ship.id
      LEFT JOIN tbl_govt ON tbl_pilot.govt = tbl_govt.id
      WHERE tbl_pilot.syst = :syst
      AND tbl_pilot.status = 'S'
      AND tbl_pilot.id != :pilot");
    $db->bind(':syst',$this->pilot->syst);
    $db->bind(':pilot',$this->pilot->id);
    $db->execute();
    return $db->resultset();
  }

  public function getUserPilot() {
    $db = new database();
    $db->query("SELECT tbl_pilot.*,
          tbl_spob.name AS planet,
          tbl_spob.type AS spobtype,
          tbl_syst.name AS system,
          tbl_govt.name AS government,
          tbl_govt.isoname,
          tbl_govt.color,
          tbl_govt.color2,
          tbl_ship.fueltank,
          tbl_ship.name AS shipname,
          tbl_ship.class,
          tbl_ship.shipwright,
          ((tbl_ship.shields - tbl_pilot.shielddam) / tbl_ship.shields) *
          100 AS shields,
          ((tbl_ship.armor - tbl_pilot.armordam) / tbl_ship.armor) *
          100 AS armor,
          (tbl_pilot.fuel/tbl_ship.fueltank) * 100 AS fuelmeter,
          tbl_ship.cargobay,
          (SELECT 
            CASE WHEN 
            sum(tbl_cargopilot.amount)
            IS NULL THEN 0
            ELSE sum(tbl_cargopilot.amount) END
            FROM tbl_cargopilot 
            WHERE tbl_cargopilot.pilot = tbl_pilot.id) 
          AS commodcargo,
          (SELECT
            CASE WHEN
            sum(tbl_misn.amount) 
            IS NULL THEN 0
            ELSE sum(tbl_misn.amount) END
            FROM tbl_misn 
            WHERE tbl_misn.pilot = tbl_pilot.id 
            AND tbl_misn.status = 'T') 
          AS misncargo,
          (SELECT commodcargo) + (SELECT misncargo) AS cargo,
          tbl_ship.cargobay,
          tbl_ship.cargobay - (SELECT cargo) AS capacity,
          floor(((SELECT cargo) / tbl_ship.cargobay) * 100) AS cargometer,
          UNIX_TIMESTAMP(tbl_pilot.jumpeta) - UNIX_TIMESTAMP(NOW())
          AS remaining
          FROM tbl_pilot
      LEFT JOIN tbl_spob ON tbl_pilot.spob = tbl_spob.id
      LEFT JOIN tbl_syst ON tbl_pilot.syst = tbl_syst.id
      LEFT JOIN tbl_ship ON tbl_pilot.ship = tbl_ship.id
      LEFT JOIN tbl_govt ON tbl_pilot.govt = tbl_govt.id
      WHERE user = :user");
    $user = new user();
    $db->bind(":user",$user->uid);
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
    $db->query("SELECT * FROM tbl_pilot WHERE user = :user");
    $user = new user();
    $db->bind(":user",$user->id);
    $db->execute();
    return $db->single();
  }

  public function getPilotDataFast($id) {
    $db = new database();
    $db->query("SELECT * FROM tbl_pilot WHERE id = :id");
    $db->bind(":id",$id);
    $db->execute();
    return $db->single();
  }

  public function getPilotNameByID($id) {
    $db = new database();
    $db->query("SELECT name FROM tbl_pilot WHERE id = :id");
    $db->bind(':id',$id);
    $db->execute();
    return $db->single()->name;
  }

  public function getPilotLocation($id){
    $db = new database();
    $db->query("SELECT tbl_pilot.name,
            tbl_pilot.id,
            tbl_spob.id AS spobid,
            tbl_spob.name AS planet,
            tbl_syst.id AS systid,
            tbl_syst.name AS system
            FROM tbl_pilot
            LEFT JOIN tbl_spob ON tbl_pilot.spob = tbl_spob.id
            LEFT JOIN tbl_syst ON tbl_spob.parent = tbl_syst.id
            WHERE tbl_pilot.id = :pilot");
    $db->bind(':pilot',$id);
    $db->execute();
    return $db->single();
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
    $db->query("UPDATE tbl_pilot
      SET fuel = fuel + :fuel
      WHERE id = :id");
    $db->bind(':fuel',$diff);
    $db->bind(':id',$this->pilot->id);
    $db->execute();
    $this->deductCredits($cost);
    $game = new game();
    $game->logEvent('R',"Refueled for ".$cost." credits. ".$diff." units.");
    $return[] = array(
      "message"=>"Refueled for ".$cost." credits. ".$diff." units.",
      "level"=>"normal"
    );
    return $return;
  }

  public function liftoff(){
    if($this->isLanded()) {
      $db = new database();
      $syst = new syst($this->pilot->syst);
      $db->query('UPDATE tbl_pilot
        SET status = "S", spob = null
        WHERE id = :id');
      $db->bind(':id',$this->pilot->id);
      if($db->execute()) {
        $game = new game();
        $game->logEvent('D','Lifted off.');
        $return[] = array(
          'message'=>'You lifted off!',
          'level'=>'normal'
        );
        return $return;
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
    $db->query('UPDATE tbl_pilot
      SET status = "L", spob = :spob, shielddam = 0
      WHERE id = :id');
    $db->bind(':spob', $spob->spob->id);
    $db->bind(':id', $this->pilot->id);
    if($db->execute()) {
      $game = new game();
      $game->logEvent('A', landVerb($spob->spob->type,'past')." ".$spob->spob->name);
      $return[] = array(
        "message"=>"You have ".landVerb($spob->spob->type,'past')." ".$spob->spob->name,
        'level'=>'normal'
      );
      return $return;
    }
  }

  public function creditCheck($credits) {
    $db = new database();
    $db->query("SELECT TRUE
      FROM tbl_pilot
      WHERE tbl_pilot.credits >= ?
      AND tbl_pilot.uid = ?");
    $db->bind(1,$credits);
    $db->bind(2,$this->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    if (empty($db->single()->TRUE)) {
      return false;
    }
    return true;
  }

  public function deductCredits($credits) {
    if ($credits > 0 && $this->creditCheck($credits)) {
      $db = new database();
      $db->query("UPDATE tbl_pilot
        SET credits = credits - ?
        WHERE uid = ?");
      $db->bind(1,$credits);
      $db->bind(2,$this->uid);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      $db->query("SELECT credits FROM tbl_pilot WHERE uid = ?");
      $db->bind(1,$this->uid);
      return returnMessage("Deducted ".credits($credits));
    }
  }

  public function addCredits($credits) {
    if ($credits > 0) {
      $db = new database();
      $db->query("UPDATE tbl_pilot
        SET credits = credits + ?
        WHERE uid = ?");
      $db->bind(1,$credits);
      $db->bind(2,$this->uid);
      $db->execute();
      //return $db->rowcount();
      return returnSuccess("Added ".credits($credits)." to your account.");
    }
  }

  public function setStatus($status) {
    //TODO: List allowed statuses and check
    $db = new database();
    $db->query("UPDATE tbl_pilot SET status = ? WHERE uid = ?");
    $db->bind(1,$status);
    $db->bind(2,$this->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function setVessel($vessel) {
    $db = new database();
    $db->query("UPDATE tbl_pilot SET vessel = ? WHERE uid = ?");
    $db->bind(1,$vessel);
    $db->bind(2,$this->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function subtractLegal($legal) {
    if ($legal > 0) {
      $db = new database();
      $db->query("UPDATE tbl_pilot
        SET legal = legal - :legal
        WHERE id = :id");
      $db->bind(':legal',$legal);
      $db->bind(':id',$this->pilot->id);
      $db->execute();
      //return $db->rowcount();
      $return['message'] = "$legal legal points deducted";
      $return['level'] = "warn";
      if ($this->pilot->legal <= PIRATE_THRESHHOLD) {
        $return[] = $this->makePirate();
      }
      return $return;
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
      $db->query("UPDATE tbl_pilot SET
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
      $db->query("UPDATE tbl_pilot
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
      $db->query("UPDATE tbl_pilot SET vessel = :name WHERE id = :id");
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
    $db->query("SELECT tbl_cargopilot.*
    FROM tbl_cargopilot
    WHERE tbl_cargopilot.commod = :commod
    AND tbl_cargopilot.pilot = :pilot");
    $db->bind(':commod',$commod);
    $db->bind(':pilot',$this->pilot->id);
    $db->execute();
    return $db->single();
  }

  public function newPilotCargo($commod, $amount) {
    $db = new database();
    $db->query("INSERT INTO tbl_cargopilot 
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
    $db->query("UPDATE tbl_cargopilot SET amount = amount + :amount, lastchange = NOW(), lastsyst = :lastsyst 
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
    $db->query("UPDATE tbl_cargopilot SET amount = amount - :amount,
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
    $db->query("SELECT (SELECT 
    CASE WHEN 
    sum(tbl_cargopilot.amount)
    IS NULL THEN 0
    ELSE sum(tbl_cargopilot.amount) END
    FROM tbl_cargopilot 
    WHERE tbl_cargopilot.pilot = tbl_pilot.id) 
    AS commodcargo,
    (SELECT
      CASE WHEN
      sum(tbl_misn.amount) 
      IS NULL THEN 0
      ELSE sum(tbl_misn.amount) END
      FROM tbl_misn 
      WHERE tbl_misn.pilot = tbl_pilot.id 
      AND tbl_misn.status = 'T') 
    AS misncargo,
    (SELECT commodcargo) + (SELECT misncargo) AS cargo,
    tbl_ship.cargobay,
    tbl_ship.cargobay - (SELECT cargo) AS capacity,
    floor(((SELECT cargo) / tbl_ship.cargobay) * 100) AS cargometer
    FROM tbl_pilot
    LEFT JOIN tbl_ship ON tbl_pilot.ship = tbl_ship.id
    WHERE tbl_pilot.id = :pilot");
    if(isset($this->pilot->id)){
      $db->bind(':pilot', $this->pilot->id);
    } else {
      $db->bind(':pilot', $id);
    }
    $db->execute();
    return $db->single();
  }

  public function setGovt($id) {
    $db = new database();
    $db->query("UPDATE tbl_pilot SET govt = :id
      WHERE tbl_pilot.id = :pilot");
    $db->bind(':id',$id);
    $db->bind(':pilot',$this->pilot->id);
    $db->execute();
    //TODO: Notify
  }

  public function makePirate() {
    $govt = new govt();
    $pirate = $govt->getPirateGovt();
    if($this->pilot->govt != $pirate) {
      $this->setGovt($govt->getPirateGovt());
      $message = new message();
      $msg = "This is a formal notice. You legal rating has dropped ";
      $msg.= "significantly enough to label you as a pirate. A warrant for ";
      $msg.= "your arrest has been issued to all relevant governments.";
      $message->newSystemMessage($this->pilot->id,'Legal Notice',$msg);
      $return[] = array(
          "message"=>"You have been labled a pirate!",
          "level"=>"emergency"
        );
      return $return;
    }
  }

  public function getPilotErrata($key) {
    $db = new database();
    $db->query("SELECT `value` FROM tbl_piloterrata
      WHERE pilot = :pilot AND `key` = :key");
    $db->bind(':pilot',$this->pilot->id);
    $db->bind(':key',$key);
    $db->execute();
    return $db->single()->value;
  }

  public function buyShip($ship) {
    $ship = new ship($ship);
    $cargo = $this->getPilotCargoStats();
    if ($ship->cost > $pilot->pilot->credits) {
      $return[] = array(
        "message"=>"You cannot afford this ship.",
        "level"=>"warn"        
      );
      return $return;
    }
    if ($ship->cargobay < $cargo->capacity) {
      $return[] = array(
        "message"=>"The new ship does not have a large enough cargobay for your cargo.",
        "level"=>"warn"        
      );
      return $return;
    } 
  } 
  private function forceJumpCompletion() {
    //Sanity check: If a pilot starts jumping and logs out in mid-jump,
    //They'll stay in space until they log back in. This forces all pilots
    //with expired jump times to land.
    $db = new database();
    $db->query("UPDATE tbl_pilot SET status = 'S'
      WHERE UNIX_TIMESTAMP(jumpeta) < UNIX_TIMESTAMP(NOW())
      AND status = 'J'");
    $db->execute();
  }
}