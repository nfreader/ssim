<?php 

class ship {
  public function getRandStarter() {
    $db = new database();
    $db->query("SELECT id, fueltank FROM ssim_ship
      WHERE starter = 1
      ORDER BY RAND()
      LIMIT 0,1");
    $db->execute();
    return $db->single();
  }

  public function newPurchaseData($id) {
    $db = new database();
    $db->query("SELECT id, fueltank
      FROM ssim_ship
      WHERE id = :id");
    $db->bind(':id',$id);
    $db->execute();
    return $db->single();
  }

}