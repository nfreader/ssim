<?php

class ship {

  public $id;
  public $name;
  public $cost;
  public $fueltank;
  public $cargobay;
  public $expansion;
  public $shields;
  public $armor;
  public $mass;
  public $accel;
  public $turn;
  public $starter;
  public $class;
  public $shipwright;
  public $description;
  public $image;

  public $classname;
  public $baseEvasion;

  public function __construct($ship = null) {
    if($ship) {
      $ship = $this->getShip($ship);
      $ship = $this->parseShip($ship);
      foreach ($ship as $key => $value){
        $this->$key = $value;
      }
    }
  }

  public function parseShip(&$ship) {
    $ship->classname = $this->getClass($ship->class)->classname;
    $ship->baseEvasion = $this->getBaseEvasion($ship->accel,$ship->turn,$ship->mass);
    return $ship;
  }

  public function getClass($class) {
    $data = new stdClass;
    switch($class) {
      default:
      case 'S':
      $data->classname ='Shuttle';
      break;

      case 'F':
      $data->classname ='Fighter';
      break;

      case 'C':
      $data->classname ='Cargo Freighter';
      break;

      case 'R':
      $data->classname ='Frigate';
      break;
    }
    return $data;
  }

  public function getBaseEvasion($accel, $turn, $mass) {
    return floor(($accel * $turn) / $mass);
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

  public function addShip($post){

    $filter = ['name','shipwright','cost','class','mass','accel','turn',
    'fuel','cargo','expansion','armor','shields','starter'];

    $shipData = sieve($post, $filter);

    $db = new database();
    $db->query("INSERT INTO tbl_ship
      (name, shipwright, cost, class, mass, accel, turn, fueltank, cargobay, expansion, armor, shields, starter)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $db->bind(1,$shipData['name']);
    $db->bind(2,$shipData['shipwright']);
    $db->bind(3,$shipData['cost']);
    $db->bind(4,$shipData['class']);
    $db->bind(5,$shipData['mass']);
    $db->bind(6,$shipData['accel']);
    $db->bind(7,$shipData['turn']);
    $db->bind(8,$shipData['fuel']);
    $db->bind(9,$shipData['cargo']);
    $db->bind(10,$shipData['expansion']);
    $db->bind(11,$shipData['armor']);
    $db->bind(12,$shipData['shields']);
    $db->bind(13,$shipData['starter']);
    try {
      $db->execute();
    } catch (Exception $e) {
      return array("Database error: ".$e->getMessage(),1);
    }
    $return[] = array(
      'message'=>"Added ".$shipData['name'],
      'level'=>'normal'
    );
    return $return;
  }

}
