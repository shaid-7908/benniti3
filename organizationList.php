<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();

  $skills = "";
  //Figure out what we're working on
  $theQuery = "";
  if (isset($_GET["query"])) {
    $theQuery = $_GET["query"];
  }
  if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST["query"])) {
    $theQuery = $_POST["query"];
  }

  //Check permissions and process requested actions
  if (isset($_GET["action"]) && $_GET["orgid"] && is_numeric($_GET["orgid"])) {
    $allowed = false;
    $removeId = (int)$_GET["orgid"];
    if (checkUserAuth($_GET["action"]."_orthogonal", Session::get('roleid'))) {
      //Admins are allowed to delete
      $allowed = true;
    } else {
      //Organization creators are allowed to delete their own organization
      $userOrg = $organizations->getOrganizationInfoById($removeId);
      if (isset($userOrg) && checkUserAuth($_GET["action"], Session::get('roleid'))) {
        if (getIfSet($userOrg, "creator") ==  Session::get("userid")) {
          $allowed = true;
        }
      }
    }
    if (!$allowed) {
      //Warn about disallowed action
      error_log ("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to delete an organization they did not create, and was ejected.");
      Session::set('pendingMsg', createUserMessage("error", "You may not delete an organization you did not create!"));
      header('Location:organizationList.php');
    } else {
      //Actually do the delete
      $removeOrg = $organizations->deleteOrganizationById($removeId);
      if (isset($removeOrg)) {
        echo $removeOrg;
      }
    }
  }

  //Process requested query
  $theQuery = "";
  if (isset($_GET["query"])) {
    $theQuery = $_GET["query"];
  }
  if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST["query"])) {
    $theQuery = $_POST["query"];
  }
?>
<div class="xt-card-organization">
  <div class="xt-sidebar-organization">
    <?php include 'inc/sidebar.php' ?>
  </div>
  <div class="xt-body-organization">
 <div class="card " style="color: black;">
    <div class="card-header">
      <h3><i class="fas fa-building mr-2"></i>Manage Organizations</h3>
    </div>
    <div class="card-body pr-2 pl-2">
      <a class="nav-link" href="organization.php?action=create_organization"><i class="fas fa-building mr-2"></i>Create Organization</a>
      <?php
        //Determine settings and request grid
        $gridColumns = ["solver"];
        if ($theQuery != "") {  //If there's a specific query to load
            $allOrgs = $organizations->searchOrganizations($theQuery);
            if (Session::get("roleid") == '1') {
              $gridActions = ["viewsolver", "edit", "delete"];
            } else {
              $gridActions = ["viewsolver"];
            }
        } else {
            //If we're loaded for admin
            if (Session::get("roleid") == '1' && isset($_GET["as"]) && $_GET["as"] == "admin") {
              $allOrgs = $organizations->getAllOrganizationData();
              $gridActions = ["viewsolver", "view", "users", "edit", "delete"];
              array_push($gridColumns, "creator");
            } else {  //Default load
              $allOrgs = $organizations->getAllOrganizationDataForUser(Session::get("userid"), $users);
              $gridActions = ["viewsolver", "createsolver", "edit"];
            }
        }
        $views->makeOrganizationGrid($allOrgs, $gridColumns, $gridActions);
        //Redirect if needed
        if ((!isset($allOrgs) || count($allOrgs) < 1) && $theQuery == "")
            echo '<script>document.location="organizationRequired.php"</script>';
      ?>
    </div>
  </div>
  </div>

</div>
 

</html>