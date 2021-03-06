<?php

class vessel {

  public $id;
  public $name;
  public $fuel;
  public $registration;
  public $shielddam;
  public $armordam;
  public $expanson;

  public $ship;

  public $fuelGauge;
  public $fuelPercent;
  public $shieldGauge;
  public $armorGauge;

  public $expansionSpace;

  public $outfits;

  public function __construct($id=null) {
    if ($id) {
      $vessel = $this->getVessel($id);
      $vessel->ship = new ship($vessel->ship);
      $vessel->outfits = $this->getVesselOutfits($vessel->id);
      $vessel->beacons = $this->canVesselLaunchBeacons($vessel->id);
      $vessel = $this->parseVessel($vessel);
      foreach ($vessel as $key => $value){
        $this->$key = $value;
      }
    }
  }

  public function parseVessel(&$vessel) {
    $vessel->fuelPercent = ($vessel->fuel/$vessel->ship->fueltank) * 100;
    $label = "Fuel (".$vessel->fuel."/".$vessel->ship->fueltank." jumps remaining)";
    $vessel->fuelGauge = meter($label, 25, $vessel->fuelPercent);

    $percent = (($vessel->ship->shields - $vessel->shielddam)/$vessel->ship->shields) * 100;
    $label = "Shields";
    $vessel->shieldGauge = meter($label, 25, $percent);

    $percent = (($vessel->ship->armor - $vessel->armordam)/$vessel->ship->armor) * 100;
    $label = "Hull Integrity";
    $vessel->armorGauge = meter($label, 25, $percent);

    $vessel->expansionSpace = $vessel->ship->expansion - $vessel->expansion;

    return $vessel;
  }

  public function newVessel($name,$registration,$ship,$pilot=null) {
    $return = '';
    $regFee = 50;
    $value = 0;
    $ship = new ship($ship);
    $pilot = new pilot();
    if (isset($pilot->vessel)) {
      $value = $this->getTradeInValue($pilot->vessel->id);
    }
    if (!$ship) {
      return returnError("Purchase error. Cannot find requested ship.");
    }
    $name = filter_var($name,FILTER_SANITIZE_STRING,FILTER_FLAG_ENCODE_LOW);
    if ('' === empty($name)) {
      return returnError("Vessel name invalid.");
    }

    if (!$this->checkRegistration($registration)) {
      $registration = $this->generateRegistration();
      $regFee = 0;
    }

    if (!$this->isUnique($name,$registration)) {
      return returnError("Vessel name or registration already in use.");
    }

    $return.= returnMessage("Your registration number is: $registration");

    if (!$pilot->deductCredits($ship->cost+$regFee-$value)) {
      return returnError("You cannot afford this ship.");
    }

    if ($pilot->status = 'F') {
      $pilot->setStatus('L');
    }

    $db = new database();
    $db->query("INSERT INTO tbl_vessel
      (pilot, name, ship, fuel, registration, purchased) VALUES
      (?, ?, ?, ?, ?, NOW())");
    $db->bind(1,$pilot->uid);
    $db->bind(2,$name);
    $db->bind(3,$ship->id);
    $db->bind(4,$ship->fueltank); //Complimentary full tank!
    $db->bind(5,$registration);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

    if ($value > 0) {
      $this->disableVessel($pilot->vessel->id);
      $return.= returnMessage("Previous ship traded in for ".credits($value));
    }

    $pilot->setVessel($this->getVesselByRegistration($registration));
    $game = new game();
    $game->logEvent("BS","Purchased a $ship->shipwright $ship->name for ".$ship->cost+$regFee-$value);
    $return.= returnSuccess("You purchased a $ship->shipwright $ship->name for ".credits($ship->cost+$regFee-$value));
    return $return;
  }

  public function getVessel($id) {
    $db = new database();
    $db->query("SELECT * FROM tbl_vessel WHERE id = ?");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function isUnique($name, $registration=null) {
    $db = new database();
    $db->query("SELECT COUNT(*) AS count
      FROM tbl_vessel WHERE name = :name OR registration = :registration");
    $db->bind(':name', $name);
    $db->bind(':registration', $registration);
    $db->execute();
    if (0 == $db->single()->count) {
      return true;
    } else {
      return false;
    }
  }


  public function checkRegistration($registration) {
    $registration = preg_replace('/[^\w-]/', '', strtoupper($registration));
    if ('' == $registration || 9 != strlen($registration)) {
      return false;
    }
    return $registration;
  }

  public function generateRegistration() {
    $reg = strtoupper(substr(sha1(time().date('now').rand(0,10)),0,9));
    if ($this->isUnique('',$reg)) {
      return $reg;
    } else {
      return strtoupper(substr(sha1(time().date('now').rand(0,10)),0,9));
    }
  }

  public function getVesselByRegistration($registration) {
    $db = new database();
    $db->query("SELECT id FROM tbl_vessel WHERE registration = ?");
    $db->bind(1,$registration);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single()->id;
  }

  public function getTradeInValue($vessel) {
    /* TODO:
      - Account for date of purchase
      - Account for outfits
      - Account for repair status
    */
    $vessel = $this->getVessel($vessel);
    $ship = new ship($vessel->ship);
    $value = $ship->cost * .50;
    return $value;
  }

  public function disableVessel($vessel) {
    $db = new database();
    $db->query("UPDATE tbl_vessel SET status = 'D', pilot = NULL
      WHERE id = ?");
    $db->bind(1, $vessel);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function addFuel($units) {
    $db = new database();
    $db->query("UPDATE tbl_vessel SET fuel = fuel + ? WHERE id = ?");
    $db->bind(1,$units);
    $db->bind(2,$this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function subtractFuel($units) {
    $db = new database();
    $db->query("UPDATE tbl_vessel SET fuel = fuel - ? WHERE id = ?");
    $db->bind(1,$units);
    $db->bind(2,$this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function renameVessel($name) {
    $pilot = new pilot(NULL);
    $name = filter_var($name,FILTER_SANITIZE_STRING,FILTER_FLAG_ENCODE_LOW);
    if ('' == trim($name) || '' == $name) {
      return returnError("Vessel name invalid.");
    }
    if (!$this->isUnique($name)) {
      return returnError("Vessel name already in use.");
    }
    $db = new database();
    $db->query("UPDATE tbl_vessel SET name = ? WHERE id = ?");
    $db->bind(1,$name);
    $db->bind(2,$pilot->vessel->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $game = new game();
    $game->logEvent('RV',"Renamed vessel to $name");
    return returnSuccess("Vessel renamed to $name");
  }

  public function subtractExpansionSpace($size) {
    $db = new database();
    $db->query("UPDATE tbl_vessel SET expansion = expansion + ?
      WHERE id = ?");
    $db->bind(1,$size);
    $db->bind(2,$this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function addExpansionSpace($size) {
    $db = new database();
    $db->query("UPDATE tbl_vessel SET expansion = expansion - ?
      WHERE id = ?");
    $db->bind(1,$size);
    $db->bind(2,$this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getOutfit($outfit) {
    $db = new database();
    $db->query("SELECT * FROM tbl_vesseloutf WHERE outfit = ? AND vessel = ?");
    $db->bind(1,$outfit);
    $db->bind(2,$this->id);
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

  public function getVesselOutfits($id=null){
    if (isset($this->id)){
      $id = $this->id;
    } else {
      $id = $id;
    }
    $db = new database();
    $db->query("SELECT tbl_outf.*,
    tbl_vesseloutf.*,
    IF (tbl_outf.ammo IS NOT NULL, ammo.quantity, 0) AS rounds
    FROM tbl_vesseloutf
    LEFT JOIN tbl_outf ON tbl_outf.id = tbl_vesseloutf.outfit
    LEFT JOIN tbl_vesseloutf AS ammo ON tbl_outf.ammo = ammo.outfit
    WHERE tbl_vesseloutf.vessel = ?
    AND tbl_vesseloutf.quantity > 0
    GROUP BY tbl_outf.id");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getVesselOutfitByType($type, $subtype=null) {
    $db = new database();
    if($subtype){
      $db->query("SELECT * FROM tbl_vesseloutf
        LEFT JOIN tbl_outf ON tbl_vesseloutf.outfit = tbl_outf.id
        WHERE tbl_outf.type = ?
        AND tbl_outf.subtype = ?");
      $db->bind(1,$tpye);
      $db->bind(2,$subtype);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      return $db->resultset();
    }
    else {
    $db->query("SELECT * FROM tbl_vesseloutf
      LEFT JOIN tbl_outf ON tbl_vesseloutf.outfit = tbl_outf.id
      WHERE tbl_outf.type = ?");
      $db->bind(1,$type);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      return $db->single();
    }
  }

  public function canVesselLaunchBeacons(){
    $db = new database();
    $db->query("SELECT tbl_outf.name,
      IF(tbl_vesseloutf.quantity > 0, TRUE, FALSE) AS can,
      ammo.quantity AS rounds
      FROM tbl_vesseloutf
      LEFT JOIN tbl_outf ON tbl_vesseloutf.outfit = tbl_outf.id
      LEFT JOIN tbl_vesseloutf AS ammo ON tbl_outf.ammo = ammo.outfit
      WHERE tbl_outf.type = 'B'
      AND tbl_outf.subtype = 'L'
      AND tbl_vesseloutf.vessel = ?
      AND ammo.quantity > 0;");
    $db->bind(1,$this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getCombatStats($id) {
    //We'll need the outfits for this
    //$db = new database();
    //$db->query("");
  }


}
