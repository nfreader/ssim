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

  public $parent;
  public $govt;

  public function __construct($id=null) {
    if (isset($id)) {
      $spob = $this->getSpob($id);
      $this->id = $spob->id;
      $this->name = $spob->name;
      $this->techlevel = $spob->techlevel;
      $this->type = $spob->type;
      $this->description = $spob->description;
      $this->homeworld = $spob->homeworld;
  
      $this->fuelcost = fuelcost($spob->techlevel,$spob->type);
      $this->nodeid = hexPrint($spob->id.$spob->name);
      $this->fullname = spobName($spob->name,$spob->type);

      $this->parent = new stdclass();
      $this->parent->id = $spob->parent;
      $this->parent->name = $spob->system;

      $this->govt = new stdclass();
      $this->govt->name = $spob->govtname;
      $this->govt->color1 = $spob->color1;
      $this->govt->color2 = $spob->color2;
      $this->govt->iso = $spob->isoname;
      $this->govt->id = $spob->govt;
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
    $db->query("SELECT tbl_spob.*,
      tbl_syst.name AS system,
      tbl_syst.govt,
      tbl_govt.name AS government,
      tbl_govt.name AS govtname,
      tbl_govt.color1 AS color1,
      tbl_govt.color2 AS color2,
      tbl_govt.isoname
      FROM tbl_spob
      LEFT JOIN tbl_syst ON tbl_spob.parent = tbl_syst.id
      LEFT JOIN tbl_govt ON tbl_syst.govt = tbl_govt.id
      WHERE tbl_spob.id = :spob");
    $db->bind(':spob',$spob);
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
    if (empty($parent)
      || empty($name)
      || empty($techlevel)
      || $techlevel < 10
      || $techlevel > 1) {
      return false;
    } else {
      $db->bind(':parent', $parent);    
      $db->bind(':name', $name);
      $db->bind(':type', $type);
      $db->bind(':techlevel', floor($techlevel));
      $db->bind(':description', $description);
      if ($db->execute()) {
        return $name. " was added.";
      } else {
        return "Something went wrong. Spob not added";
      }
      
    }
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

}