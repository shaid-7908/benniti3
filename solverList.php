<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();

  //Figured out what we're working on
  $theQuery = "";
  $skills = "";
  if (isset($_GET["query"])) {
    $theQuery = $_GET["query"];
  }
  if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST["query"])) {
    $theQuery = $_POST["query"];
  }

  //Process requested actions if allowed
  if (isset($_GET["action"]) && isset($_GET["solverid"]) && is_numeric($_GET["solverid"])) {
    $allowed = false;
    $removeId = (int)$_GET["solverid"];
    if (checkUserAuth($_GET["action"]."_orthogonal", Session::get('roleid'))) {
      //Admins are allowed to manage solvers
      $allowed = true;
    } else {
      //Check if this profile belongs to this user!
      $solverProfile = $solvers->getSolverProfileById($_GET["solverid"]);
      if (isset($solverProfile)) {
        if (getIfSet($solverProfile, "fk_user_id") ==  Session::get("userid")) {
          $allowed = true;
        }
      }
    }
    if (!$allowed && isset($solverProfile)) {
      //Org admins are allowed to manage solvers in their org
      $orgLevel = $organizations->getUserOrganizationLevel(getIfSet($solverProfile, "fk_org_id"), Session::get("userid"), $users); 
      if(checkUserAuth($_GET["action"], $orgLevel)) {
        $allowed = true;
      }
    }
    if (!$allowed) {
      //Warn about disallowed action
      error_log ("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to delete a profile they did not own, and was ejected.");
      Session::set('pendingMsg', createUserMessage("error", "You may not manage a Solver profile you do not own!"));
      header('Location:solverList.php?query');
      die();
    } else {
      $removeSolver = $solvers->deleteSolverProfile($removeId);
      if (isset($removeSolver)) {
        echo $removeSolver;
      }
    }
  }
?>
<div class="">

</div>
<div class="xt-card-organization1">
<div class="xt-sidebar-organization1">
<?php  include 'inc/sidebar.php'?>
<div style="border-top: 2px solid #053B45;padding: 8px;">
        <a href="https://www.bennit.ai/" target="_blank">
          <span style="text-decoration: underline; color:#F5A800;font-size: 14px;">
            Bennit.Ai
          </span>
        </a>
      </div>
</div>
<div class="xt-body-organization1">
  <div class="card " style="color: black;">
    <?php 
    //Show search form if needed
    if (strpos($_SERVER["QUERY_STRING"], "query") !== false) {
    ?>
      <div class="card-header">
        <h3><i class="fas fa-briefcase mr-2"></i>Solver Search </h3>
      </div>
      <div class="card-body">
        <div style="width:600px; margin:0px auto">
          <form class="" action="" method="GET">
            <div class="form-group">
              <label for="query"><b>Search String</b> - Key words to search for</label>
              <input type="text" name="query" id="query" value="<?php if (isset($theQuery)) { echo $theQuery; } ?>" class="form-control" minlength="3" required>
            </div>
            <div class="form-group">
              <label for="skills"><b>Skills</b> - Skills to search for</label>
              <input type="text" id="skills" name="skills" value="" class="form-control" disabled="true">
            </div>

            <div class="form-group">
                <button type="submit" name="dosearch" class="btn btn-default">Search</button>
            </div>
            </form>
        </div>
      </div>
    <?php 
    } else {
    ?>
      <div class="card-header">
        <h3><i class="fas fa-briefcase mr-2"></i>Solvers</h3>
      </div> 
    <?php 
    } 
      //Determine settings and request grid
      $gridColumns = ["location"];
      //If there's a query
      if ($theQuery != "") {
        $allSolvers = $solvers->searchSolvers($theQuery, $skills);
        print_r($allSolvers);
        if (Session::get("roleid") == '1') {
          $gridActions = ["view", "edit", "adminmatch", "delete"];
          array_push($gridColumns, "organization");
          array_push($gridColumns, "location");
        } else {
          $gridActions = ["view", "match"];
        }
      }
      //If we're loaded without a query
      $emptySearch = true;
      if ($theQuery == "" && !strpos($_SERVER["QUERY_STRING"], "query") !== false) {
        if (checkUserAuth("list_solvers", Session::get("roleid")) && isset($_GET["as"]) && $_GET["as"] == "admin") {
          $allSolvers = $solvers->getAllSolverData();
          array_push($gridColumns, "organization");
          array_push($gridColumns, "location");
          array_push($gridColumns, "adminflags");
          $gridActions = ["view", "edit", "adminmatch", "delete"];
          $emptySearch = false;
        }
      }
    ?>
    <div class="card-body pr-2 pl-2">
      <?php
      if (strpos($_SERVER["QUERY_STRING"], "query") === false)
        echo '<a class="nav-link" href="solver.php?action=create_solver"><i class="fas fa-briefcase mr-2"></i>Create Solver Profile</a>';
      if (isset($allSolvers))
        $views->makeSolverGrid($allSolvers, $gridColumns, $gridActions);
      ?>
    </div>
</div>
</div>
</div>
 


</html>