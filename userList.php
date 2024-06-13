<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();

  //Check if user is allowed to view this page
  $orgLevel = null;
  if (!checkUserAuth('list_users_orthogonal', Session::get('roleid'))) {
    //Check if this user has a role in this organization
    if (isset($_GET["orgid"])) {
      $orgLevel = $organizations->getUserOrganizationLevel($_GET["orgid"], Session::get("userid"), $users); 
    }
    //Warn about disallowed action
    if (!isset($_GET["orgid"]) || !checkUserAuth('list_users', $orgLevel)) {
      error_log("A user with id " . Session::get('userid') . " and role " . Session::get('userid') . " attempted to access the user list without sufficient permissions.");
      Session::set('pendingMsg', createUserMessage("error", "You do not have permission to manage users!"));
      header("Location:index.php");
      die();
    }
  }
  //Process requested actions if allowed
  //  Global Admin-only Actions
  if (isset($_GET["action"]) && !isset($_GET["orgid"]) && isset($_GET["userid"]) && is_numeric($_GET["userid"])) {
    $allowed = false;
    if (!checkUserAuth($_GET["action"], Session::get('roleid'))) {
      //Warn about disallowed action
      error_log ("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to manage users, and was ejected.");
      Session::set('pendingMsg', createUserMessage("error", "You do not have permission to manage users!"));
      header('Location:index.php');
      die();
    } else {
      $allowed = true;
    }
    if ($allowed && $_GET["action"] == "delete_user") {
      $removeUser = $users->deleteUserById($_GET["userid"]);
      if (isset($removeUser)) {
        echo $removeUser;
      }
    }
    if ($allowed && $_GET["action"] == "disable_user") {
      $disableUser = $users->disableUser($_GET["userid"]);
      if (isset($disableUser)) {
        echo $disableUser;
      }
    }
    if ($allowed && $_GET["action"] == "enable_user") {
      $enableUser = $users->enableUser($_GET["userid"]);
      if (isset($enableUser)) {
        echo $enableUser;
      }
    }
  }
  //  Org Admin-level Actions
  if (isset($_GET["action"]) && isset($_GET["orgid"]) && isset($_GET["userid"]) && is_numeric($_GET["userid"])) {
    //Admins are allowed to do org-level actions
    $allowed = checkUserAuth($_GET["action"]."_orthogonal", Session::get('roleid'));
    if (!$allowed && isset($orgLevel)) {
      //Check if this user's role is allowed to perform this action
      $allowed = checkUserAuth($_GET["action"], $orgLevel);
    }
    if (!$allowed) {
      //Warn about disallowed action
      error_log ("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to modify a user without permission, and was ejected.");
      Session::set('pendingMsg', createUserMessage("error", "You may not change a user when you are not the administrator!"));
      header('Location:index.php');
      die();
    } else {
      if ($_GET["action"] == "remove_user_from_org" && isset($_GET["userid"]) ) {
        $removeUser = $organizations->removeUserFromOrganization($_GET["orgid"], $_GET["userid"], $users);
        if (isset($removeUser))
          echo $removeUser;
      }
      if ($_GET["action"] == "add_user_to_org" && isset($_GET["userid"]) && isset($_GET["org_level"])) {
        $addUser = $organizations->addUserToOrganization($_GET["orgid"], $_GET["userid"], $_GET["org_level"], $users);
        if (isset($addUser))
          echo $addUser;
      }
    }
  }
?>
<div class="xt-card-organization">
  <div class="xt-sidebar-organization">

  </div>
  <div class="xt-body-organization" style="color: black;">
     <div class="card">
    <div class="card-header">
      <h3><i class="fab fa-500px mr-2"></i>User Admin</h3>
    </div>
    <div class="card-body pr-2 pl-2">
      <?php
        //Determine settings and request grid
        $gridColumns = ["username", "status"];
        if (!isset($_GET["orgid"]) || $_GET["orgid"] == "") {   //Normal User Admin Grid
          $allUsers = $users->getAllUserData();
          $gridActions = [];
          if (checkUserAuth("disable_user", Session::get("roleid")))
            array_push($gridActions,"disable");
          if (checkUserAuth("edit_user_orthogonal", Session::get("roleid")))
            array_push($gridActions,"edit");
          if (checkUserAuth("delete_user", Session::get("roleid")))
            array_push($gridActions,"delete");
          array_push($gridColumns, "created");    
        } else {   //Org User Grid and Actions
          if (isset($_GET["action"]) && $_GET["action"] == "add_user_to_org") {
            $allUsers = $users->getAllUserData();
            $gridActions = ["orgadd"];
          } else {
            if (checkUserAuth("add_user_to_org_orthogonal", Session::get("roleid")))
              echo '<a class="nav-link" href="userList.php?action=add_user_to_org&orgid=' . $_GET["orgid"] . '"><i class="fas fa-building mr-2"></i>Add User to Organization</a>';
            $allUsers = $organizations->getAllUserDataForOrganization($_GET["orgid"]);
            $gridActions = ["orgremove"];
            array_push($gridColumns, "orglevel");  
          }
        }
        $views->makeuserGrid($allUsers, $gridColumns, $gridActions, $orgLevel);
      ?>
    </div>
  </div>
  </div>

</div>
  
<?php
  include 'inc/footer.php';
?>
</html>