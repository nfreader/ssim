<?php 

class misn {

  public function __construct() {
    //TODO: What.
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

      $return.= "Take $commod from $pickup to $deliver\n\r";
      $i++;
    }
    return $return;
  }

}