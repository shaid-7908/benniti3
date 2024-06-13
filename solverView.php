<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();

  //Figure out what we're working on
  $solverid = null;
  if (isset($_GET['id'])) { //TODO: deprecate ambiguous ids
    $solverid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['id']);
  }
  if (isset($_GET['solverid'])) {
    $solverid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['solverid']);
  }
  $orgid = null;
  if (isset($_GET['orgid'])) {
    $orgid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['orgid']);
  }
  //TODO: Prevent scraping
?>
<div class="card " style="background-color: gray;">
  <div class="card-header">

  </div>
  <?php
    if (isset($solverid) && $solverid != 0){
      $getSolverInfo = $solvers->getSolverProfileById($solverid);
    } elseif (isset($orgid) && $orgid != 0) {
      $solverUserId = Session::get("userid");
      if (isset($_GET['userid']) && is_numeric($_GET['userid']))
        $solverUserId = $_GET['userid'];  
      $getSolverInfo = $solvers->getSolverProfileByOrgIdAndUserId($orgid, $solverUserId, $organizations, $users);
    }
    if (isset($getSolverInfo)) {
  ?>
  <div class="card-body">    
    <div style="width:600px; margin:0px auto">
      <span class="float-right"><img src="solverImage.php?id=<?php echo $solverid; ?>&type=portrait" style="width:142px;height:230px;margin-left:18px;" width="142" height="230"></span>
      <h3><?php echo getIfSet($getSolverInfo, "headline"); ?></h3>
  
      <div class="form-group">
        <?php
          //Determine if user is allowed to see the org info
          $useSolverId = $organizations->getPublicId(getIfSet($getSolverInfo, "fk_org_id"));
          $getOrgList = $organizations->getOrganizationInfoById($useSolverId);
          $userOrgs = $organizations->getAllOrganizationDataForUser(Session::get("userid"), $users);
          $userOrgIds = [];
          foreach ($userOrgs as $userOrg) {
              array_push($userOrgIds, $userOrg->id);
          }
          if (isset($getOrgList)) {
            if (Session::get("roleid") == '1' || in_array($thisOrgId, $userOrgIds))
              echo "<h4><i>" . getIfSet($getOrgList, "orgname") . "</i></h4> ";
          }
        ?>
      </div>
      <div class="form-group">
        <?php echo str_replace("\n", "<br>", getIfSet($getSolverInfo, "experience")); ?>
      </div>
      <div class="form-group">
      <img src="solverImage.php?id=<?php echo $solverid; ?>&type=banner" style="width:320px;height:180px;" width="320" height="180">
      </div>
      <div class="form-group" style="text-transform: capitalize;">
        <b>Skills: </b> 
      <?php
        if(getIfSet($getSolverInfo, "id")) {
          $getSkillsInfo = $skills->getAllSkillsForSolverById(getIfSet($getSolverInfo, "id"));
          $skillText = "";
          foreach($getSkillsInfo as $skill) {
            $skillText = $skillText . $skill->skill_name . ", ";
          }
          $skillText = substr($skillText, 0, strrpos($skillText, ','));
        }
        echo $skillText;
        ?>
      </div>
      <div class="form-group">
        <b>Availability: </b><?php echo getIfSet($getSolverInfo, "availability"); ?>
      </div>
      <div class="form-group">
        <b>Rate: </b><?php echo getIfSet($getSolverInfo, "rate"); ?>
      </div>
      <div class="form-group">
        <b>Locations: </b><?php echo getIfSet($getSolverInfo, "locations"); ?>
      </div>
      <?php
        if(Session::get("roleid") == '1') {
          $coachChecked = "";
          if (getIfSet($getSolverInfo, "is_coach"))
            $coachChecked = "checked";
          $externalChecked = "";
          if (getIfSet($getSolverInfo, "allow_external"))
            $externalChecked = "checked";
      ?>
      <div class="form-group">
        <b>Admin Flags: </b><br/>
        <input type="checkbox" value="1" id="is_coach" name="is_coach" <?php echo $coachChecked; ?> disabled> Verified Coach<br/>
        <input type="checkbox" value="1" id="allow_external" name="allow_external" <?php echo $externalChecked; ?> disabled> Allow External Marketplaces (eg: CESMII)<br/>
      </div>
      <?php 
        }
      ?>
    <div class="form-group">
      <?php
      $orgid = $organizations->getPublicId(getIfSet($getSolverInfo, "fk_org_id"));
      if($organizations->checkOrganizationAdmin($orgid, Session::get("userid"), $users) || Session::get("roleid") == '1') {
        echo "<span class='btn btn-reversed'><a href='solver.php?solverid=" . getIfSet($getSolverInfo, "public_id") . "&orgid=" . $orgid . "'>Edit</a></span>";
      } else {
        echo "<span class='btn btn-default'><a href='matchSuggest.php?solverid=" . getIfSet($getSolverInfo, "public_id") . "&orgid=" . $orgid . "'>Match</a></span>";
      }
      ?>
    </div>
  </div>
  <?php
    } else {
      echo "<div class='card-body'>Solver not found</div>";
    }
  ?>
</div>
</div>
<?php
  include 'inc/footer.php';
?>
</html>