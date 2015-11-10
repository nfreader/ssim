<?php

class outfit {

  public function __construct(){}

  public function newOutfit($data) {
    $requirements = array(
      'name',
      'size',
      'cost',
      'type',
      'subtype',
      'modifies',
      'value',
      'reload',
      'ammo',
      'description',
      'techlevel',
      'govt'
    );
    if (methodRequires($requirements,$data)) {
      return returnError("Data malformed. Try again.");
    }
  }

  public function getOutfit($outfit) {
    $db = new database();
    $db->query("SELECT * FROM tbl_outf WHERE id = ?");
    $db->bind(1,$outfit);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getOutfitType($type) {
    switch($type){
      case 'M': //A modifier
      return 'pilot';
      break;

      case 'W': //A weapon
      case 'A': //An addon
      return 'vessel';
      break;
    }
  }

  public function buyOutfit($outfit,$quantity=1) {
    $outfit = $this->getOutfit($outfit);
    //First off, let's decide if this is a PILOT or VESSEL outfit
    //Pilot outfits are attached to the pilot and are carried between vessel
    //changes. Vessel outfits stay with the vessel and are factored into the 
    //trade-in value (TODO)
    $type = $this->getOutFitType($outfit->type);
    //Let's make sure that the pilot is landed. We'll grab a full pilot object
    //Because we need to check a bunch of vessel stuff as well.
    $pilot = new pilot(NULL,TRUE);
    if ('L' != $pilot->status){
      return returnError("You cannot purchase that outfit here.");
    }
    //Now let's make sure the pilot can actually buy this here
    $spob = new spob($pilot->spob);
    if ($outfit->techlevel >= $spob->techlevel) {
      return returnError("Can't purchase this outfit here.");
    }

    //And if they can afford it
    $cost = $outfit->cost * $quantity;

    if($pilot->credits < $cost){
      return returnError("You can't afford this.");
    }

    if ('vessel' === $type){
      //This is a vessel outfit so we need to do an expansion space check
      $vessel = new vessel($pilot->vessel);
      if ($outfit->size > $vessel->expansionSpace) {
        return returnError("You don't have enough expansion space for this.");
      }
      if(!$this->addVesselOutfit($vessel->id,$outfit->id,$quantity)){
        return returnError("Something went wrong when installing the outfit.");
      }
      $vessel->subtractExpansionSpace($outfit->size*$quantity);
    } else {
      $this->addPilotOutfit($pilot->uid,$outfit->id,$quantity);
    }

    $pilot->deductCredits($cost);
    return returnSuccess("You purchased a $outfit->name for ".credits($cost));
  }

  public function sellOutfit($outfit, $quantity=1) {
    $outfit = $this->getOutfit($outfit);
    $type = $this->getOutFitType($outfit->type);
    if ('U' === $outfit->flag) {
      return returnError("This outfit can't be sold.");
    }
    //Make sure the pilot is landed
    $pilot = new pilot(NULL,TRUE);
    if ('L' != $pilot->status){
      return returnError("You cannot sell that outfit here.");
    }
    //Now let's make sure the pilot can actually sell this here
    $spob = new spob($pilot->spob);
    if ($outfit->techlevel >= $spob->techlevel) {
      return returnError("Can't sell that outfit here.");
    }

    if ('vessel' === $type){
      //This is a vessel outfit so we need to do an expansion space check
      $vessel = new vessel($pilot->vessel);
      if(!$this->subtractVesselOutfit($vessel->id,$outfit->id,$quantity)){
        return returnError("Something went wrong when installing the outfit.");
      }
      $vessel->addExpansionSpace($outfit->size*$quantity);
    } else {
      $this->subtractPilotOutfit($pilot->uid,$outfit->id,$quantity);
    }

    //And calculate the cost
    $cost = $this->getTradeInValue($outfit->cost * $quantity);
    $pilot->addCredits($cost);
    return returnSuccess("You sold a $outfit->name for ".credits($cost));
  }

  public function addVesselOutfit($vessel,$outfit,$quantity) {
    $db = new database();
    $db->query("INSERT INTO tbl_vesseloutf
      (vessel, outfit, quantity, timestamp) VALUES (?,?,?, NOW())
      ON DUPLICATE KEY UPDATE quantity = quantity + ?");
    $db->bind(1,$vessel);
    $db->bind(2,$outfit);
    $db->bind(3,$quantity);
    $db->bind(4,$quantity);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return TRUE;
  }
  public function addPilotOutfit($pilot,$outfit,$quantity) {
    $db = new database();
    $db->query("INSERT INTO tbl_pilotoutf
      (vessel, outfit, quantity, timestamp) VALUES (?,?,?, NOW())");
    $db->bind(1,$pilot);
    $db->bind(2,$outfit);
    $db->bind(3,$quantity);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return TRUE;
  }

  public function subtractVesselOutfit($vessel,$outfit,$quantity) {
    $db = new database();
    $db->query("UPDATE tbl_vesseloutf SET quantity = quantity - ? WHERE outfit = ? AND vessel = ?");
    $db->bind(1,$quantity);
    $db->bind(2,$outfit);
    $db->bind(3,$vessel);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return TRUE;
  }
  public function subtractPilotOutfit($pilot,$outfit,$quantity) {
    $db = new database();
    $db->query("UPDATE tbl_pilotoutf SET quantity = quantity - ? WHERE outfit = ? AND pilot = ?");
    $db->bind(1,$quantity);
    $db->bind(2,$outfit);
    $db->bind(3,$pilot);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return TRUE;
  }

  public function getTradeInValue($cost) {
    // TODO: 
    // - Account for date of purchase
    return $cost * .75;
  }

}