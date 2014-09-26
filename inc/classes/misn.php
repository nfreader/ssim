<?php 

class misn {

  public function __construct() {
    //TODO: What does this do.
  }

  public function newMisn($commod, $tons, $pickup, $dest, $reward) {

  }

  public function generateMisn($count=1) {
    //We need arrays of
      //spob IDs
      //commod IDs (where class != 'D')
    //$commod = new commod();
    //$commods = $commod->getCommods();
    //$spob = new spob();
    //$spobs = $spob->getSpobs();

    //WAIT WAIT WAIT
    //That's SUPER DUMB. Add methods to get spobs and commods at random! Damn!

    //WAIT A SECOND
    //YOU'RE super dumb! generateMisn will call those methods for each 
    //$count! That's a shitload of pointless DB calls. Your first instincts to
    //pull down all the spobs and commods was right.
    $commod = new commod();
    $commods = $commod->getCommods();
    $spob = new spob();
    $spobs = $spob->getSpobs();

    $db = new database();
    $db->query("INSERT INTO ssim_misn
      (status, pickup, dest, amount, commod, reward, uid)
      VALUES ('N', :pickup, :dest, :amount, :commod, :reward, :uid)");

    $i = 0;
    $return = '';
    while ($i != $count) {

      getlocs:
      $commod = $commods[array_rand($commods)];
      $pickup = $spobs[array_rand($spobs)];
      $deliver = $spobs[array_rand($spobs)];
  
      $commod = $commod->id;
      $pickup = $pickup->id;
      $deliver = $deliver->id;
  
      if ($deliver === $pickup) {
        goto getlocs;
      }
      //TODO: Set limits based on largest and smallest ships in ssim_ship
      $amount = rand(5,100);

      //TODO: Set reward based on calculated $commod price
      $reward = rand($amount*1.25,$amount*3)*100;

      $db->bind(':pickup',$pickup);
      $db->bind(':dest', $deliver);
      $db->bind(':amount',$amount);
      $db->bind(':reward',$reward);
      $db->bind(':commod',$commod);
      $db->bind(':uid',hexPrint($pickup.$deliver.$amount.$reward.$commod));
      $db->execute();
      $i++;
    }
    return $db->rowCount();
  }

  public function getMissionList($spob=null) {
    $db = new database();
    $db->query("SELECT
    ssim_commod.name AS commodity,
    dest.name AS delivery,
    pickup.name AS pickup,
    ssim_misn.reward,
    ssim_misn.amount AS tons,
    ssim_commod.baseprice * (ssim_commod.techlevel/dest.techlevel) /
    ssim_commodspob.supply * 1000 AS price,
    floor((SELECT price) * ssim_misn.amount) AS value,
    floor(((SELECT price) * ssim_misn.amount) / ssim_misn.reward * 100)
    AS ratio,
    ssim_commod.class
    FROM ssim_misn
    LEFT JOIN ssim_spob AS dest ON ssim_misn.dest = dest.id
    LEFT JOIN ssim_spob AS pickup ON ssim_misn.pickup = pickup.id
    LEFT JOIN ssim_commod ON ssim_misn.commod = ssim_commod.id
    LEFT JOIN ssim_commodspob ON ssim_commodspob.spob = dest.id
    LIMIT 0,100");
    $db->execute();
    return $db->resultSet();
  }

  public function getMissionStats() {
    $db = new database();
    $db->query("SELECT
    ssim_commod.name AS commodity,
    ssim_commod.baseprice * (ssim_commod.techlevel/dest.techlevel) /
    ssim_commodspob.supply * 1000 AS unitprice,
    floor((SELECT unitprice) * SUM(ssim_misn.amount)) AS realvalue,
    SUM(ssim_misn.amount) AS totaltons,
    SUM(ssim_misn.reward) AS totalvalue,
    ssim_commod.class
    FROM ssim_misn
    LEFT JOIN ssim_spob AS dest ON ssim_misn.dest = dest.id
    LEFT JOIN ssim_spob AS pickup ON ssim_misn.pickup = pickup.id
    LEFT JOIN ssim_commod ON ssim_misn.commod = ssim_commod.id
    LEFT JOIN ssim_commodspob ON ssim_commodspob.spob = dest.id
    GROUP BY ssim_misn.commod");
    $db->execute();
    return $db->resultSet();
  }

  public function getAvailableMissions() {
    $db = new database();
    $db->query("SELECT ssim_misn.*,
    dest.name AS destination,
    ssim_commod.name AS commodity,
    ssim_commod.class
    FROM ssim_misn
    LEFT JOIN ssim_spob AS dest ON ssim_misn.dest = dest.id
    LEFT JOIN ssim_commod ON ssim_misn.commod = ssim_commod.id
    WHERE ssim_misn.pickup = :spob
    AND ssim_misn.amount <= :limit
    AND ssim_misn.status = 'N'");
    $pilot = new pilot(true, true);
    $limit = $pilot->getPilotCargoStats();
    $db->bind(':spob',$pilot->pilot->spob);
    $db->bind(':limit',$limit->capacity);
    $db->execute();
    return $db->resultSet();
  }

  public function getDeliverableMissions() {
    $db = new database();
    $db->query("SELECT ssim_misn.*,
    dest.name AS destination,
    ssim_commod.name AS commodity,
    ssim_commod.class
    FROM ssim_misn
    LEFT JOIN ssim_spob AS dest ON ssim_misn.dest = dest.id
    LEFT JOIN ssim_commod ON ssim_misn.commod = ssim_commod.id
    WHERE ssim_misn.dest = :spob
    AND ssim_misn.pilot = :pilot
    AND ssim_misn.status = 'T'");
    $pilot = new pilot(true, true);
    $db->bind(':pilot',$pilot->pilot->id);
    $db->bind(':spob',$pilot->pilot->spob);
    $db->execute();
    return $db->resultSet();
  }

  public function getMission($uid) {
    $db = new database();
    $db->query("SELECT ssim_misn.* FROM ssim_misn WHERE uid = :uid");
    $db->bind(':uid',$uid);
    $db->execute();
    return $db->single();
  }

  public function acceptMission($uid) {
    //Couple verification steps here
      //Does the player have enough available cargo space for this? 
      //Is this mission available here, or is someone trying to BS their way
      //in? 
    $misn = $this->getMission($uid);
    if (!$misn) {
      return "Unable to locate mission: $uid";
    }

    $pilot = new pilot(true, true);

    if ($pilot->pilot->spob != $misn->pickup) {
      return "Unable to pick up this mission!";
    }

    $space = $pilot->getPilotCargoStats();

    if ($space->capacity < $misn->amount){
      return "You can't carry that much cargo!";
    }

    $db = new database();
    $db->query("UPDATE ssim_misn SET pilot = :pilot, status = 'T'
      WHERE uid = :uid");
    $db->bind(':pilot',$pilot->pilot->id);
    $db->bind(':uid',$misn->uid);
    $db->execute();
    return "Mission $misn->uid picked up.";
  }

  public function deliverMission($uid) {
    //All we need to verify is whether or not this is the intended destination
    //We can add other features (taxes etc) later on
    $misn = $this->getMission($uid);
    if (!$misn) {
      return "Unable to locate mission: $uid";
    }
    $dest = new pilot(true, true);

    if($dest->pilot->spob != $misn->dest) {
      return "$uid cannot be delivered here";
    }

    $dest->addCredits($misn->reward);

    $db = new database();
    $db->query("UPDATE ssim_misn SET status = 'D'
      WHERE uid = :uid
      AND pilot = :pilot");
    $db->bind(':pilot', $dest->pilot->id);
    $db->bind(':uid',$misn->uid);
    $db->execute();
    return "Mission $misn->uid delivered for $misn->reward cr.";

  }

  public function getPirateableMissions(){
    $db = new database();
    $db->query("SELECT ssim_misn.*,
    ssim_commod.*,
    ssim_commod.name AS commodity
    FROM ssim_misn
    JOIN ssim_commod ON ssim_misn.commod = ssim_commod.id
    JOIN ssim_commodspob ON ssim_commod.id = ssim_commodspob.commod
    WHERE ssim_commod.class = 'R'
    AND ssim_misn.status = 'T'
    AND ssim_commodspob.spob = :spob");
    $pilot = new pilot(true,true);
    $db->bind(':spob',$pilot->pilot->spob);
    $db->execute();
    return $db->resultset();
  }

  public function pirateMission($uid) {
    //This is basically $commod->sellCommod() with a bunch of stuff specifed
    //manually. The only difference here is the source of the cargo!

    //Let's grab some data we'll need
    $misn = $this->getMission($uid);
    $pilot = new pilot(true, true);

    //First up! Can this commodity be sold on this spob? 
    $commod = new commod;
    $commoddata = $commod->getSpobCommodData($pilot->pilot->spob,
      $misn->commod);
    if(!$commoddata) {
      return "This commodity is not sold here";
    }

    //Yarr! Time to pirate!
    $finalcost = floor($commoddata->price * $misn->amount);
    $legal = $misn->amount * floor(rand(1, CARGO_PENALTY));

    $commod->addSpobCommod($pilot->pilot->spob,$misn->commod,$misn->amount);
    $pilot->addCredits($finalcost);
    $pilot->subtractLegal($legal);

    $db = new database();
    $db->query("UPDATE ssim_misn SET status = 'P'
      WHERE ssim_misn.uid = :uid
      AND ssim_misn.pilot = :pilot");
    $db->bind(':uid',$uid);
    $db->bind(':pilot',$pilot->pilot->id);
    $db->execute();
    $return = "Mission cargo sold for $finalcost cr., legal impact of -$legal points";
    return $return;
  }

}
