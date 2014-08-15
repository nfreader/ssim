<?php 

class commod {
  //IT'S HAPPENING

  public function __construct($single=null) {
    if ($single === null) {
      return; //We're going after ALL the commodities.
    } else {
      //Set some properties!
    }
  }

  /* getCommods
   *
   * Surprise! Gets a list of commods from the database.
   * 
   * @return (obj) An array of objects of commods for you to have your way with
   *
   */

  public function getCommods() {
    $db = new database();
    $db->query("SELECT * FROM ssim_commod WHERE class != 'D'");
    $db->execute();
    return $db->resultset();
  }

  /* getCommod
   *
   * Surprise! Gets a single commod from the database
   * 
   * @return (obj) The commod data in object format.
   *
   */

  public function getCommod($commod) {
    $db = new database();
    $db->query("SELECT * FROM ssim_commod
      WHERE class != 'D' AND id = :id");
    $db->bind(':id',$commod);
    $db->execute();
    return $db->single();
  }

  /* spamCommods
   *
   * Loops through the list of spobs and adds records for all the commodities
   * match the spob techlevel.
   * 
   *
   * @return (string) A message indicating how many commods were added to spobs
   *
   */

  public function spamCommods() {
    $spob = new spob();
    $spobs = $spob->getSpobs();
    $commods = $this->getCommods();
    $db = new database();
    $db->query("INSERT IGNORE INTO ssim_commodspob (spob, commod, supply)
      VALUES (:spob, :commod, :supply)");
    foreach ($spobs as $spob) {
      foreach ($commods as $commod) {
        if ($commod->techlevel <= $spob->techlevel) {
          $db->bind(':spob',$spob->id);
          $db->bind(':commod',$commod->id);
          $db->bind(':supply',floor(rand(500,1000)));
          $db->execute();
        }
      }
      return "Added ".$db->rowCount()." commodity records.";
    }
  }

  /* spamCommod
   *
   * Loops through the list of spobs and adds records for spobs that meet the 
   * commod techlevel.
   * 
   *
   * @return (string) A message indicating how many commods were added. Will
   * Will return a number even IF no new records were added.
   *
   */

  public function spamCommod($commod) {
    $spob = new spob();
    $spobs = $spob->getSpobs();
    $commod = $this->getCommod($commod);
    $db = new database();
    $db->query("INSERT IGNORE INTO ssim_commodspob (spob, commod, supply)
      VALUES (:spob, :commod, :supply)");
    foreach ($spobs as $spob) {
      if ($commod->techlevel <= $spob->techlevel) {
        $db->bind(':spob',$spob->id);
        $db->bind(':commod',$commod->id);
        $db->bind(':supply',floor(rand(500,1000)));
        $db->execute();
      }
    }
    return "Added ".$db->rowCount()." commodity records.";
  }

  /* addCommod
   *
   * Does what it says on the tin. 
   *
   * @name (string) Name of the commodity
   * @techlevel (int) Minimum techlevel where this commodity is available
   * @baseprice (int) The starting price for determining the commod's price
   * based on various spob factors
   * @type (string) Allowed values are R(egular), S(pecial), M(ission)
   * and D(isabled)
   *
   * @return (string) A message indicating that the commodity was added, or a
   * message telling you it failed if there was an error.
   *
   */

  public function addCommod($name, $techlevel, $baseprice, $type) {
    $db = new database();
    $db->query("INSERT INTO ssim_commod (name, techlevel, baseprice, class)
      VALUES (:name,:techlevel, :baseprice, :type)");
    $db->bind(':name',$name);
    $db->bind(':techlevel',$techlevel);
    $db->bind(':baseprice',$baseprice);
    $db->bind(':type',$type);
    if($db->execute()){
      return $name." was added as a commodity!";
    } else {
      return "Error! Unable to add ".$name."!";
    }
  }

  /* disableCommod
   *
   * Changes a commod to the 'D' type for disabled (Hidden)
   *
   * TODO: Disallow if the commodity is active, get commodity name
   * 
   * @commod (int) The ID of the commod to be disabled. 
   *
   * @return (string) A message indicating that the commodity was disabled.
   *
   */

  public function disableCommod($commod) {
    $db = new database();
    $db->query("UPDATE ssim_commod SET class='D' WHERE id = :commod");
    $db->bind(':commod',$commod);
    if ($db->execute()) {
      return "Commod was disabled!";
    } else {
      return "Something went wrong! Unable to disable commodity!";
    }
  }

  public function getCommodAvgs() {
    $db = new database();
    $db->query("SELECT ssim_commod.*,
      COUNT(ssim_spob.id) AS spobs,
      SUM(ssim_commodspob.supply) AS totalsupply,
      SUM(ssim_commodspob.supply) / COUNT(ssim_spob.id) AS avgsupply,
      AVG(ssim_commod.baseprice * (ssim_commod.techlevel/ssim_spob.techlevel)/
        ssim_commodspob.supply) * 1000 AS price
      FROM ssim_commod
      LEFT JOIN ssim_commodspob ON ssim_commodspob.commod = ssim_commod.id
      LEFT JOIN ssim_spob ON ssim_commodspob.spob = ssim_spob.id
      WHERE ssim_commod.class = 'R'
      GROUP BY ssim_commod.id");
    $db->execute();
    return $db->resultset();
  }

  public function getSpobCommodData($spob,$commod=null) {
    if ($commod === null) {
      $db = new database();
      $db->query("SELECT ssim_commod.*,
        ssim_commodspob.*,
        ssim_commod.baseprice * (ssim_commod.techlevel/ssim_spob.techlevel)
        / ssim_commodspob.supply * 1000 AS price
        FROM ssim_commod
        LEFT JOIN ssim_commodspob ON ssim_commod.id = ssim_commodspob.commod
        LEFT JOIN ssim_spob ON ssim_commodspob.spob = ssim_spob.id
        WHERE ssim_commod.class = 'R'
        AND ssim_commodspob.spob = :spob
        GROUP BY ssim_commod.id");
      $db->bind(':spob',$spob);
      $db->execute();
      return $db->resultset();
    } else {
      $db = new database();
      $db->query("SELECT ssim_commod.*,
        ssim_commodspob.*,
        ssim_commod.baseprice * (ssim_commod.techlevel/ssim_spob.techlevel)
        / ssim_commodspob.supply * 1000 AS price
        FROM ssim_commod
        LEFT JOIN ssim_commodspob ON ssim_commod.id = ssim_commodspob.commod
        LEFT JOIN ssim_spob ON ssim_commodspob.spob = ssim_spob.id
        WHERE ssim_commod.class = 'R'
        AND ssim_commodspob.spob = :spob
        AND ssim_commodspob.commod = :commod
        GROUP BY ssim_commod.id");
      $db->bind(':spob',$spob);
      $db->bind(':commod',$commod);
      $db->execute();
      return $db->single();      
    }
  }

  public function subtractSpobCommod($spob,$commod,$amount) {
    $db = new database();
    $db->query("UPDATE ssim_commodspob SET supply = supply - :amount
      WHERE commod = :commod
      AND spob = :spob");
    $db->bind(':spob',$spob);
    $db->bind(':commod',$commod);
    $db->bind(':amount',$amount);
    if ($db->execute()) {
      return true;
    }
  }

  public function addSpobCommod($spob,$commod,$amount) {
    $db = new database();
    $db->query("UPDATE ssim_commodspob SET supply = supply + :amount
      WHERE commod = :commod
      AND spob = :spob");
    $db->bind(':spob',$spob);
    $db->bind(':commod',$commod);
    $db->bind(':amount',$amount);
    if ($db->execute()) {
      return true;
    }
  }

  public function buyCommod($commod, $amount) {
    if($amount <= 0) {
      return "Unable to purchase cargo. Temporal anomaly detected.";
    }
    $pilot = new pilot();
    $commod = $this->getSpobCommodData($pilot->pilot->spob,$commod);
    $finalcost = floor($commod->price * $amount);
    //Two areas need to be checked:
      //1. Is this commod available here?
    if (!$commod){
      return "Unable to purchase cargo. Not available here.";
    } 
      //2. Is there enough supply to fill the order?
    if ($commod->supply < $amount){
      return "Unable to purchase that much cargo. Insufficent supply.";
    }
    //and...
      //3. Does the pilot have enough room?
    if ($pilot->capacity < $amount){
      return "Unable to purchase cargo. Insufficent space.";
    } 
      //4. Does the pilot have enough money?
    if ($pilot->credits < $finalcost) {
      return "Unable to purchase cargo. Insufficent funds.";
    }
    //If those checks pass, we need to:
      //1. Remvoe the $amount from commodspob
    $this->subtractSpobCommod($pilot->pilot->spob, $commod->id, $amount);
      //2. Add $amount it to the matching cargopilot row (or create it)
    $pilot->newPilotCargo($commod->id,$amount);
      //3. Remove the credits from the player, including applicable taxes
    $pilot->deductCredits($finalcost);
      //4. Generate a receipt and store the transaction
    return "Purchased $amount tons of $commod->name for $finalcost credits!";
  }

  public function sellCommod($commod, $amount) {
    //Unlike buying, all we have to check is whether or not this commod can be
    //sold here.

    //But the pilot side is more complicated. We have to move the commod from
    //cargopilot to commodspob and then credit the pilot. ALSO, if they're
    //inside the one week I.C.T. period, hit 'em in the legal. And then see if
    //their legal rating qualifies them for pirate (which we can check in the
    //$pilot->reduceLegal() method. SO COMPLICATED!
    if ($amount < 0) {
      return "Unable to sell cargo. Temporal anomaly detected.";
    }
    $pilot = new pilot(true, true);
    $cargo = $this->getPilotCommods($pilot->pilot->spob,$commod);
    $commod = $this->getSpobCommodData($pilot->pilot->spob, $commod);
    if (!$commod || !$cargo) {
      return "This can't be sold here!";
    }
    $this->addSpobCommod($pilot->pilot->spob, $commod->id, $amount);
    $finalcost = floor($commod->price * $amount);
    if(($pilot->pilot->syst === $cargo->lastsyst)
      && ($cargo->is_legal == false)) {
      $pilot->subtractLegal($amount * floor(rand(1, CARGO_PENALTY)));
    }
    $pilot->addCredits($finalcost);
    $pilot->subtractPilotCargo($commod->id,$amount);
    return "Sold $amount tons of ".$commod->name." for $finalcost credits.";

  }

  public function getPilotCommods($spob,$commod=null) {
    $db = new database();
    if ($commod === null) {
      $pilot = new pilot(true, true);
      $db->query("SELECT ssim_commod.*,
          ssim_commodspob.*,
          ssim_commod.baseprice * (ssim_commod.techlevel/ssim_spob.techlevel)
          / ssim_commodspob.supply * 1000 AS price,
          ssim_cargopilot.amount,
          ssim_cargopilot.lastsyst,
          ssim_cargopilot.lastchange,
          CASE WHEN ssim_cargopilot.lastchange + INTERVAL 7 DAY < NOW()
          THEN 1
          ELSE 0
          END AS is_legal
          FROM ssim_commod
          LEFT JOIN ssim_commodspob ON ssim_commod.id = ssim_commodspob.commod
          LEFT JOIN ssim_spob ON ssim_commodspob.spob = ssim_spob.id
          LEFT JOIN ssim_cargopilot ON ssim_cargopilot.commod = ssim_commod.id
          WHERE ssim_commod.class = 'R'
          AND ssim_commodspob.spob = :spob
          AND ssim_cargopilot.pilot = :pilot
          AND ssim_cargopilot.amount > 0
          GROUP BY ssim_commod.id");
      $db->bind(':spob',$spob);
      $db->bind(':pilot',$pilot->pilot->id);
      $db->execute();
      return $db->resultSet();
    } else {
      $pilot = new pilot(true, true);
      $db->query("SELECT ssim_commod.*,
          ssim_commodspob.*,
          ssim_commod.baseprice * (ssim_commod.techlevel/ssim_spob.techlevel)
          / ssim_commodspob.supply * 1000 AS price,
          ssim_cargopilot.amount,
          ssim_cargopilot.lastsyst,
          ssim_cargopilot.lastchange,
          CASE WHEN ssim_cargopilot.lastchange + INTERVAL 7 DAY < NOW()
          THEN 1
          ELSE 0
          END AS is_legal
          FROM ssim_commod
          LEFT JOIN ssim_commodspob ON ssim_commod.id = ssim_commodspob.commod
          LEFT JOIN ssim_spob ON ssim_commodspob.spob = ssim_spob.id
          LEFT JOIN ssim_cargopilot ON ssim_cargopilot.commod = ssim_commod.id
          WHERE ssim_commod.class = 'R'
          AND ssim_commodspob.spob = :spob
          AND ssim_cargopilot.pilot = :pilot
          AND ssim_commod.id = :commod
          GROUP BY ssim_commod.id");
      $db->bind(':spob',$spob);
      $db->bind(':pilot',$pilot->pilot->id);
      $db->bind(':commod',$commod);
      $db->execute();
      return $db->single();
    }
  }

}