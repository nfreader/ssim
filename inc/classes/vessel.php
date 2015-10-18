<?php 

class vessel {

  public $name;
  public $ship;
  public $fuel;
  public $registration;
  public $fuelGauge;

  public function __construct($id=null) {
    if (isset($id)) {
      $vessel = $this->getVessel($id);
      $this->name = $vessel->name;
      $this->registration = $vessel->registration;
      $this->fuel = $vessel->fuel;
      $this->ship = new ship($vessel->ship);
      $this->fuelPercent = ($this->fuel/$this->ship->fueltank) * 100;
      $this->fuelGauge = meter("Fuel", 25, $this->ship->fueltank,$this->fuelPercent);
    }
  }

  public function newVessel($name,$registration,$ship,$pilot=null) {
    $return = '';
    $regFee = 50;
    $ship = new ship($ship);
    $pilot = new pilot(true);
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

    if (!$pilot->deductCredits($ship->cost+$regFee)) {
      return returnError("You cannot afford this ship.");
    }

    if ($pilot->status = 'F') {
      $pilot->setStatus('L');
    }

    $db = new database();
    $db->query("INSERT INTO tbl_vessel
      (pilot, name, ship, fuel, registration) VALUES
      (?, ?, ?, ?, ?)");
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

    $pilot->setVessel($this->getVesselByRegistration($registration));

    $return.= returnSuccess("You purchased a $ship->shipwright $ship->name for ".credits($ship->cost+$regFee));
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

  public function isUnique($name, $registration) {
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

  
}