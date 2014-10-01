<?php 

class ship {

  public $id;
  public $name;
  public $cost;
  public $fueltank;
  public $cargobay;
  public $shields;
  public $armor;
  public $starter;
  public $class;
  public $shipwright;

  public function __construct($ship = null) {
    if(isset($ship)) {
      $data = $this->getShip($ship);
      $this->id = $data->id;
      $this->name = $data->name;
      $this->cost = $data->cost;
      $this->fueltank = $data->fueltank;
      $this->cargobay = $data->cargobay;
      $this->shields = $data->shields;
      $this->armor = $data->armor;
      $this->starter = $data->starter;
      $this->class = $data->class;
      $this->shipwright = $data->shipwright;
    }
  }

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

  public function getShip($id) {
    $db = new database();
    $db->query("SELECT * FROM ssim_ship WHERE id = :id");
    $db->bind(':id',$id);
    $db->execute();
    return $db->single();
  }

  public function getShipyard() {
    $db = new database();
    $db->query("SELECT * FROM ssim_ship");
    $db->execute();
    return $db->resultset();
  }

}
