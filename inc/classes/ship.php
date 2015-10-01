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

  public function getShipClasses() {
    $shipClasses = array(
      'S'=>'Shuttle',
      'F'=>'Fighter',
      'C'=>'Cargo Freighter',
      'R'=>'Frigate'
    );
    return $shipClasses;
  }

  public function addShip($name, $shipwright, $cost, $class, $mass, $accel, $turn, $fuel, $cargo, $expansion, $armor, $shields){
    $return = array(
      'message'=>"You fucked up.",
      'level'=>'error'
    );
    return $return;
    $db = new database();
    $db->query("INSERT INTO tbl_ship
      (name, shipwright, cost, class, mass, accel, turn, fueltank, cargobay, expansion, armor, shields)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $db->bind(1,$name);
    $db->bind(2,$shipwright);
    $db->bind(3,$cost);
    $db->bind(4,$class);
    $db->bind(5,$mass);
    $db->bind(6,$accel);
    $db->bind(7,$turn);
    $db->bind(8,$fuel);
    $db->bind(9,$cargo);
    $db->bind(10,$expansion);
    $db->bind(11,$armor);
    $db->bind(12,$shields);
    try {
      $db->execute();
    } catch (Exception $e) {
      return array("Database error: ".$e->getMessage(),1);
    }
    $return[] = array(
      'message'=>"Added $name",
      'level'=>'normal'
    );
    return $return;
  }

}
