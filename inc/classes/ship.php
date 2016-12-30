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

  public function __construct($ship = null) {
    if(isset($ship)) {
      $ship = $this->getShip($ship);
      $this->id = $ship->id;
      $this->name = $ship->name;
      $this->cost = $ship->cost;
      $this->fueltank = $ship->fueltank;
      $this->cargobay = $ship->cargobay;
      $this->expansion = $ship->expansion;
      $this->shields = $ship->shields;
      $this->armor = $ship->armor;
      $this->mass = $ship->mass;
      $this->accel = $ship->accel;
      $this->turn = $ship->turn;
      $this->starter = $ship->starter;
      $this->class = $ship->class;
      $this->shipwright = $ship->shipwright;
      $this->description = $ship->description;
      $this->image = $ship->image;

      $this->classname = shipClass($ship->class)['class'];
      $this->baseEvasion = evasionChance($this->accel,$this->turn,$this->mass);
      $this->evasion = $this->baseEvasion;
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
