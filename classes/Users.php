<?php
require_once dirname(__DIR__) . "/lib/Database.php";
require_once dirname(__DIR__) . "/lib/Session.php";
require_once dirname(__DIR__) . "/config/config.php";
/* Users operate on public_id */
class Users {

  private $db;
  private $userColumns = "id, public_id, fullname, username, email, phone, roleid, is_disabled, is_firstrun, stripe_id, created_at, updated_at";

  public function __construct() {
    $this->db = new Database();
  }
  public function checkUserRoleByPublicId($publicId){
    if (!isset($publicId) || $publicId == "") {
      error_log("Could not find real id for user when public id not set");
      return false;
    }
    $sql = "SELECT roleid FROM tbl_users WHERE public_id = :publicid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':publicid', $publicId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
      if ($result) {
      return $result[0]->roleid;
    } else {
      error_log("Could not find real id for user with public id " . $publicId);
      return false;
    }
  }
  public function getRealId($publicId) {
    if (!isset($publicId) || $publicId == "") {
      error_log("Could not find real id for user when public id not set");
      return false;
    }
    $sql = "SELECT id FROM tbl_users WHERE public_id = :publicid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':publicid', $publicId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result[0]->id;
    } else {
      error_log("Could not find real id for user with public id " . $publicId);
      return false;
    }
  }

  public function checkEmailExists($email) {
    $sql = "SELECT email from tbl_users WHERE LOWER(email) = LOWER(:email)";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount()> 0) {
      return true;
    } else {
      return false;
    }
  }

  public function checkUsernameExists($username) {
    $sql = "SELECT username from tbl_users WHERE username = :username";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    if ($stmt->rowCount()> 0) {
      return true;
    } else {
      return false;
    }
  }

  

  public function createUser($data,$paymentId, $byAdmin) {
    

    if ($data['fullname'] == "" || $data['username'] == "" || $data['email'] == "" || $data['phone'] == "" || $data['password'] == "") {
      return createUserMessage("error", "All fields are required!");
    } elseif (containsBadWords($data['fullname'])) {
      return createUserMessage("error", "Name should not contain foul language.");
    } elseif (strlen($data['username']) < 3) {
      return createUserMessage("error", "User name must be at least 3 characters.");
    } elseif (containsBadWords($data['username'])) {
      return createUserMessage("error", "User name should not contain foul language.");
    } elseif (filter_var($data['phone'], FILTER_SANITIZE_NUMBER_INT) == FALSE) {
      return createUserMessage("error", "Phone number must be numeric.");
    } elseif(strlen($data['password']) < 8) {
      return createUserMessage("error", "Password must be at least 8 characters long, and contain at least one alphabetic character and one number.");
    } elseif(!preg_match("#[0-9]+#", $data['password'])) {
      return createUserMessage("error", "Password must be at least 8 characters long, and contain at least one alphabetic character and one number.");
    } elseif(!preg_match("#[a-z]+#", $data['password'])) {
      return createUserMessage("error", "Password must be at least 8 characters long, and contain at least one alphabetic character and one number.");
    } elseif (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === FALSE) {
      return createUserMessage("error", "Please provide a valid email address.");
    } elseif ($this->checkEmailExists($data['email']) == TRUE) {
      return createUserMessage("error", "A user has already registered with that email address. Please login, or register with a different email.");
    } elseif ($this->checkUsernameExists($data['username']) == TRUE) {
      return createUserMessage("error", "A user has already registered with that username. Please login, or register with a different username.");
    } else {
      $sql = "INSERT INTO tbl_users(public_id, fullname, username, email, password, phone, roleid,payment_id) 
        VALUES (:publicid, :fullname, :username, :email, :password, :phone, :roleid,:payment_id)";
      $stmt = $this->db->pdo->prepare($sql);
      $snowflake = new \Godruoyi\Snowflake\Snowflake;
      $stmt->bindValue(':publicid', $snowflake->id());
      $stmt->bindValue(':fullname', $data['fullname']);
      $stmt->bindValue(':username', $data['username']);
      $stmt->bindValue(':email', $data['email']);
      $stmt->bindValue(':password', $this->hashPassword($data['password']));
      $stmt->bindValue(':phone', $data['phone']);
      $stmt->bindValue(':roleid', 3);
      $stmt->bindValue(':payment_id',$paymentId);
      $result = $stmt->execute();
      if ($result) {
        //TODO: email verification unless $byAdmin == TRUE
        if ($byAdmin != TRUE) {
          Session::set('pendingMsg', createUserMessage("success", "Registration complete. You may now login."));
          return "<script>location.href='login.php';</script>";
        } else {
          return createUserMessage("success", "Registration complete. User may now login.");
        }
      } else {
        return createUserMessage("error", "Registration not complete. Something went wrong!");
      }
    }
  }

  public function hashPassword($password) {
    $password = hash("sha256", SHA_SEED.$password);
    return $password;
  }

  // Get All User Data (except password)
  public function getAllUserData() {
    $sql = "SELECT $this->userColumns FROM tbl_users ORDER BY id DESC";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }
  public function updateOrCreateTokenByEmail($token,$email){
   $sql = "UPDATE tbl_users SET reset_token = :token WHERE LOWER(email) = LOWER(:email); ";
   $stmt = $this->db->pdo->prepare($sql);
   $stmt->bindValue(':email',$email);
   $stmt->bindValue(':token',$token);
   $stmt->execute();
  }
  // User Authentication Method
  public function userAuthentication($data, $partnerKey) {
    $email = $data['email'];
    $password = $data['password'];

    if ($email == "" || $password == "" ) {
      return createUserMessage("error", "Please provide both email and password!");
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
      return createUserMessage("error", "Invalid email address!");
    } elseif ($this->checkEmailExists($email) == FALSE) {
      return createUserMessage("error", "Could not find a user with that email address.");
    } else {

      $loginResult = $this->tryUserCredentials($email, $password);
      $userDisabled = false;
      if (isset($loginResult) && isset($loginResult->is_disabled))
        $userDisabled = $loginResult->is_disabled;

      if ($userDisabled == TRUE) {
        return createUserMessage("error", "Your account has been disabled. Please contact Bennit for more information.");
      } elseif ($loginResult) {
        Session::init();
        Session::set('login', TRUE);
        Session::set('userid', $loginResult->public_id);
        Session::set('roleid', $loginResult->roleid);
        Session::set('fullname', $loginResult->fullname);
        Session::set('email', $loginResult->email);
        Session::set('username', $loginResult->username);
        Session::set('firstrun', $loginResult->is_firstrun);
        Session::set('partnerKey', $partnerKey);
        //Session::set('pendingMsg', createUserMessage("success", "Log-in success!"));
        echo "<script>location.href='index.php';</script>";

      } else {
        return createUserMessage("error", "Could not find a user with those credentials.");
      }
    }
  }

  // User login
  public function tryUserCredentials($email, $password) {
    $password = $this->hashPassword($password);
    $sql = "SELECT $this->userColumns FROM tbl_users WHERE email = :email and password = :password LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':password', $password);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function checkUserActive($email) {
    $sql = "SELECT $this->userColumns FROM tbl_users WHERE email = :email and is_disabled = :is_disabled LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':is_disabled', 1);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }
  public function getUserInfoByRealId($user_real_id){
  $sql = "SELECT * FROM tbl_users WHERE id = :real_id";
  $stmt = $this->db->pdo->prepare($sql);
  $stmt->bindValue(':real_id',$user_real_id);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_OBJ);
    if ($result) {
      return $result;
    } else {
      return false;
    }
  }
  public function getUserInfoById($userId){
    $sql = "SELECT $this->userColumns FROM tbl_users WHERE public_id = :id LIMIT 1";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $userId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    if ($result) {
      return $result;
    } else {
      return false;
    }
  }
  public function getUserPublicIdByEmail($email){
  $sql = "SELECT public_id FROM tbl_users WHERE LOWER(email) = LOWER(:email);";
  $stmt = $this->db->pdo->prepare($sql);
  $stmt->bindValue(':email',$email);
  $stmt->execute();
  return $stmt->fetch(PDO::FETCH_OBJ);
  }
  public function updateFirstRunStatus($userId, $firstrunstatus) {
    if (is_numeric($userId) && ($firstrunstatus == 0 || $firstrunstatus == 1)) {
      $sql = "UPDATE tbl_users SET is_firstrun = " . $firstrunstatus;
      $sql .= " WHERE public_id = :userid;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':userid', $userId);
      $result = $stmt->execute();
      if (!$result)
        return createUserMessage("error", "User's first run status could not be updated.");
    } else {
      return createUserMessage("error", "User ID or first run status invalid.");
    }
  }

  public function updateStripeCustomerId($userId, $stripeId) {
    $sql = "UPDATE tbl_users SET stripe_id = :stripeid ";
    $sql .= " WHERE public_id = :userid;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':userid', $userId);
    $stmt->bindValue(':stripeid', $stripeId);
    $result = $stmt->execute();
    if (!$result)
      return createUserMessage("error", "User's Stripe information could not be updated.");
  }

  public function updateUserById($userId, $data) {
    $publicId = $userId;
    $userId = $this->getRealId($userId);
    if ($data['fullname'] == "" || $data['username'] == ""|| $data['email'] == "" || $data['phone'] == ""  ) {
      return createUserMessage("error", "All fields are required!");
    } elseif (containsBadWords($data['fullname'])) {
      return createUserMessage("error", "Name should not contain foul language.");
    } elseif (strlen($data['username']) < 3) {
      return createUserMessage("error", "User name must be at least 3 characters.");
    } elseif (containsBadWords($data['username'])) {
      return createUserMessage("error", "User name should not contain foul language.");
    } elseif (filter_var($data['phone'], FILTER_SANITIZE_NUMBER_INT) == FALSE) {
      return createUserMessage("error", "Phone number must be numeric.");
    } elseif (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === FALSE) {
      return createUserMessage("error", "Please provide a valid email address.");
    } elseif (isset($data['roleid']) && filter_var($data['roleid'], FILTER_SANITIZE_NUMBER_INT) === FALSE) {
      return createUserMessage("error", "Invalid user level.");
    } else {

      $sql = "UPDATE tbl_users SET
        fullname = :fullname,
        username = :username,
        phone = :phone,
        email = :email";
      if (Session::get('roleid') == 1 && isset($data['roleid']))  
        $sql .= ", roleid = :roleid";
      $sql .= " WHERE id = :id;";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':fullname', $data['fullname']);
      $stmt->bindValue(':username', $data['username']);
      $stmt->bindValue(':email', $data['email']);
      $stmt->bindValue(':phone', $data['phone']);
      if (Session::get('roleid') == 1 && isset($data['roleid']))
        $stmt->bindValue(':roleid', $data['roleid']);
      $stmt->bindValue(':id', $userId);
      $result = $stmt->execute();

      if ($result) {
        Session::set('pendingMsg', createUserMessage("success", "User updated."));
        return "<script>location.href='userProfile.php?userid=$publicId';</script>";
      } else {
        return createUserMessage("error", "User could not be updated. Something went wrong!");
      }
    }
  }

  public function deleteUserById($userId){
    $sql = "DELETE FROM tbl_users WHERE public_id = :id ";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $userId);
    $result = $stmt->execute();
    if ($result) {
      return createUserMessage("success", "User deleted.");
    } else {
      return createUserMessage("error", "User not deleted. Something went wrong!");
    }
  }

  // User Disabled By Admin
  public function disableUser($userId){
    $userId = $this->getRealId($userId);
    if ($userId) {
      if (Session::get('roleid') == 1) {
        $sql = "UPDATE tbl_users SET is_disabled=1 WHERE public_id = :id";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':id', $userId);
        $result = $stmt->execute();
        if ($result) {
          return createUserMessage("success", "User disabled.");
        } else {
          return createUserMessage("error", "User not disabled. Something went wrong!");
        }
      }
    } else {
      return createUserMessage("error", "User not changed. A real user could not be found!");
    }
  }

  // User Enabled By Admin
  public function enableUser($userId){
    $userId = $this->getRealId($userId);
    if ($userId) {
      if (Session::get('roleid') == 1) {
        $sql = "UPDATE tbl_users SET is_disabled=0 WHERE public_id = :id";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':id', $userId);
        $result =   $stmt->execute();
        if ($result) {
          return createUserMessage("success", "User enabled.");
        } else {
          return createUserMessage("error", "User not enabled. Something went wrong!");
        }
      }
    } else {
      return createUserMessage("error", "User not changed. A real user could not be found!");
    }
  }
  //verify token
 public function verifyToken($token, $userid) {
    // Prepare the SQL query to retrieve reset_token for the specified userid
    $sql = "SELECT reset_token FROM tbl_users WHERE id = :userid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the result (reset_token) from the query
    $resetToken = $stmt->fetchColumn();

    // Check if a token was retrieved for the specified userid
    if ($resetToken !== false && $resetToken === $token) {
        return true; // Token matches and is valid
    } else {
        return false; // Token does not match or user not found
    }
}

  //change password  by token
  public function changePasswordByToken($userid,$token,$data){
    $userID = $this->getRealId($userid);
    $token_status = $this->verifyToken($token,$userID);
    if($token_status){
      $new_pass = $data['new_password'];
      $repeat_pass = $data['repeat_password'];
      if ($new_pass == "" )
        return createUserMessage("error", "New password may not be blank.");
      if ($new_pass != $repeat_pass ) {
        return createUserMessage("error", "New password did not match repeat password.");
      } elseif(strlen($new_pass) < 6) {
        return createUserMessage("error", "Password must be at least 6 characters long, and contain at least one alphabetic character and one number.");
      } elseif(!preg_match("#[0-9]+#", $new_pass)) {
        return createUserMessage("error", "Password must be at least 6 characters long, and contain at least one alphabetic character and one number.");
      } elseif(!preg_match("#[a-z]+#", $new_pass)) {
        return createUserMessage("error", "Password must be at least 6 characters long, and contain at least one alphabetic character and one number.");
      }else{
        $new_pass = $this->hashPassword($new_pass);
        $sql = "UPDATE tbl_users SET password=:password WHERE id = :id";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':password', $new_pass);
        $stmt->bindValue(':id', $userID);
        $result = $stmt->execute();
        if ($result) {
          Session::set('pendingMsg', createUserMessage("success", "Password changed."));
          return "<script>location.href='login.php';</script>";
        } else {
          return createUserMessage("error", "Password not changed. Something went wrong!");
        }

      }
    }

  }

  // Change Password By User Id
  public function changePasswordById($userId, $data){
    $publicId = $userId;
    $userId = $this->getRealId($userId);
    if ($userId) {
      $oldPass = false;
      if (!array_key_exists("old_password", $data) || $data['old_password'] == "") {
        if (Session::get('roleid') != 1)
          return createUserMessage("error", "Old password may not be blank.");
        else
          $oldPass = true;
      } else {
        $oldPass = $this->checkOldPassword($userId, $data['old_password']);
      }
      $new_pass = $data['new_password'];
      $repeat_pass = $data['repeat_password'];
      if ($new_pass == "" )
        return createUserMessage("error", "New password may not be blank.");
      if ($new_pass != $repeat_pass ) {
        return createUserMessage("error", "New password did not match repeat password.");
      } elseif(strlen($new_pass) < 6) {
        return createUserMessage("error", "Password must be at least 6 characters long, and contain at least one alphabetic character and one number.");
      } elseif(!preg_match("#[0-9]+#", $new_pass)) {
        return createUserMessage("error", "Password must be at least 6 characters long, and contain at least one alphabetic character and one number.");
      } elseif(!preg_match("#[a-z]+#", $new_pass)) {
        return createUserMessage("error", "Password must be at least 6 characters long, and contain at least one alphabetic character and one number.");
      }
  
      if (!$oldPass) {
        return createUserMessage("error", "Old password was not correct.");
      } else {
        $new_pass = $this->hashPassword($new_pass);
        $sql = "UPDATE tbl_users SET password=:password WHERE id = :id";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':password', $new_pass);
        $stmt->bindValue(':id', $userId);
        $result = $stmt->execute();
  
        if ($result) {
          Session::set('pendingMsg', createUserMessage("success", "Password changed."));
          return "<script>location.href='userProfile.php?userid=$userId';</script>";
        } else {
          return createUserMessage("error", "Password not changed. Something went wrong!");
        }
      }  
    } else {
      return createUserMessage("error", "Password not changed. A real user could not be found!");
    }
  }

  // Check old password is correct
  public function checkOldPassword($realUserId, $old_pass){
    $old_pass = $this->hashPassword($old_pass);
    $sql = "SELECT password FROM tbl_users WHERE password = :password AND id =:id";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':password', $old_pass);
    $stmt->bindValue(':id', $realUserId);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }
}
