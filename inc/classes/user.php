<?php

class user {

  public $uid;
  public $status;
  public $rank;
  public $activepilot;

  public function __construct() {
    if(isset($_SESSION['uid'])) {
      $this->uid = $_SESSION['uid'];
      $this->status = $_SESSION['status'];
      $this->rank = $_SESSION['rank'];
      if (empty($_SESSION['pilotuid'])) {
        $this->activepilot = NULL;
      } else {
        $this->activepilot = $_SESSION['pilotuid'];
      }
    }
    else {
      return "No session detected";
    }
  }

  public function register($username, $password, $password2, $email) {

    $username = filter_var($username,FILTER_SANITIZE_STRING,FILTER_FLAG_ENCODE_LOW);
    if ('' === empty($username)) {
      return returnError("Username is invalid");
    }

    if (trim($password) == '') {
      return array('Password cannot be empty.',2);
    }

    if ($password != $password2) {
      return array('Passwords do not match!',2);
    }

    if (trim($email) == '') {
      return array('Email cannot be empty.',2);
    }

    if (!$this->isUnique($username,$email)) {
      return array('Email address or username already in use.',2);
    }

    $db = new database();
    $db->query("INSERT INTO tbl_user (
        uid,
        username,
        password,
        email,
        created
      ) VALUES (
        substr(sha1(uuid()),4,12),
        ?,
        ?,
        ?,
        NOW()
      )");
    $db->bind(1,$username);
    $db->bind(2,password_hash($password,PASSWORD_DEFAULT));
    $db->bind(3,$email);

    try {
      $db->execute();
    } catch (Exception $e) {
      return array("Database error: ".$e->getMessage(),1);
    }

    $game = new game();
    $game->logEvent("UR","Registered");

    if (SSIM_DEBUG) {
      $this->activateUser($this->getUIDByUsername($username));
      $return[] = array(
        'message'=>"Your account has been created and activated. Please log in.",
        'level'=>1
      );
    } else {
      $return[] = array(
        'message'=>"An email to activate your account has been sent to the address you provided.",
        'level'=>1
      );
    }
    if(1 == $db->countRows('tbl_user')) {
      $db->query("SELECT uid FROM tbl_user WHERE username = :username");
      $db->bind(':username',$username);
      $db->execute();
      $uid = $db->single()->uid;
      $db->query("UPDATE tbl_user SET status = 1, rank = 'A'
        WHERE uid = ?");
      $db->bind(1,$uid);
      $db->execute();
      $return[] = array(
        'message'=>"Initial user detected. You have been promoted to administrator and activated. Please log in now.",
        'level'=>1
      );
      $game->logEvent("PA","Initial user promoted to admin");
    }
    return $return;
  }

  public function activateUser($uid) {
    $db = new database();
    $db->query("UPDATE tbl_user SET status = 1 WHERE uid = ?");
    $db->bind(1,$uid);
    $db->execute();
    $game = new game();
    $game->logEvent("UA","User activated");
  }

  public function getUIDByUsername($username){
    $db = new database();
    $db->query("SELECT uid FROM tbl_user WHERE username = ?");
    $db->bind(1,$username);
    $db->execute();
    return $db->single()->uid;
  }

  public function isUnique($username, $email) {
    $db = new database();
    $db->query("SELECT COUNT(*) AS count
      FROM tbl_user WHERE username = :username OR email = :email");
    $db->bind(':username', $username);
    $db->bind(':email', $email);
    $db->execute();
    if (0 == $db->single()->count) {
      return true;
    } else {
      return false;
    }
  }

  public function isLoggedIn() {
    if (isset($this->uid) && 1 == $this->status) {
      return true;
    }
  }

  public function isAdmin() {
    if ("A" === $this->rank) {
      $db = new database();
      $db->query("SELECT rank FROM tbl_user WHERE tbl_user.uid = :id");
      $db->bind(':id',$this->uid);
      if ($db->single()->rank === 'A') {
        return true;
      }
    } else {
      return false;
    }
  }

  public function login($username, $password) {
    $db = new database();
    $db->query("SELECT password FROM tbl_user
      WHERE username = :username");
    $db->bind(':username',$username);
    $db->execute();
    $user = $db->single();
    if (!$user) {
      $return[] = array(
        'message'=>"Incorrect password.",
        'level'=>2
      );
      return $return;
    }
    if(!password_verify($password, $user->password)) {
      $return[] = array(
        'message'=>"Incorrect password.",
        'level'=>2
      );
      return $return;
    } else {
      $db->query("SELECT uid, username, email, rank, status
      FROM tbl_user
      WHERE username = :username");
      $db->bind(':username', $username);
      $db->execute();
      $login = $db->single();

      $_SESSION['username'] = $login->username;
      $_SESSION['uid'] = $login->uid;
      $this->uid = $login->uid;
      $_SESSION['rank'] = $login->rank;
      $_SESSION['status'] = $login->status;
      if($this->isAdmin()){
        $_SESSION['sudo_mode'] = false;
      }
      if ($login->status == 0) {
        $return[] = array(
          'message'=>"You are now logged in as $login->username. Your account is awaiting activation.",
          'level'=>'normal'
        );
      } else {
        $return[] = array(
          'message'=>"You are now logged in as $login->username.",
          'level'=>'normal'
        );
      }
      $game = new game();
      $game->logEvent("LI","Logged in");
      return $return;
    }
  }

  public function logOut(){
    $game = new game();
    $game->logEvent("LO","Logged out");
    $_SESSION = '';
    session_destroy();
    $return[] = array(
      'message'=>"sys.session(user)->terminate();",
      'level'=>1
    );
    return $return;
  }

  public function issuePasswordReset($email) {
    $email = $this->getUserByEmail($email);
    if(!$email) {
      $return[] = array(
        'message'=>"Unable to find the specified user.",
        'level'=>2
      );
      return $return;
    }

    $link = $this->generatePasswordReset($email->id);
    if(!$link) {
      $return[] = array(
        'message'=>"Unable to generate a new password reset link.",
        'level'=>2
      );
      return $return;
    }
    $to = $email->email;
    $subject = APP_NAME." password reset";
    $message = "<strong>$subject</strong><br>--------<br>";
    $message.= "If you need to reset your passoword for ".APP_NAME." ";
    $message.= "please click the link below and follow the instructions. ";
    $message.= "If you did not request a password reset, please disregard ";
    $message.= "this message.<br>--------<br>";
    $message.= "<a href='".APP_URL."?action=resetPassword&link=$link'>Reset Password</a> <em>This link will expire in 15 minutes</em>";

    $app = new app();
    try{
      $app->systemMail($email->email,$subject,$message);
    } catch (Exception $e) {
      $return[] = array(
        'message'=>"Unable to send password reset. ".$e->getMessage(),
        'level'=>2
      );
      return $return;
    }
    $return[] = array(
      'message'=>"A link to reset your password has been sent.",
      'level'=>1
    );
    return $return;
  }

  public function getUserByEmail($email) {
    $db = new database();
    $db->query("SELECT id, username, email FROM tbl_user WHERE email = :email");
    $db->bind(':email',$email);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'message'=>"Unable to find email address.".$e->getMessage(),
        'level'=>2
      );
      return $return;
    }
    return $db->single();
  }

  public function generatePasswordReset($user) {
    $link = generatePasswordResetLink();
    $db = new database();
    $db->query("INSERT INTO tbl_passwordresets (user, link, timestamp)
      VALUES (:user, :link, NOW())");
    $db->bind(':user',$user);
    $db->bind(':link',$link);
    try {
      $db->execute();
    } catch (Exception $e) {
      return false;
    }
    return $link;
  }

  public function isPasswordResetValid($link) {
    $db = new database();
    $db->query("SELECT *,
      CASE WHEN (tbl_passwordresets.timestamp >= NOW() - INTERVAL 15 MINUTE)
      THEN 1
      ELSE 0
      END AS valid
      FROM tbl_passwordresets
      WHERE tbl_passwordresets.link = :link");
    $db->bind(':link',$link);
    try {
      $db->execute();
    } catch (Exception $e) {
      return false;
    }
    $link = $db->single();
    if(FALSE == $link->valid) {
      $this->deletePasswordResetLink($link);
      return false;
    }
    return true;
  }

  public function deletePasswordResetLink($link) {
    $db = new database();
    $db->query("DELETE FROM tbl_passwordresets WHERE link = :link");
    $db->bind(':link',$link);
    try {
      $db->execute();
    } catch (Exception $e) {
      return false;
    }
    return true;
  }

  public function resetPassword($link, $password, $password2) {
    if ($password != $password2) {
      $return[] = array(
        'message'=>"Passwords must match!",
        'level'=>2
      );
      return $return;
    }
    if ('' === trim($password)) {
      $return[] = array(
        'message'=>'Password cannot be empty!',
        'level'=>2
      );
      return $return;
    }
    if (!$this->isPasswordResetValid($link)){
      $return[] = array(
        'message'=>"This link has expired.",
        'level'=>2
      );
      return $return;
    }

    $db = new database();
    $db->query("SELECT * FROM tbl_passwordresets WHERE link = :link");
    $db->bind(':link',$link);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'message'=>"Unable to find password reset. ".$e->getMessage(),
        'level'=>2
      );
      return $return;
    }
    $user = $db->single();
    $this->deletePasswordResetLink($user->link);
    $db->query("UPDATE tbl_user SET password = :password
      WHERE id = :user");
    $db->bind(':password',password_hash($password,PASSWORD_DEFAULT));
    $db->bind(':user',$user->user);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'message'=>"Unable to reset password. ".$e->getMessage(),
        'level'=>2
      );
      return $return;
    }
    $return[] = array(
      'message'=>"Your password has been reset. Please log in.",
      'level'=>1
    );
    return $return;
  }

  public function userCanMange($user,$permission) {
    $db = new database();
    $db->query("SELECT * FROM sf_permissions
      WHERE user = ?");
  }

}
