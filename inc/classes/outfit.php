<?php

class outfit {

  public $id;
  public $name;
  public $size;
  public $cost;
  public $type;
  public $subtype;
  public $flag;
  public $modifies; //Or the key
  public $value; 
  public $reload; //How many battle ticks until this outfit can be reused
  public $ammo; //ID of the outfit used for ammunition
  public $description;
  public $techlevel; //Minimum techlevel needed to see this outfit for sale
  public $govt; //Restrict item to this government's ID

  public $class; //Whether this outift is attached to a pilot or a vessel
  public $fullType;
  public $htmlListing;

  public function __construct($id=null) {
    if ($id){
      $outfit = $this->getOutfit($id);
      $outfit = $this->parseOutfit($outfit);
      foreach ($outfit as $key => $value){
        $this->$key = $value;
      }
    }
  }

  public function parseOutfit(&$outfit){
    $outfit->class = $this->getOutfitClass($outfit->type);
    $outfit->fullType = $this->getOutfitFullType($outfit->type,$outfit->subtype);
    $outfit->htmlListing = $this->generateOutfitListingHTML($outfit);
    return $outfit;
  }

  public function generateOutfitListingHTML($outfit){

    $body = "<p>$outfit->description</p><ul class='dot-leader'>";
    switch($outfit->type) {
      case 'W':
      $body.= "<li><span>Damage</span><span>$outfit->value</span></li>";
      $body.= "<li><span>Reload</span><span>$outfit->reload</span></li>";
      break;
    }

    if ($outfit->size){
      $body.= "<li><span>Size</span>";
      $body.="<span>".singular($outfit->size,'ton','tons')."</span></li>";
    }
    $body.= "</ul>";

    $html = "<div class='outfit-listing'>";
    $html.= "<div class='outfit-header'><h3>$outfit->name</h3>";
    $html.= "<small>$outfit->fullType</small></div>";
    $html.= "<div class='outfit-body'>$body</div>";
    $html.= "<div class='outfit-footer'>";
    $html.= credits($outfit->cost)." <p class='pull-right'>";
    $html.= "<a href='#' class='btn color green'>Purchase</a></div>";
    $html.= "</div>";

    return $html;
  }

  public function getOutfitClass($type) {
    switch($type){
      case 'M': //A modifier
      case 'H': //A hacking tool
      case 'D': //A decorative outfit
      return 'pilot';
      break;

      default:
      case 'W': //A weapon
      case 'A': //An addon
      case 'E': //An escape pod
      case 'B': //A beacon launcher
      case 'J': //A jamming tool
      return 'vessel';
      break;
    }
  }

  public function getOutfitFullType($type, $subtype){
    switch ($type){
      case 'W':
        switch($subtype){
          case 'E':
            return 'A Focused Energy Weapon';
          break;

          case 'M':
            return 'A Missile Launcher';
          break;
          
          default:
            return 'A Weapon';
          break;
        }
      break;

      case 'A':
        return 'An addon';
      break;

      case 'E':
        return 'An Escape Pod';
      break;

      case 'B':
        return 'A beacon launcher';
      break;

      case 'J':
        return 'A Jammer';
      break;

      case 'H':
        return 'A Hacking Tool';
      break;

      case 'D':
        return 'A Decoration';
      break;
    }
  }

  public function getOutfit($id=null){
    $db = new database();
    $db->query("SELECT * FROM tbl_outf WHERE id = ?");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function getPortOutfitListing($govt=null,$techlevel=null){
    $db = new database();
    $db->query("SELECT *
      FROM tbl_outf
      WHERE tbl_outf.techlevel >= ? OR tbl_outf.techlevel IS NULL
      AND tbl_outf.govt = ? OR tbl_outf.govt IS NULL
      AND tbl_outf.cost IS NOT NULL");
    $db->bind(1, $govt);
    $db->bind(2, $techlevel);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $result = $db->resultset();
    foreach($result as &$outfit) {
      $outfit = $this->parseOutfit($outfit);
    }
    return $result;
  }

  public function getPilotOutfits($pilot) {
    $db = new database();
    $db->query("SELECT ssim_outf.*, ssim_pilotoutf.*
      FROM ssim_pilotoutf
      LEFT JOIN ssim_outf ON ssim_pilotoutf.outfit = ssim_outf.id
      WHERE ssim_pilotoutf.pilot = ?");
    $db->bind(1,$pilot);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $result = $db->resultset();
    foreach($result as &$outfit) {
      $outfit = $this->parseOutfit($outfit);
    }
    return $result;
  }

}
