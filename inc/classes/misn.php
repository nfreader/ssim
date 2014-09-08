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

      $amount = rand(5,100);
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
ssim_commod.baseprice * (ssim_commod.techlevel/dest.techlevel) / ssim_commodspob.supply * 1000 AS price,
floor((SELECT price) * ssim_misn.amount) AS pirate,
floor(((SELECT price) * ssim_misn.amount) / ssim_misn.reward * 100) AS ratio
FROM ssim_misn
LEFT JOIN ssim_spob AS dest ON ssim_misn.dest = dest.id
LEFT JOIN ssim_spob AS pickup ON ssim_misn.pickup = pickup.id
LEFT JOIN ssim_commod ON ssim_misn.commod = ssim_commod.id
LEFT JOIN ssim_commodspob ON ssim_commodspob.spob = dest.id
WHERE ssim_commod.class != 'S'
LIMIT 0,100");
    $db->execute();
    return $db->resultSet();
  }

}