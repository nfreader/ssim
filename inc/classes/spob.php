<?php

class spob {

  public $id;
  public $name;
  public $techlevel;
  public $type;
  public $description;
  public $homeworld;

  public $fuelcost;
  public $nodeid;
  public $fullname;

  public $parent;
  public $govt;
  public $commods;
  public $outfits;

  public $govtid;

  public function __construct($id=null, $data=null) {
    if (isset($id)) {
      $spob = $this->getSpob($id);
      $spob = $this->parseSpob($spob);
      if ($data){
        if (is_string($data)) {
          $data = explode(',',$data);
        }
        foreach ($data as $get){
          switch($get){
            case 'commods':
              $commod = new commod();
              $spob->commods = $commod->getSpobCommods($spob->id);
            break;

            case 'govt':
              if (!is_object($spob->parent)){
                $spob->govt = new govt($this->getSpobGovt($spob->id));
                $spob->govtid = $spob->govt->id;
              } else {
                $spob->govt = $spob->parent->govt;
                $spob->govtid = $spob->parent->govt->id;
              }
            break;

            case 'outfit':
              if ($spob->govtid){
                $spob->govt = new govt($this->getSpobGovt($spob->id));
                $spob->govtid = $spob->govt->id;
              }
              $outfit = new outfit();
              $spob->outfits = $outfit->getPortOutfitListing($spob->govtid,$spob->techlevel);
            break;

            case 'parent':
              $spob->parent = new syst($spob->parent,null);
            break;
          }
        }
      }
      if (isset($spob->parent->govt)){
        $spob->govt = $spob->parent->govt;
      }
      foreach ($spob as $key=>$value){
        $this->$key = $value;
      }
    }
  }

  public function parseSpob(&$spob){
    $spob->govtid = null;
    $spob->fuelcost = $this->fuelcost($spob->techlevel,$spob->type);
    $spob->nodeid = hexPrint($spob->id.$spob->name);
    $spob->fullname = $this->spobName($spob->name,$spob->type);
    return $spob;
  }

  public function getSpobGovt($id){
    $db = new database();
    $db->query("SELECT tbl_syst.govt FROM tbl_spob LEFT JOIN tbl_syst ON tbl_spob.parent = tbl_syst.id WHERE tbl_spob.id = ?");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single()->govt;
  }

  /**
   * fuelCost
   *
   * Returns the cost of fuel per unit based on the spob tech level and type
   *
   * @param $techlevel (int) The tech level of the spob we're looking at.
   * Defaults to 1
   *
   * @param $type (string) The spob type we're looking at. Defaults to 'P' if not
   * specified
   *
   * @return (int) The cost of one unit of fuel on this spob
   *
   */

  public function fuelCost($techlevel=1,$type) {
    switch($type) {
      case 'P':
      default:
        return floor(FUEL_BASE_COST/$techlevel);
        break;

      case 'S':
      case 'N':
        return floor(FUEL_BASE_COST/$techlevel) * 1.5;
        break;

      case 'M':
        return floor(FUEL_BASE_COST/$techlevel) * .5;
        break;
    }
  }

  /**
   * spobName
   *
   * Returns the full name of the given spob, with the type added as a suffix or prefix respectively.
   *
   * @param $name (string) The name of the spob
   * @param $type (string) The type of spob
   *
   * @return (string) The full name of the spob
   */

  function spobName($name,$type) {
    $fullType = spobType($type);
    if ($type == 'P') {
      return "$fullType $name";
    } elseif ('S' == $type || 'M' == $type) {
      return "$name $fullType";
    } else {
      return $name;
    }
  }

  public function getSpobs() {
    $db = new database();
    $db->query("SELECT * FROM tbl_spob");
    $db->execute();
    return $db->resultSet();
  }

  public function getSystemSpobs($parent) {
    $db = new database();
    $db->query("SELECT * FROM tbl_spob WHERE parent = ?");
    $db->bind(1,$parent);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }

  public function getSpob($spob) {
    $db = new database();
    $db->query("SELECT tbl_spob.*
      FROM tbl_spob
      WHERE tbl_spob.id = ?");
    $db->bind(1,$spob);
    $db->execute();
    return $db->single();
  }

  //Get a list of all spobs with a planet flag set
  public function getHomeworlds() {
    $db = new database();
    $db->query("SELECT name, id FROM tbl_spob WHERE homeworld = 1");
    $db->execute();
    return $db->resultSet();
  }

  //Returns one random homeworld spob (id and name)
  public function getRandHomeworld() {
    $db = new database();
    $db->query("SELECT tbl_spob.name,
      tbl_spob.id,
      tbl_spob.parent,
      tbl_syst.govt
      FROM tbl_spob
      LEFT JOIN tbl_syst ON tbl_spob.parent = tbl_syst.id
      WHERE homeworld = 1
      ORDER BY RAND()
      LIMIT 0,1;");
    $db->execute();
    return $db->single();
  }

  public function makeHomeworld($spob) {
    $db = new database();
    $db->query("UPDATE tbl_spob SET homeworld = 1 WHERE id = :id");
    $db->bind(":id",$spob);
    $db->execute();
    return $db->rowCount();
    $game = new game();
    $game->logEvent('MH',"Made $spob a homeworld.");
  }

  public function revokeHomeworld($spob) {
    $db = new database();
    $db->query("UPDATE tbl_spob SET homeworld = 0 WHERE id = :id");
    $db->bind(":id",$spob);
    $db->execute();
    return $db->rowCount();
    $game = new game();
    $game->logEvent('RH',"Revoked homeworld status for $spob");
  }

  public function addSpob($parent, $name, $type, $techlevel, $description) {
    $db = new database();
    $db->query("INSERT INTO tbl_spob (parent, name, type, techlevel, description)
    VALUES (:parent, :name, :type, :techlevel, :description)");

    if(empty($parent)) {return returnError("Missing parent");}
    if(empty($name)) {return returnError("No name");}
    if(empty($techlevel)) {return returnError("Techlevel required");}
    if($techlevel > 10) {return returnError("Techlevel too high: $techlevel");}
    if($techlevel < 1) {return returnError("Techlevel too low: $techlevel");}

    $db->bind(':parent', $parent);
    $db->bind(':name', $name);
    $db->bind(':type', $type);
    $db->bind(':techlevel', floor($techlevel));
    $db->bind(':description', $description);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return returnSuccess("$name was added");
  }

  public function generatePlanets($count) {
    global $systNames;
    $i=0;
    $planets = [];
    while ($i<$count) {
      $planet = [];
      $planet['name'] = $systNames[array_rand($systNames)];
      $planet['desc'] = 'TEST';
      $planet['techlevel'] = floor(rand(1,10));
      $planets[] = $planet;
      $i++;
    }
    return $planets;
  }

  public function generateStation($count) {
    global $companyNames;
    global $stationNames;
    global $stationAdjectives;
    global $phoneticAlphabet;
    global $greekAlphabet;
    global $romanNumerals;
    $i=0;
    $stations = [];
    while ($i<$count) {
    $isCompany = (bool)rand(0,1);
    if ($isCompany === true) { //This station needs a company prefix and desc.
      $company = $companyNames[array_rand($companyNames)];
      $stationName = $stationNames[array_rand($stationNames)];
      //TODO: Description generators
      /*

      $name is widely regarded as the crown jewel of $company.

      $company spent 10 years and 20 billion credits to build $name. Today it lies almost completely dormant, a monument to excess.

      The $company's security forces glare at you, uncertain of your intentions on $name. They don't get many visitors here.

      "Welcome to $name" blares the automated attendant. "Thenk you for choosing $company, we hope you enjoy your stay!"



      */
    } else { //Just a regular station.
      $company = $stationAdjectives[array_rand($stationAdjectives)];
      $stationName = $stationNames[array_rand($stationNames)];
      //TODO: Description generators
      /*

      Seven hundred and sixty one armless and legless corpses float inconspicuously around the inside of hangar $name

      The bar on $name is widely known for its

      */
    }
    $suffix = floor(rand(1,4));
    if ($suffix == 1) {
      $sfx = $phoneticAlphabet[array_rand($phoneticAlphabet)];
    } elseif ($suffix == 2) {
      $sfx = $greekAlphabet[array_rand($greekAlphabet)];
    } elseif ($suffix == 3) {
      $sfx = $romanNumerals[array_rand($romanNumerals)];
    } else {
      $sfx = '';
    }
    $i++;
    $station = [];
    $station['name'] = $company." ".$stationName." ".$sfx;
    $station['desc'] = 'TEST';
    $station['techlevel'] = floor(rand(1,10));
    $stations[] = $station;
    }
    return $stations;
  }

  public function getSpobByName($name) {
    $db = new database();
    $db->query("SELECT tbl_spob.id FROM tbl_spob
      WHERE tbl_spob.name LIKE ? LIMIT 0,1");
    $db->bind(1,'%'.$name.'%');
    try {
      $db->execute();
    } catch (Exception $e) {
      return "Database error: ".$e->getMessage();
    }
    $id = $db->single();
    if (!$id) {
      return false;
    } else {
      return $id->id;
    }
  }

}
