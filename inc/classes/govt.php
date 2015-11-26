<?php

class govt {

  public $id;
  public $name;
  public $isoname;
  public $type;
  public $color1;
  public $color2;

  public $relations;

  public function __construct($id=null) {
    if($id){
      $govt = $this->getGovt($id);
      $this->id = $govt->id;
      $this->name = $govt->name;
      $this->isoname = $govt->isoname;
      $this->color1 = $govt->color1;
      $this->color2 = $govt->color2;

      if('I' === $govt->type) {
        $this->type = 'Independent';
      } elseif('P' === $govt->type) {
        $this->type = 'Pirate';
      } else {
        $this->type = 'Regular';
      }
      $this->relations = $this->getRelations($this->id);
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
