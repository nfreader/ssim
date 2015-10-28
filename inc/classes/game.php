<?php

class game {

  public $date;
  public $version;

  public function __construct() {
    $this->date = date(SSIM_DATE);
    $this->version = GAME_VERSION;
  }

  public function logEvent($what, $data) {
    $db = new database();
    $db->query("INSERT INTO tbl_log (who, aspilot, what, timestamp, data)
      VALUES (?, ?, ?, NOW(), ?)");
    $user = new user();
    $db->bind(1,$user->uid);
    $db->bind(2,$user->activepilot);
    $db->bind(3,$what);
    $db->bind(4,$data);
    $db->execute();
  }

  public function getLogs($offset=0,$perpage=30) {
    $db = new database();
    $db->query("SELECT ssim_log.*,
        ssim_user.username,
        ssim_pilot.name
        FROM ssim_log
        LEFT JOIN ssim_user ON ssim_log.who = ssim_user.uid
        LEFT JOIN ssim_pilot ON ssim_user.uid = ssim_pilot.user
        ORDER BY timestamp DESC
        LIMIT $offset,$perpage");
    $db->execute();
    return $db->resultset();
  }
  public function test() {
    $return[] = array('message'=>$this->date,'level'=>0);
    $return[] = array('message'=>$this->version,'level'=>0);
    $return[] = array('message'=>'Test one','level'=>1);
    $return[] = array('message'=>'Test two','level'=>1);
    $return[] = array('message'=>'Test three','level'=>2);
    $return[] = array('message'=>'Test four','level'=>2);
    return $return;
  }

  public function json_test(){
    $return = '';
    $return.= json_encode(array('message'=>$this->date,'level'=>0));
    $return.= json_encode(array('message'=>$this->version,'level'=>0));
    $return.= json_encode(array('message'=>'Test one','level'=>1));
    $return.= json_encode(array('message'=>'Test two','level'=>1));
    $return.= json_encode(array('message'=>'Test three','level'=>2));
    $return.= json_encode(array('message'=>'Test four','level'=>2));
    $return.= $this->anotherChain();
    return $return;
  }

  public function returnError() {
    return returnError('This is an error');
  }

  public function chainedCall() {
    $return[] = array('Deep Test',2);
    $return[] = array('Deep Test 2',0);
    return $return;
  }

  public function anotherChain() {
    $return = '';
    $return.= json_encode(array('message'=>'Testing Deep','level'=>2));
    $return.= json_encode(array('message'=>'Deep Test','level'=>0));
    return $return;    
  }
}