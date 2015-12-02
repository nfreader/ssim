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
  public $jumpeta;
  public $remaining;

  public $spobname;
  public $spobtype;
  public $systname;
  public $canRefuel;

  public $flags;

  public $govt;

  public $fullstatus;

  public $cargo;

  public function __construct($uid=NULL,$short=FALSE) {
    if (NULL === $uid) {
      $uid = $_SESSION['pilotuid'];
    } elseif (FALSE === $uid) {
      return;
    }

    if (isset($uid)) {
      $pilot = $this->getPilot($uid);

      $this->name = $pilot->name;
      $this->uid = $pilot->uid;
      $this->fingerprint = $pilot->fingerprint;
      $this->credits = $pilot->credits;
      $this->legal = $pilot->legal;
      $this->status = $pilot->status;
      $this->spob = $pilot->spob;
      $this->syst = $pilot->syst;
      $this->vessel = $pilot->vessel;
      $this->location = $pilot->location;
      $this->jumpeta = $pilot->jumpeta;
      $this->remaining = $pilot->remaining;
      $this->govt = $pilot->govt;
      if($this->jumpeta <= time() && 'B' == $this->status){
        $this->jumpComplete();
        $this->setStatus('S');
      }

      $this->spobname = spobName($pilot->spobname,$pilot->spobtype);
      $this->spobtype = $pilot->spobtype;
      $this->systname = $pilot->systname;

      if (FALSE === $short) {
        $this->vessel = new vessel($pilot->vessel);
        $this->govt = new govt($pilot->govt,FALSE);

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

        $this->cargo = $this->getPilotCargoStats($this->uid);

        $this->flags = new stdclass();

        $this->flags->canRefuel = FALSE;
        if (100 > $this->vessel->fuelPercent && 'L' == $this->status) {
          $this->flags->canRefuel = TRUE;
        }

        if ('L' == $this->status && isset($this->spob)) {
          $this->flags->isLanded = TRUE;
        } else {
          $this->flags->isLanded = FALSE;
        }

        if ('L' == $this->status && isset($this->spob)) {
          $this->flags->canLiftoff = TRUE;
        }

        if ($this->vessel->fuel >= 1 && !$this->flags->isLanded && 'S' == $this->status) {
          $this->flags->canJump = TRUE;
        } else {
          $this->flags->canJump = FALSE;
        }

        if ('S' == $this->status){
          $this->flags->canLand = TRUE;
        }

        $this->flags->canHack = FALSE;
        if($this->canHack()) {
          $this->flags->canHack = TRUE;
        }

        if ($pilot->newmsgs) {
          $this->flags->newMessages = TRUE;
        } else {
          $this->flags->newMessages = FALSE;
        }

        $commod = new commod();
        if ($this->flags->isLanded) {
        $this->cargo->commods = $commod->getPilotCommods($this->uid,$this->spob);
        } else {
          $this->cargo->commods = $commod->getPilotCargoCommods($this->uid);
        }
        $this->outfits = $this->getPilotOutfits();
        //This was moved to view/outfit/outfitter since it duplicates data
        //if we need to do this again, we can just uncomment this line
        //$this->outfits = array_merge($this->outfits,$this->vessel->outfits);
      }
    }
  }

  public function getPilot($uid) {
    $db = new database();
    $db->query("SELECT tbl_pilot.*,
      tbl_spob.name AS spobname,
      tbl_spob.type AS spobtype,
      tbl_syst.name AS systname,
      tbl_vessel.name AS vesselname,
      tbl_vessel.ship AS shipid,
      UNIX_TIMESTAMP(tbl_pilot.jumpeta) - UNIX_TIMESTAMP(NOW()) AS remaining,
      CASE WHEN tbl_pilot.status = 'L' THEN tbl_pilot.spob
      ELSE tbl_pilot.syst
      END AS location,
      IF (tbl_message.read = 0, TRUE, FALSE) AS newmsgs
      FROM tbl_pilot
      LEFT JOIN tbl_spob ON tbl_pilot.spob = tbl_spob.id
      LEFT JOIN tbl_syst ON tbl_pilot.syst = tbl_syst.id
      LEFT JOIN tbl_vessel ON tbl_pilot.vessel = tbl_vessel.id
      LEFT JOIN tbl_message ON tbl_pilot.uid = tbl_message.msgto
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
    if (1 < $db->single()->count) {
      return returnError("Only one pilot per player allowed.");
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
    $game = new game();
    $game->logEvent('AP',"$activated->name activated by $user->uid");
    return $activated;
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
    $db->bind(':pilot',$this->uid);
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
    $return = '';
    if(!$this->flags->canRefuel) {
      return returnError("You must dock or land before you can refuel");
    }
    $db = new database();
    //Get the fuel cost
    $spob = new spob($this->spob);
    //Determine how much fuel we need
    $diff = $this->vessel->ship->fueltank - $this->vessel->fuel;
    if ($diff <= 0) {
      return returnMessage("You cannot refuel at this time.");
    }

    if ($this->credits >= ($spob->fuelcost * $diff)) {
      $units = $diff;
    } else {
      $units = ($this->credits - ($this->credits % $spob->fuelcost)) / $spob->fuelcost;
    }

    if (0 == $units) {
      return returnError("Can't refuel. Not enough credits.");
    }

    $cost = floor($units * $spob->fuelcost);

    $this->deductCredits($cost);
    $vessel = new vessel($this->vessel->id);
    $vessel->addFuel($units);

    $game = new game();
    $game->logEvent("R","Refueled $units for $cost at $this->spobname ($this->spob)");
    return returnSuccess("Refueled ".singular($units,'fuel unit','fuel units')." for $cost cr.");
  }

  public function liftoff(){
    if($this->flags->isLanded && $this->flags->canLiftoff) {
      $db = new database();
      $syst = new syst($this->syst);
      $this->setStatus('S');

      $db = new database();
      $db->query("UPDATE tbl_pilot SET spob = NULL WHERE uid = ?");
      $db->bind(1,$this->uid);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      $game = new game();
      $game->logEvent("LO","Lifted off from $this->spobname ($this->spob)");
      return returnSuccess("Lifted off from $this->spobname.");
    } else {
      return returnError("Unable to liftoff.");
    }
  }

  public function land($spob) {
    $spob = new spob($spob);
    if (($spob->parent->id != $this->syst) || !$this->flags->canLand) {
      return returnError("Unable to land on $spob->name.");
    }
    $this->setStatus('L');
    $db = new database();
    $db->query('UPDATE tbl_pilot SET spob = ? WHERE uid = ?');
    $db->bind(1, $spob->id);
    $db->bind(2, $this->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $game = new game();
    $game->logEvent("LA","Landed on $spob->name ($spob->id)");
    return returnSuccess("You have ".landVerb($spob->type, 'past')." $spob->fullname");
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
      return TRUE;
    } else {
      return FALSE;
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
    return true;
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
        SET legal = legal - ?
        WHERE uid = ?");
      $db->bind(1,$legal);
      $db->bind(2,$this->uid);
      $db->execute();
      //return $db->rowcount();
      $game = new game();
      $game->logEvent("SL","Lost $legal legal points.");
      return returnMessage("Legal reduced by $legal points");
    }
  }

  public function jump($target) {
    if (!$this->flags->canJump) {
      return returnError("Unable to initiate bluespace jump");
    }
    $jump = new syst();
    $jump = $jump->getJumpData($target,$this->syst);
    if (!$jump) {
      return returnError("Invalid bluespace coordinates");
    }
    $vessel = new vessel($this->vessel->id);
    $vessel->subtractFuel(1);
    $this->setStatus('B');
    $eta = $jump->distance/2;

    $db = new database();
    if(TRUE === SSIM_DEBUG){
      $db->query("UPDATE tbl_pilot SET jumpstarted = NOW(),
        jumpeta = DATE_ADD(NOW(),INTERVAL ? SECOND), syst = ? WHERE uid = ?");
    } else {
      $db->query("UPDATE tbl_pilot SET jumpstarted = NOW(),
        jumpeta = DATE_ADD(NOW(),INTERVAL ? HOUR), syst = ? WHERE uid = ?");
    }
    $db->bind(1,$eta);
    $db->bind(2,$jump->dest);
    $db->bind(3,$this->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $game = new game();
    $game->logEvent('J',"Jumped to $jump->dest_name ($jump->dest)");
    return returnSuccess("Bluespace jump to $jump->dest_name initiated. Estimated travel time: ".singular($eta,'hour','hours'));
  }

  public function jumpComplete() {
    if ($this->remaining <= 0) {
      $this->setStatus('S');
      return returnSuccess("Jump to $this->systname complete");
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
      $db->bind(':id',$this->uid);
      if($db->execute()) {
        $game = new game();
        $game->logEvent('RV',"Renamed vessel to $name");
        return returnSuccess("You are now piloting the $name");
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
    $db->bind(':pilot',$this->uid);
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
    $db->bind(':pilot',$this->uid);
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
    $db->bind(':pilot',$this->uid);
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
    $db->bind(':pilot',$this->uid);
    $db->bind(':commod',$commod);
    $db->bind(':amount',$amount);
    $db->bind(':lastsyst',$this->syst);
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
    WHERE ssim_cargopilot.pilot = ssim_pilot.uid) AS commodcargo,
    (SELECT
      CASE WHEN
      sum(ssim_misn.amount)
      IS NULL THEN 0
      ELSE sum(ssim_misn.amount) END
      FROM ssim_misn
      WHERE ssim_misn.pilot = ssim_pilot.uid
      AND ssim_misn.status = 'T') AS misncargo,
    (SELECT commodcargo) + (SELECT misncargo) AS cargo,
    ssim_ship.cargobay,
    ssim_ship.cargobay - (SELECT cargo) AS capacity,
    floor(((SELECT cargo) / ssim_ship.cargobay) * 100) AS cargometer
    FROM ssim_pilot
    LEFT JOIN ssim_vessel ON ssim_pilot.vessel = ssim_vessel.id
    LEFT JOIN ssim_ship ON ssim_vessel.ship = ssim_ship.id
    WHERE ssim_pilot.uid = ?
    GROUP BY ssim_pilot.uid");
    if(isset($this->uid)){
      $db->bind(1, $this->uid);
    } else {
      $db->bind(1, $id);
    }
    $db->execute();
    return $db->single();
  }

  public function setGovt($id) {
    $db = new database();
    $db->query("UPDATE tbl_pilot SET govt = :id
      WHERE tbl_pilot.id = :pilot");
    $db->bind(':id',$id);
    $db->bind(':pilot',$this->uid);
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
      $message->newSystemMessage($this->uid,'Legal Notice',$msg);
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
    $db->bind(':pilot',$this->uid);
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

  public function getPilotList() {
    $db = new database();
    $db->query("SELECT name, uid FROM tbl_pilot WHERE status != 'D'");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }

  public function getOutfit($outfit) {
    $db = new database();
    $db->query("SELECT * FROM tbl_pilotoutf WHERE outfit = ? AND pilot = ?");
    $db->bind(1,$outfit);
    $db->bind(2,$this->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function hasOutfit($outfit) {
    $has = $this->getOutfit($outfit);
    if (!$has){
      return false;
    }
    if ($has->quantity == 0) {
      return false;
    } else {
      return true;
    }
  }

  public function getPilotOutfits(){
    $db = new database();
    $db->query("SELECT tbl_outf.*,
    tbl_pilotoutf.*
    FROM tbl_pilotoutf
    LEFT JOIN tbl_outf ON tbl_outf.id = tbl_pilotoutf.outfit
    WHERE tbl_pilotoutf.pilot = ?
    AND tbl_pilotoutf.quantity > 0");
    $db->bind(1,$this->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function canHack() {
    $db = new database();
    $db->query("SELECT IF (ssim_pilotoutf.quantity > 0, TRUE, FALSE) AS canhack
    FROM ssim_pilotoutf
    LEFT JOIN ssim_outf ON ssim_pilotoutf.outfit = ssim_outf.id
    WHERE ssim_outf.type = 'H'
    AND ssim_pilotoutf.pilot = ?;");
    $db->bind(1,$this->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    if($db->single()->canhack) {
      return true;
    }
    return false;
  }

  public function ping($uid) {
    $db = new database();
    $db->query("SELECT * FROM tbl_ping WHERE pilot = ? LIMIT 0,1");
    $db->bind(1,$uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $ping = $db->single();
    if ($ping) {
      $db->query("DELETE FROM tbl_ping WHERE pilot = ?");
      $db->bind(1,$uid);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      return $ping;
    } else {
      return false;
    }
  }

  public function sendPing($pilot,$key,$value) {
    $db = new database();
    $db->query("INSERT INTO tbl_ping (pilot, `key`, `value`, timestamp)
    VALUES (?, ?, ?, NOW())");
    $db->bind(1,$pilot);
    $db->bind(2,$key);
    $db->bind(3,$value);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function leaveGovt() {
    $govt = new govt(NULL);
    $db = new database();
    $db->query("UPDATE tbl_pilot SET govt = ? WHERE uid = ?");
    $db->bind(1,$govt->getIndieGovt());
    $db->bind(2,$this->uid);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $return = $govt->revokeMembership($this->govt,$this->uid);
    $return.= returnSuccess("You have left your government. You are now marked as an independent pilot.");
    return $return;
  }

  public function joinGovt($govt){
    $govt = new govt($govt,TRUE);
    if ('R' != $govt->type){
      return returnError("You cannot join this government");
    }
    //This is the only member of the government,
    //so they get to be the president automatically.
    if (0 == $govt->totalpilots) {
      $db = new database();
      $db->query("UPDATE tbl_pilot SET govt = ? WHERE uid = ?");
      $db->bind(1,$govt->id);
      $db->bind(2,$this->uid);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      $return = returnSuccess("You have joined the $govt->name");
      $return.= $govt->declareNewLeader($govt->id,$this->uid);
      return $return;
    }
  }

  private function forceJumpCompletion() {
    //Sanity check: If a pilot starts jumping and logs out in mid-jump,
    //They'll stay in space until they log back in. This forces all pilots
    //with expired jump times to land.
    $db = new database();
    $db->query("UPDATE tbl_pilot SET status = 'S'
      WHERE jumpeta < NOW()
      AND status = 'B'");
    $db->execute();
  }
}
