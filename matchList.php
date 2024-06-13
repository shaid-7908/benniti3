<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();
$views->showAndClearPendingMessage();
$roleId =  Session::get('roleid');

if ($roleId != '1' || ($roleId == '1' && $_GET["as"] != "admin")) {
  //Load Subscription status
  // if (!$subscriptions->checkSubscriptionExistsAnywhere(Session::get('userid'), $users, $organizations))
  //   header("Location:subscriptionRequired.php");
}

//Process requested actions if allowed
if (isset($_GET["action"])) {

  //Check permissions
  if ((strpos($_GET["action"], "admin") !== false || (isset($_GET["as"]) && $_GET["as"] == "admin")) && Session::get('roleid') != 1) {
    //Warn about disallowed action
    error_log("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to administrate a match, and was ejected.");
    Session::set('pendingMsg', createUserMessage("error", "You may not manage matches you do not own!"));
    header('Location:index.php');
  }

  //Check if a match was specified
  if (isset($_GET["matchid"]) && is_numeric($_GET["matchid"])) {
    //Find user's role if match if not admin
    if (strpos($_GET["action"], "user") !== false) {
      $userRole = $matches->getUserRoleInMatch(Session::get("userid"), $_GET["matchid"], $users, $organizations, $opportunities);
      print_r($userRole);
      //TODO: The matching UI should prevent this from ever happening, but for now we'll just catch it here
      if ($userRole == "solverseeker") {
        Session::set('pendingMsg', createUserMessage("error", "The seeker and solver cannot be the same in a match!"));
        header('Location:matchList.php');
      }
    }

    //Determine and perform action
    print_r($userRole);
    switch ($_GET["action"]) {
      case "user_approve": {
          echo $matches->actOnMatchById($_GET["matchid"], "approve", $userRole);
          break;
        }
      case "user_reject": {
          echo $matches->actOnMatchById($_GET["matchid"], "reject", $userRole);
          break;
        }
      case "admin_approve": {
          echo $matches->actOnMatchById($_GET["matchid"], "approve", "admin");
          break;
        }
      case "admin_reject": {
          echo $matches->actOnMatchById($_GET["matchid"], "reject", "admin");
          break;
        }
      case "admin_contact": {
          $matchData = $matches->getSpecificMatchDataById($_GET["matchid"], $organizations, $users);
          if (!$matchData)
            echo createUserMessage("error", "The match could not be loaded, cannot contact users.");
          $seeker = $users->getUserInfoById($matchData->seeker_match);
          if (!$seeker || $seeker->email == null || $seeker->email == "")
            echo createUserMessage("error", "The seeker contact info could not be loaded, cannot contact users.");
          $solver = $users->getUserInfoById($matchData->solver_match);
          if (!$solver || $solver->email == null || $solver->email == "")
            echo createUserMessage("error", "The solver contact info could not be loaded, cannot contact users.");
          $mailLink = "mailto:" . $seeker->email . "," . $solver->email . "?subject=Bennit Exchange Found a Match!";
          header('Location:' . $mailLink);
          //TODO: Send email from service, instead of external email client. Then we could update the seeker_solver_connect column in tbl_matches.
          break;
        }
      default: {
          echo createUserMessage("error", "The matching action or target could not be determined. Matching failed.");
          break;
        }
    }
  }
}
?>
<div class="xt-card-organization1">
  <div class="xt-sidebar-organization1">
   <?php include 'inc/sidebar.php' ?>
   <div style="border-top: 2px solid #053B45;padding: 8px;">
        <a href="https://www.bennit.ai/" target="_blank">
          <span style="text-decoration: underline; color:#F5A800;font-size: 14px;">
            Bennit.Ai
          </span>
        </a>
      </div>
  </div>
  <div class="xt-body-organization1">
    <div class="card " style="color:black">
      <div class="card-header">
        <h3><i class="fas fa-plug mr-2"></i>Review Matches</h3>
      </div>
      <?php
      //Determine settings and request grid
      $gridColumns = [];
      $allMatches = $matches->getAllMatchData($organizations, $users);
      // print_r($allMatches);
      if (Session::get("roleid") == '1') {
        $gridActions = ["adminconfirm", "adminreject", "admincontact"];
        array_push($gridColumns, "solverorg", "opportunityorg", "suggester", "actors", "created");
      } else {
        $gridActions = ["userconfirm", "userreject"];
      }
      ?>
      <div class="card-body pr-2 pl-2">
        <?php
        if (Session::get("roleid") == '1')
          echo '<i class="fas fa-plug mr-2"></i>Create Match: <a href="solverList.php?as=admin">From Solver</a> - <a href="opportunityList.php?as=admin">From Opportunity</a>' . PHP_EOL;
        if (isset($allMatches))

          $views->makeMatchGrid($allMatches, $gridColumns, $gridActions);
        ?>
      </div>
    </div>
  </div>
</div>


</html>