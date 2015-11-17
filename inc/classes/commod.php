<?php

class commod {

  public $id;
  public $name;
  public $class;
  public $techlevel;
  public $baseprice;
  public $basesupply;

  public $commodSpob;

  public $fullclass;
  public $avgPrice;
  public $avgSupply;

  public function __construct($id=null,$fast=false) {
    if(isset($id)) {
      $commod = $this->getCommod($id);
      $this->id = $commod->id;
      $this->name = $commod->name;
      $this->class = $commod->class;
      $this->techlevel = $commod->techlevel;
      $this->baseprice = $commod->baseprice;
      $this->basesupply = $commod->basesupply;

      $this->commodSpob = $this->getCommodSpobs($commod->id,FALSE);

      switch($commod->class) {
        case 'R':
          $type = 'Regular';
        break;
        case 'S':
          $type = 'Special';
        break;
        case 'M':
          $type = 'Mission';
        break;
        case 'D':
          $type = 'Disabled';
        break;
      }
      $this->fullclass = $type;

      $price = 0;
      $supply = 0;
      foreach ($this->commodSpob as $spob) {
        $price = $price + $spob->price;
        $supply = $supply + $spob->supply;
      }
      $this->avgPrice = floor($price/count($this->commodSpob));
      $this->avgSupply = floor($supply/count($this->commodSpob));
    }

  }

  public function addBaseCommod($name, $techlevel, $price, $supply=100) {
    $user = new user();
    if (!$user->isAdmin()) {
      $game = new game();
      $game->logEvent('AD','Action denied: addBaseCommod');
      return returnError("You must be an admin for this action");
    }
    if (empty($name) || '' == trim($name)) {
      return returnError("Name is invalid");
    }
    if ($techlevel < 2) {
      return returnError("Techlevel cannot be lower than two!");
    }
    $db = new database();
    $db->query("INSERT INTO tbl_commod
      (name, class, techlevel, baseprice, basesupply)
      VALUES (?, 'R', ?, ?, ?)");
    $db->bind(1, $name);
    $db->bind(2, floor($techlevel));
    $db->bind(3, $price);
    $db->bind(4, $supply);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $game = new game();
    $game->logEvent("AC","Added new base commodity: $name");
    return returnSuccess("Added commodity: $name");
  }

  public function getCommods($full=FALSE) {
    $db = new database();
    if (FALSE === $full) {
      $db->query("SELECT * FROM tbl_commod");
    } else {
      $db->query("SELECT tbl_commod.*,
      COUNT(tbl_spob.id) AS spobs,
      SUM(tbl_commodspob.supply) AS totalsupply,
      SUM(tbl_commodspob.supply) / COUNT(tbl_spob.id) AS avgsupply,
      floor(AVG(tbl_commod.baseprice * (tbl_commod.techlevel/tbl_spob.techlevel)/
        tbl_commodspob.supply) * 1000) AS price
      FROM tbl_commod
      LEFT JOIN tbl_commodspob ON tbl_commodspob.commod = tbl_commod.id
      LEFT JOIN tbl_spob ON tbl_commodspob.spob = tbl_spob.id
      GROUP BY tbl_commod.id");
    }
    $db->execute();
    return $db->resultset();

  }

  public function getSpecialCommods($full=FALSE) {
    $db = new database();
    $db->query("SELECT * FROM tbl_commod WHERE class != 'R'");
    $db->execute();
    return $db->resultset();
  }

  public function getCommod($id) {
    $db = new database();
    $db->query("SELECT * FROM tbl_commod WHERE id = ?");
    $db->bind(1,$id);
    $db->execute();
    return $db->single();
  }

  public function getCommodSpobs($commod,$short=TRUE) {
    $db = new database();
    if(TRUE === $short) {
      $db->query("SELECT * FROM tbl_commodspob WHERE commod = ?");
    } else {
      $db->query("SELECT tbl_commodspob.*,
        tbl_spob.name,
        tbl_spob.techlevel,
        floor(tbl_commod.baseprice * (tbl_commod.techlevel/tbl_spob.techlevel)
        / tbl_commodspob.supply * 1000) AS price
        FROM tbl_commodspob
        LEFT JOIN tbl_spob ON tbl_commodspob.spob = tbl_spob.id
        LEFT JOIN tbl_commod ON tbl_commodspob.commod = tbl_commod.id
        WHERE commod = ?");
    }
    $db->bind(1,$commod);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getSpobCommods($spob) {
    $db = new database();
    $db->query("SELECT ssim_commodspob.*,
      ssim_commod.*,
      floor(ssim_commod.baseprice * (ssim_commod.techlevel/ssim_spob.techlevel)
              / ssim_commodspob.supply * 1000) AS price
      FROM ssim_commodspob
      LEFT JOIN ssim_spob ON ssim_commodspob.spob = ssim_spob.id
      LEFT JOIN ssim_commod ON ssim_commodspob.commod = ssim_commod.id
      WHERE ssim_commodspob.spob = ?");
    $db->bind(1,$spob);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }
  public function spamCommods($id) {
    $user = new user();
    if (!$user->isAdmin()) {
      $game = new game();
      $game->logEvent('AD','Action denied: spamCommods');
      return returnError("You must be an admin for this action");
    }
    $commod = $this->getCommod($id);
    $spob = new spob();
    $spobs = $spob->getSpobs();
    $db = new database();
    $db->query("INSERT IGNORE INTO tbl_commodspob
      (commod, spob, supply) VALUES (?, ?, ?)");
    $i = 0;
    foreach ($spobs as $spob) {
      if ($spob->techlevel >= $commod->techlevel) {
        $supply = $commod->basesupply * COMMOD_DISTRIBUTION;
        if (1 == floor(rand(0,1))) {
          $supply = $commod->basesupply - floor(rand(0,$supply));
        } else {
          $supply = $commod->basesupply + floor(rand(0,$supply));
        }
        $db->bind(1,$commod->id);
        $db->bind(2,$spob->id);
        $db->bind(3,$supply);
        try {
          $db->execute();
        } catch (Exception $e) {
          return returnError("Database error: ".$e->getMessage());
        }
        $i++;
      }
    }
    $game = new game();
    $game->logEvent("PC","Spammed $commod->name to $i spobs");
    return returnSuccess("Added $commod->name to ".singular($i,'spob','spobs'));
  }
  public function spamAllCommods() {
    $user = new user();
    if (!$user->isAdmin()) {
      $game = new game();
      $game->logEvent('AD','Action denied: spamCommods');
      return returnError("You must be an admin for this action");
    }
    $return = '';
    $commods = $this->GetCommods();
    foreach ($commods as $commod) {
      if ('R' == $commod->class) {
        $return.= $this->spamCommods($commod->id);
      }
    }
    return $return;
  }

  public function getSpobCommod($spob,$commod) {
    $db = new database();
    $db->query("SELECT ssim_commodspob.*,
      ssim_commod.*,
      floor(ssim_commod.baseprice * (ssim_commod.techlevel/ssim_spob.techlevel)
      / ssim_commodspob.supply * 1000) AS price
      FROM ssim_commodspob
      LEFT JOIN ssim_spob ON ssim_commodspob.spob = ssim_spob.id
      LEFT JOIN ssim_commod ON ssim_commodspob.commod = ssim_commod.id
      WHERE ssim_commodspob.spob = ?
      AND ssim_commodspob.commod = ?");
    $db->bind(1,$spob);
    $db->bind(2,$commod);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }


  public function buyCommod($commod, $amount) {
    if($amount <= 0) {
      return returnError("Unable to purchase cargo. Temporal anomaly detected.");
    }
    $pilot = new pilot(NULL,TRUE);
    $pilot->cargo = $pilot->getPilotCargoStats();
    $commod = $this->getSpobCommod($pilot->spob,$commod);
    $finalcost = floor($commod->price) * floor($amount);
    //Two areas need to be checked:
      //1. Is this commod available here?
    if (!$commod){
      return returnError("Unable to purchase cargo. Not available here.");
    }
      //2. Is there enough supply to fill the order?
    if ($commod->supply < $amount){
      return returnError("Unable to purchase that much cargo. Insufficent supply.");
    }
    //and...
      //3. Does the pilot have enough room?
    if ($pilot->cargo->capacity < $amount){
      return returnError("Unable to purchase cargo. Insufficent space.");
    }
      //4. Does the pilot have enough money?
    if ($pilot->credits < $finalcost) {
      return returnError("Unable to purchase cargo. Insufficent funds.");
    }
    //If those checks pass, we need to:
      //1. Remvoe the $amount from commodspob
    $this->subtractSpobCommod($pilot->spob, $commod->id, $amount);
      //2. Add $amount it to the matching cargopilot row (or create it)
    $pilot->newPilotCargo($commod->id,$amount);
      //3. Remove the credits from the player, including applicable taxes
    $pilot->deductCredits($finalcost);
      //4. Generate a receipt and store the transaction
      //5. Send the callback.
    $this->logCommodTransaction($commod->id,$amount, $pilot->uid, $pilot->syst, $pilot->spob, 'B', $finalcost);
    $game = new game();
    $game->logEvent("BC","Purchased $amount tons of $commod->name for $finalcost credits at $pilot->spobname ($pilot->spob)");
    return returnSuccess("Purchased $amount tons of $commod->name for $finalcost credits!");
  }

  public function subtractSpobCommod($spob,$commod,$amount) {
    $db = new database();
    $db->query("UPDATE ssim_commodspob SET supply = supply - ?
      WHERE commod = ?
      AND spob = ?");
    $db->bind(1,$amount);
    $db->bind(2,$commod);
    $db->bind(3,$spob);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }
  public function addSpobCommod($spob,$commod,$amount) {
    $db = new database();
    $db->query("UPDATE ssim_commodspob SET supply = supply + ?
      WHERE commod = ?
      AND spob = ?");
    $db->bind(1,$amount);
    $db->bind(2,$commod);
    $db->bind(3,$spob);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }
  public function sellCommod($commod, $amount) {
    $return = '';
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
    $pilot = new pilot(NULL,true);
    $cargo = $this->getPilotSpobCommods($pilot->uid,$pilot->spob,$commod);
    $commod = $this->getSpobCommod($pilot->spob,$commod);
    if (!$commod || !$cargo) {
      return "This can't be sold here!";
    }
    $this->addSpobCommod($pilot->spob, $commod->id, $amount);
    $finalcost = floor($commod->price) * floor($amount);
    $pilot->addCredits($finalcost);
    if(($pilot->syst === $cargo->lastsyst)
      && (!$cargo->is_legal)) {
      $legal = $amount * floor(rand(1, CARGO_PENALTY));
      $return.= $pilot->subtractLegal($legal);
    }
    $pilot->subtractPilotCargo($commod->id,$amount);
    $this->logCommodTransaction($commod->id,$amount, $pilot->uid, $pilot->syst, $pilot->spob, 'S', $finalcost);
    $game = new game();
    $game->logEvent("SC","Sold $amount tons of $commod->name for $finalcost at $pilot->spobname ($pilot->spob)");
    $return.= returnSuccess("Sold ".singular($amount,'ton','tons')." of $commod->name for ".credits($finalcost));

    return $return;
  }

  public function jettisonCommod($commod, $amount) {
    $return = '';
    $pilot = new pilot(NULL,TRUE);
    if ($amount < 0 || TRUE === $pilot->flags->isLanded) {
      return returnError("Unable to jettison cargo");
    }
    $commod = $this->getPilotCargoCommods($pilot->uid,$commod);
    $pilot->subtractPilotCargo($commod->id,$amount);
    $this->logCommodTransaction($commod->id,$amount, $pilot->uid, $pilot->syst, NULL, 'J', NULL);
    $game = new game();
    $game->logEvent("JC","Jettisoned $amount tons of $commod->name at $pilot->systname ($pilot->syst)");
    $return.= returnSuccess("Jettisoned ".singular($amount,'ton','tons')." of $commod->name");
    return $return;
  }

  public function getPilotCargoCommods($pilot,$commod=null){
    $db = new database();
    if (NULL != $commod) {
      $db->query("SELECT ssim_commod.*,
          ssim_cargopilot.amount,
          ssim_cargopilot.lastsyst,
          ssim_cargopilot.lastchange
          FROM ssim_commod
          LEFT JOIN ssim_cargopilot ON ssim_cargopilot.commod = ssim_commod.id
          WHERE ssim_commod.class = 'R'
          AND ssim_cargopilot.commod = ?
          AND ssim_cargopilot.amount > 0
          AND ssim_cargopilot.pilot = ?");
      $db->bind(1,$commod);
      $db->bind(2,$pilot);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      return $db->single();
    } else {
      $db->query("SELECT ssim_commod.*,
          ssim_cargopilot.amount,
          ssim_cargopilot.lastsyst,
          ssim_cargopilot.lastchange
          FROM ssim_commod
          LEFT JOIN ssim_cargopilot ON ssim_cargopilot.commod = ssim_commod.id
          WHERE ssim_commod.class = 'R'
          AND ssim_cargopilot.amount > 0
          AND ssim_cargopilot.pilot = ?");
      $db->bind(1,$pilot);
      try {
        $db->execute();
      } catch (Exception $e) {
        return returnError("Database error: ".$e->getMessage());
      }
      return $db->resultset();
    }
  }

  public function getPilotCommods($pilot, $spob) {
    $db = new database();
    $db->query("SELECT ssim_commod.*,
      ssim_commodspob.*,
      floor(ssim_commod.baseprice * (ssim_commod.techlevel/ssim_spob.techlevel)
      / ssim_commodspob.supply * 1000) AS price,
      ssim_cargopilot.amount,
      ssim_cargopilot.lastsyst,
      ssim_cargopilot.lastchange,
      CASE
        WHEN ssim_cargopilot.lastchange + INTERVAL 1 WEEK < NOW() AND ssim_spob.parent != ssim_cargopilot.lastsyst THEN 1
        ELSE 0
      END AS is_legal
      FROM ssim_commod
      LEFT JOIN ssim_commodspob ON ssim_commod.id = ssim_commodspob.commod
      LEFT JOIN ssim_spob ON ssim_commodspob.spob = ssim_spob.id
      LEFT JOIN ssim_cargopilot ON ssim_cargopilot.commod = ssim_commod.id
      WHERE ssim_commod.class = 'R'
      AND ssim_commodspob.spob = ?
      AND ssim_cargopilot.pilot = ?
      AND ssim_cargopilot.amount > 0
      GROUP BY ssim_commod.id;");
    $db->bind(1,$spob);
    $db->bind(2,$pilot);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }
  public function getPilotSpobCommods($pilot, $spob, $commod) {
    $db = new database();
    $db->query("SELECT ssim_commod.*,
          ssim_commodspob.*,
          floor(ssim_commod.baseprice * (ssim_commod.techlevel/ssim_spob.techlevel)
          / ssim_commodspob.supply * 1000) AS price,
          ssim_cargopilot.amount,
          ssim_cargopilot.lastsyst,
          ssim_cargopilot.lastchange,
          CASE
            WHEN ssim_cargopilot.lastchange + INTERVAL 1 WEEK < NOW() AND ssim_spob.parent != ssim_cargopilot.lastsyst THEN 1
          ELSE 0
          END AS is_legal
          FROM ssim_commod
          LEFT JOIN ssim_commodspob ON ssim_commod.id = ssim_commodspob.commod
          LEFT JOIN ssim_spob ON ssim_commodspob.spob = ssim_spob.id
          LEFT JOIN ssim_cargopilot ON ssim_cargopilot.commod = ssim_commod.id
          WHERE ssim_commod.class = 'R'
          AND ssim_commodspob.spob = ?
          AND ssim_cargopilot.pilot = ?
          AND ssim_commodspob.commod = ?
          AND ssim_cargopilot.amount > 0
          GROUP BY ssim_commod.id;");
    $db->bind(1,$spob);
    $db->bind(2,$pilot);
    $db->bind(3,$commod);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function logCommodTransaction($commod, $amount, $who, $syst, $spob=null, $type, $value=null) {
    $db = new database();
    $db->query("INSERT INTO tbl_commodtransact
      (timestamp, commod, amount, who, syst, spob, type, value) VALUES
      (NOW(), ?, ?, ?, ?, ?, ?, ?)");
    $db->bind(1,$commod);
    $db->bind(2,$amount);
    $db->bind(3,$who);
    $db->bind(4,$syst);
    $db->bind(5,$spob);
    $db->bind(6,$type);
    $db->bind(7,$value);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }
}
