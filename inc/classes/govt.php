<?php

class govt {
  public $id;
  public $name;
  public $isoname;
  public $type;
  public $color1;
  public $color2;
  public $shipcss;

  public $fullType;

  public $relations;

  public $totalmemberbalance;
  public $totalpilots;
  public $systems;
  public $spobs;

  public $css;
  public $htmlBadge;
  public $smallBadge;

  public function __construct($id=null,$full=false){
    if ($id) {
      $govt = $this->getGovt($id,$full);
      $govt = $this->parseGovt($govt,$full);
      foreach ($govt as $key => $value){
        $this->$key = $value;
      }
      if ($full) {
        $relations = $this->refactoredRelations();
        foreach ($relations as &$govt) {
          $govt = $this->parseGovt($govt, false);
          $govt->relationName = $this->getRelationType($govt->relation)->relationName;
        }
        $this->relations = $relations;
      }
    }
  }

  public function getGovtType($type){
    $return = new stdClass;
    switch ($type) {
      case 'I':
        $return->type = 'I';
        $return->fullType = 'Indepdendent';
      break;

      case 'R':
      default:
        $return->type = 'R';
        $return->fullType = 'Regular';
      break;

      case 'P':
        $return->type = 'P';
        $return->fullType = 'Pirate';
      break;
    }
    return $return;
  }

  public function getRelationType($type){
    $return = new stdClass;
    switch ($type) {
      case 'W':
        $return->relation = 'W';
        $return->relationName = 'At War';
      break;

      case 'W':
      default:
        $return->relation = 'N';
        $return->relationName = 'Neutral';
      break;

      case 'A':
        $return->relation = 'A';
        $return->relationName = 'Allied';
      break;
    }
    return $return;
  }

  public function parseGovt(&$govt,$full) {

    $govt->fullType = $this->getGovtType($govt->type)->fullType;

    $govt->css = "background-color: $govt->color1; color: $govt->color2; ";
    $govt->css.= "border-color: $govt->color2";

    $govt->htmlBadge = "<span class='badge govt' ";
    $govt->htmlBadge.= "style='$govt->css'>";
    $govt->htmlBadge.= "<strong>$govt->name</strong><br>";
    $govt->htmlBadge.="<small>$govt->isoname • $govt->id • $govt->fullType";
    $govt->htmlBadge.= "</small></span>";

    $govt->smallBadge = "<span class='badge small govt' ";
    $govt->smallBadge.= "style='$this->css'>";
    $govt->smallBadge.= "$govt->name</span>";

    return $govt;
  }

  public function getGovt($id,$full=false) {
    $db = new database();
    if ($full){
      $db->query("SELECT tbl_govt.*,
        sum(distinct tbl_pilot.credits) AS totalmemberbalance,
        count(distinct tbl_pilot.uid) AS totalpilots,
        count(distinct tbl_syst.id) AS systems,
        count(distinct tbl_spob.id) AS spobs
        FROM tbl_govt
        LEFT JOIN tbl_pilot ON tbl_govt.id = tbl_pilot.govt
        LEFT JOIN tbl_syst ON tbl_govt.id = tbl_syst.govt
        LEFT JOIN tbl_spob ON tbl_syst.id = tbl_spob.parent
        WHERE tbl_govt.id = ?
        GROUP BY tbl_govt.id;");
    } else {
      $db->query("SELECT * FROM tbl_govt WHERE id = ?");
    }
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }
  
  public function getGovts() {
    $db = new database();
    $db->query("SELECT * FROM tbl_govt");
    $db->execute();
    return $db->resultSet();
  }

  public function getIndieGovt() {
    $db = new database();
    $db->query("SELECT id FROM tbl_govt WHERE type = 'I'");
    $db->execute();
    return $db->single()->id;
  }

  public function getPirateGovt() {
    $db = new database();
    $db->query("SELECT id FROM tbl_govt WHERE type = 'P'");
    $db->execute();
    return $db->single()->id;
  }

  public function getRelations() {
    $db = new database();
    $db->query("SELECT tbl_govtrelations.*,
      target.name AS tgtname,
      target.isoname AS tgtisoname,
      target.color1 AS tgtcolor1,
      target.color2 AS tgtcolor2,
      subject.name AS subjname,
      subject.isoname AS subjisoname,
      subject.color1 AS subjcolor1,
      subject.color2 AS subjcolor2
      FROM tbl_govtrelations
      LEFT JOIN tbl_govt AS target ON tbl_govtrelations.target = target.id
      LEFT JOIN tbl_govt AS `subject` ON tbl_govtrelations.subject = subject.id
      WHERE tbl_govtrelations.subject = ?
      OR tbl_govtrelations.target = ?");
    $db->bind(1,$this->id);
    $db->bind(2,$this->id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function refactoredRelations($id=null){
    $db = new database();
    $db->query("SELECT tbl_govtrelations.*,
      target.*
      FROM tbl_govtrelations
      JOIN tbl_govt AS target ON tbl_govtrelations.target = target.id OR (tbl_govtrelations.target = ? AND tbl_govtrelations.reciprocal = 1)
      GROUP BY target.id;");
    if ($id){
      $db->bind(1,$id);
    } else {
      $db->bind(1,$this->id);
    }
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function revokeMembership($govt,$pilot) {
    $db = new database();
    $db->query("DELETE FROM tbl_govtranks
      WHERE tbl_govtranks.govt = ? AND tbl_govtranks.pilot = ?");
    $db->bind(1,$govt);
    $db->bind(2,$pilot);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("Your membership has been revoked");
  }

  public function declareNewLeader($govt,$pilot) {
    $govt = $this->getGovt($govt);
    $db = new database();
    $db->query("INSERT INTO tbl_govtranks (govt, pilot, rank)
    VALUES (?,?,?)");
    $db->bind(1,$govt->id);
    $db->bind(2,$pilot);
    $db->bind(3,'P');
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("You have been declared leader of the $govt->name");
  }
}