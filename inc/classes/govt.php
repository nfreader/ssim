<?php

class govt {

  public $id;
  public $name;
  public $isoname;
  public $type;
  public $color1;
  public $color2;
  public $shipcss;

  public $fulltype;

  public $relations;

  public $totalmemberbalance;
  public $totalpilots;
  public $systems;
  public $spobs;


  public function __construct($id=null,$full=FALSE) {
    if($id){
      $govt = $this->getGovt($id);
      $this->id = $govt->id;
      $this->name = $govt->name;
      $this->isoname = $govt->isoname;
      $this->color1 = $govt->color1;
      $this->color2 = $govt->color2;
      $this->type = $govt->type;

      if('I' === $govt->type) {
        $this->fulltype = 'Independent';
      } elseif('P' === $govt->type) {
        $this->fulltype = 'Pirate';
      } else {
        $this->fulltype = 'Regular';
      }
      $this->relations = $this->getRelations($this->id);

      $this->shipcss = "<style>.primary{fill:".$govt->color1.";} .accent{fill:".$govt->color2."}</style>";

      if($full) {
        $stats = $this->getGovtStats($id);
        $this->totalmemberbalance = $stats->totalmemberbalance;
        $this->totalpilots = $stats->totalpilots;
        $this->systems = $stats->systems;
        $this->spobs = $stats->spobs;
      }
    }
  }

  public function getGovt($id) {
    $db = new database();
    $db->query("SELECT * FROM tbl_govt WHERE id = ?");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getGovtStats($id) {
    $db = new database();
    $db->query("SELECT sum(distinct tbl_pilot.credits) AS totalmemberbalance,
      count(distinct tbl_pilot.uid) AS totalpilots,
      count(distinct tbl_syst.id) AS systems,
      count(distinct tbl_spob.id) AS spobs
      FROM tbl_govt
      LEFT JOIN tbl_pilot ON tbl_govt.id = tbl_pilot.govt
      LEFT JOIN tbl_syst ON tbl_govt.id = tbl_syst.govt
      LEFT JOIN tbl_spob ON tbl_syst.id = tbl_spob.parent
      WHERE tbl_govt.id = ?
      GROUP BY tbl_govt.id;");
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

  public function getRelations($id) {
    $db = new database();
    $db->query("SELECT ssim_govtrelations.*,
      target.name AS tgtname,
      target.isoname AS tgtisoname,
      target.color1 AS tgtcolor1,
      target.color2 AS tgtcolor2,
      subject.name AS subjname,
      subject.isoname AS subjisoname,
      subject.color1 AS subjcolor1,
      subject.color2 AS subjcolor2
      FROM ssim_govtrelations
      LEFT JOIN ssim_govt AS target ON ssim_govtrelations.target = target.id
      LEFT JOIN ssim_govt AS `subject` ON ssim_govtrelations.subject = subject.id
      WHERE tbl_govtrelations.subject = ?
      OR tbl_govtrelations.target = ?");
    $db->bind(1,$id);
    $db->bind(2,$id);
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

  public function generateCSS() {
    $db = new database();
    $db->query("SELECT tbl_govt.id,
      tbl_govt.isoname,
      tbl_govt.color,
      tbl_govt.color2
      FROM tbl_govt");
    $db->execute();
    $colors = $db->resultset();
    $css = '';
    foreach($colors as $gov) {
      $css.=".gov.$gov->isoname {";
      $css.="  color: #$gov->color;";
      $css.="  background: #$gov->color2;";
      $css.="}";
      $css.=".gov.$gov->isoname.inverse {";
      $css.="  color: #$gov->color2;";
      $css.="  background: #$gov->color;";
      $css.="}";
    }
    $handle = fopen("assets/css/govt.css","a+");
    ftruncate($handle,0);
    fwrite($handle,$css);
    fclose($handle);
  }

}
