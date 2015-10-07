<?php 

class pilot {

  public $name;
  public $fingerprint;
  public $credits;
  public $legal;
  public $status;
  public $spob;

  public $govt;

  public $spobname;
  public $spobtype;
  public $systname;

  public function __construct() {
    if (isset($_SESSION['pilotuid'])) {
      $uid = $_SESSION['pilotuid'];
      $pilot = $this->getPilot($uid);

      $this->name = $pilot->name;
      $this->fingerprint = $pilot->fingerprint;
      $this->credits = $pilot->credits;
      $this->legal = $pilot->legal;
      $this->status = $pilot->status;
      $this->spob = $pilot->spob;

      $this->govt = new stdclass();
      $this->govt->name = $pilot->govtname;
      $this->govt->color1 = $pilot->color1;
      $this->govt->color2 = $pilot->color2;
      $this->govt->iso = $pilot->isoname;
      $this->govt->id = $pilot->govt;

      $this->spobname = spobName($pilot->spobname,$pilot->spobtype);
      $this->spobtype = $pilot->spobtype;
      $this->systname = $pilot->systname;
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
      tbl_syst.name AS systname
      FROM tbl_pilot
      LEFT JOIN tbl_govt ON tbl_pilot.govt = tbl_govt.id
      LEFT JOIN tbl_spob ON tbl_pilot.spob = tbl_spob.id
      LEFT JOIN tbl_syst ON tbl_pilot.syst = tbl_syst.id
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
    $db->query("SELECT count(*) AS count FROM ssim_pilot WHERE ssim_pilot.user = ?;");
    $db->bind(1, $user);
    $db->execute();
    if (3 < $db->single()->count) {
      return returnError("Only three pilots per player.");
    }
    $db->query("INSERT INTO ssim_pilot
      (uid, name, user, syst, spob,
        homeworld, credits, legal, govt, timestamp, fingerprint, status)
    VALUES (substr(sha1(uuid()),4,12),:name,:user,:syst, :spob,
        :homeworld,:credits,:legal,:govt,NOW(),:fingerprint, 'B')");
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
          (SELECT 
            CASE WHEN 
            sum(ssim_cargopilot.amount)
            IS NULL THEN 0
            ELSE sum(ssim_cargopilot.amount) END
            FROM ssim_cargopilot 
            WHERE ssim_cargopilot.pilot = ssim_pilot.id) 
          AS commodcargo,
          (SELECT
            CASE WHEN
            sum(ssim_misn.amount) 
            IS NULL THEN 0
            ELSE sum(ssim_misn.amount) END
            FROM ssim_misn 
            WHERE ssim_misn.pilot = ssim_pilot.id 
            AND ssim_misn.status = 'T') 
          AS misncargo,
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
      $db->query('UPDATE ssim_pilot
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
    $db->query('UPDATE ssim_pilot
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
      //return $db->rowcount();
      return array(
      "message"=>"$credits cr. have been added to your account.",
      "level"=>"normal"
    );
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
    $db->query("SELECT (SELECT 
    CASE WHEN 
    sum(ssim_cargopilot.amount)
    IS NULL THEN 0
    ELSE sum(ssim_cargopilot.amount) END
    FROM ssim_cargopilot 
    WHERE ssim_cargopilot.pilot = ssim_pilot.id) 
    AS commodcargo,
    (SELECT
      CASE WHEN
      sum(ssim_misn.amount) 
      IS NULL THEN 0
      ELSE sum(ssim_misn.amount) END
      FROM ssim_misn 
      WHERE ssim_misn.pilot = ssim_pilot.id 
      AND ssim_misn.status = 'T') 
    AS misncargo,
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

  public function setGovt($id) {
    $db = new database();
    $db->query("UPDATE ssim_pilot SET govt = :id
      WHERE ssim_pilot.id = :pilot");
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
    $db->query("SELECT `value` FROM ssim_piloterrata
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
    $db->query("UPDATE ssim_pilot SET status = 'S'
      WHERE UNIX_TIMESTAMP(jumpeta) < UNIX_TIMESTAMP(NOW())
      AND status = 'J'");
    $db->execute();

  }
}