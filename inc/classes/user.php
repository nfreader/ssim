<?php 

class User {

  private $id;
  private $rank;

  public function __construct() {
    if(isset($_SESSION['userid'])) {
      $this->id = $_SESSION['userid'];
    }
  }

  public function isLoggedIn() {
    if ((isset($_SESSION['username'])) && (isset($_SESSION['userid'])) && $_SESSION['status'] == 1) {
      return true;
    }
  }

  public function isAdmin() {
    $db = new database();
    $db->query("SELECT rank FROM ssim_user WHERE ssim_user.id = :id");
    $db->bind(':id',$this->id);
    if ($db->single()->rank === 'A') {
      return true;
    }
  }

  public function isUnique($username, $email) {
    $db = new database();
    $db->query("SELECT COUNT(*) AS count FROM ssim_user WHERE username = :username OR email = :email");
    $db->bind(':username', $username);
    $db->bind(':email', $email);
    $db->execute();
    if ($db->single()->count == 0) {
      return true;
    } 
  }

  public function registerNewUser($username, $password, $email) {
    if($this->isUnique($username, $email)) {
      $salt = getSalt();
      $db = new database();
      $db->query("INSERT INTO ssim_user
      (username, password, email, salt, timestamp) VALUES 
      (:username, :password, :email, :salt, NOW())");
      $db->bind(':username',$username);
      $db->bind(':password',hash('sha512', $salt . $password));
      $db->bind(':email',$email);
      $db->bind(':salt',$salt);
      $db->execute();

      echo "<div class='alert alert-success'>You are now registered.
      <a href='index.php'>Please log in</a></div>";
      if ($db->countRows('ssim_user') == 1) {
        //We need to get the user's ID
        $db->query("SELECT id FROM ssim_user WHERE username = :username");
        $db->bind(':username',$username);
        $db->execute();
        $count = $db->single();
        $this->makeAdmin($count->id);
        $this->activateUser($count->id);
      }
    } else {
      echo "<div class='alert alert-danger'>This username or
      email address is already in use.</div>";
    }
  }
  
  public function logIn($username, $password) {
    $db = new database();
    $db->query("SELECT username, salt FROM ssim_user WHERE username = :username");
    $db->bind(':username',$username);
    $db->execute();
    $check = $db->single();
    if ($check == array()) {
      echo "<div class='alert alert-danger'>Username or
      password invalid.</div>";
      return false;
    } else {
      $db->query("SELECT id, username, email, rank, status FROM ssim_user
      WHERE password = :password AND username = :username");
      $db->bind(':password', hash('sha512', $check->salt . $password));
      $db->bind(':username', $username);
      $db->execute();
      $login = $db->single();
      if ($login === array()) {
        echo "<div class='alert alert-danger'>Username or
        password invalid.</div>";
        return false;
      } else {
        $_SESSION['username'] = $login->username;
        $_SESSION['userid'] = $login->id;
        $_SESSION['rank'] = $login->rank;
        $_SESSION['status'] = $login->status;
        if ($login->status == 0) {
          echo "<div class='alert alert-info'>You are now logged in as 
        ".$login->username.". The site administrator must activate your account before you can continue.</div>";
        } else {
          echo "<div class='alert alert-success'>You are now logged in as 
        ".$login->username.". <a href='index.php'>Continue</a></div>";
        }
        return true;
      }
    }
  }
  private function makeAdmin($id) {
    $database = new database();
    $database->query('UPDATE ssim_user SET rank = "A" WHERE id = :user');
    $database->bind(':user',$id);
    $database->execute();
    return $database->rowCount();
  }

  public function activateUser($id) {
    if (!$this->isAdmin($id)){
      echo "<div class='alert alert-danger'>Users can only be activated by administrators.</div>";
    } else {
      $database = new database();
      $database->query("UPDATE ssim_user SET status = 1 WHERE id = :user");
      $database->bind(':user',$id);
      $database->execute();

      if ($id != $_SESSION['userid']) {
        $name = $this->getUserProfile(NULL,$id);
        $database->query("DELETE FROM ssim_session 
          WHERE session_data LIKE '%".$name->username."%'");
        $database->execute();
      }

      echo "<div class='alert alert-success'>".$name->username." has been activated.</div>";

    }
  }
  public function deactivateUser($id) {
    if (!$this->isAdmin()){
      echo "<div class='alert alert-danger'>Users can only be deactivated by administrators.</div>";
    } else {
      $database = new database();
      $database->query("UPDATE ssim_user SET status = 0 WHERE id = :user");
      $database->bind(':user',$id);
      $database->execute();

      if ($id != $_SESSION['userid']) {
        $name = $this->getUserProfile(NULL,$id);
        $database->query("DELETE FROM ssim_session 
          WHERE session_data LIKE '%".$name->username."%'");
        $database->execute();
      }

      echo "<div class='alert alert-success'>".$name->username." has been deactivated.</div>";

    }
  }
}