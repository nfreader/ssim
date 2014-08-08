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
    $db->query("SELECT * FROM ssim_commod");
    $db->execute();
    return $db->resultset();
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
    $i = 1;
    foreach ($spobs as $spob) {
      foreach ($commods as $commod) {
        if ($commod->techlevel <= $spob->techlevel) {
          $db->bind(':spob',$spob->id);
          $db->bind(':commod',$commod->id);
          $db->bind(':supply',floor(rand(500,1000)));
          if ($db->execute()) {
            $i++;
          }
        }
      }
      return "Added ".$i." commodity records.";
    }
  }

  public function addCommod($name, $techlevel, $baseprice, $type) {
    $db = new database();
    $db->query("INSERT INTO ssim_commod (name, techlevel, baseprice)
      VALUES (:name,:techlevel, :baseprice)");
    $db->bind(':name',$name);
    $db->bind(':techlevel',$techlevel);
    $db->bind(':baseprice',$baseprice);
    $db->bind(':type',$type);
    if($db->execute()){
      return $name." was added as a commodity!";
    }
  }

}