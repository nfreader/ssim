<?php 

class User {

  public $id;
  public $rank;
  public $status;

  public function __construct() {
    if(isset($_SESSION['userid'])) {
      $this->id = $_SESSION['userid'];
      $this->status = $_SESSION['status'];
    }
    else {
      return "No session detected";
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
    $db->query("SELECT COUNT(*) AS count
      FROM ssim_user WHERE username = :username OR email = :email");
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

      if ($db->countRows('ssim_user') == 1) {
        //We need to get the user's ID
        $db->query("SELECT id FROM ssim_user WHERE username = :username");
        $db->bind(':username',$username);
        $db->execute();
        $count = $db->single();
        $this->makeAdmin($count->id);
        $this->activateUser($count->id);
      }
      $game = new game();
      $game->logEvent('NU','Registered');
      return "You are now registered. Please log in.";
    } else {
      return "This username or email address is already in use.";
    }
  }
  
  public function logIn($username, $password) {
    $db = new database();
    $db->query("SELECT username, salt FROM ssim_user WHERE username = :username");
    $db->bind(':username',$username);
    $db->execute();
    $check = $db->single();
    if ($check == array()) {
      return "Username or password invalid.";
    } else {
      $db->query("SELECT id, username, email, rank, status FROM ssim_user
      WHERE password = :password AND username = :username");
      $db->bind(':password', hash('sha512', $check->salt . $password));
      $db->bind(':username', $username);
      $db->execute();
      $login = $db->single();
      if ($login === array()) {
        return "Username or password invalid";
      } else {
        $_SESSION['username'] = $login->username;
        $_SESSION['userid'] = $login->id;
        $this->id = $login->id;
        $_SESSION['rank'] = $login->rank;
        $_SESSION['status'] = $login->status;
        if($this->isAdmin()){
          $_SESSION['sudo_mode'] = false;
        }
        $game = new game();
        $game->logEvent('LI','Logged in');
        if ($login->status == 0) {
          return "You are now logged in as ".$login->username.". Your account is awaiting activation.";
        } else {
          return "You are now logged in as ".$login->username;
        }
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

  public function logOut(){
    $game = new game();
    $game->logEvent('LO','Logged out');
    $_SESSION = '';
    session_destroy();
    return "\$this-\>session-\>terminate()";
  }

}