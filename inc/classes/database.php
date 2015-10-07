<?php 

class database {

  private $dbhost = DB_HOST;
  private $dbname = DB_NAME;
  private $dbuser = DB_USER;
  private $dbpass = DB_PASS;
  private $dbmethod = DB_METHOD;
  private $prefix = TBL_PREFIX;

  private $dbh;
  private $error;

  private $stmt;

  public function __construct() {
    $dbs = $this->dbmethod.":
    host=".$this->dbhost.";
    dbname=".$this->dbname.";charset=utf8mb4;collation=utf8mb4_unicode_ci;";

    $options = array(
      \PDO::ATTR_PERSISTENT => true,
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
    );

    try {
      $this->dbh = new \PDO($dbs, $this->dbuser, $this->dbpass, $options);
    }

    catch (\PDOException $e) {
      $this->error = $e->getMessage();
    }

  }

  public function query($query) {
    $query = str_replace('tbl_', TBL_PREFIX, $query);
    $this->stmt = $this->dbh->prepare($query);
  }

  public function bind($param, $value, $type = null) {
    if (is_null($type)) {
      switch (true) {
        case is_int($value):
          $type = \PDO::PARAM_INT;
          break;
        case is_bool($value):
          $type = \PDO::PARAM_BOOL;
          break;
        case is_null($value):
          $type = \PDO::PARAM_NULL;
          break;
        default: 
          $type = \PDO::PARAM_STR;
      }
    }
    $this->stmt->bindValue($param, $value, $type);
  }

  public function execute() {
    return $this->stmt->execute();
  }

  public function resultSet($mode=\PDO::FETCH_OBJ) {
    $this->execute();
    return $this->stmt->fetchAll($mode);
  }

  public function single($mode=\PDO::FETCH_OBJ) {
    $this->execute();
    return $this->stmt->fetch($mode);
  }

  public function rowCount(){
    return $this->stmt->rowCount();
  }

  public function lastInsertId() {
    return $this->dbh->lastInsertId();
  }

  public function beginTransaction(){
    return $this->dbh->beginTransaction();
  }

  public function endTransaction() {
    return $this->dbh->commit();
  }

  public function cancelTransaction() {
    return $this->dbh->rollBack();
  }

  public function debugDumpParams() {
    return $this->stmt->debugDumpParams();
  }

  public function countRows($table) {
    $database = new database();
    $database->query("SELECT COUNT(*) AS num FROM $table");
    $database->execute();
    return $database->single()->num;
  }  

}