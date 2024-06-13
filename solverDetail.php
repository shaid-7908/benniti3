<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
  include 'inc/header.php';
  include 'inc/topbar.php';
  Session::checkSession();
  $views->showAndClearPendingMessage();

  //Make sure they have an organization
  $orgList = $organizations->checkUserHasOrganizationOrRedirect($users);

  //Figured out what we're working on
  if (isset($_GET['solverid'])) {
    $solverid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['solverid']);
  }
  if (isset($_GET['orgid'])) {
    $orgid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['orgid']);
  }
  $getSolverInfo = null;
  if (isset($solverid) && $solverid != 0) {
    $getSolverInfo = $solvers->getSolverProfileById($solverid);
  }
  if (isset($orgid) && $orgid != "") {
    $solverUserId = Session::get("userid");
    if (isset($_GET['userid']) && is_numeric($_GET['userid']))
      $solverUserId = $_GET['userid'];
    $getSolverInfo = $solvers->getSolverProfileByOrgIdAndUserId($orgid, $solverUserId, $organizations, $users);
  }
  $allowed = false;
  if (checkUserAuth("edit_solver_orthogonal", Session::get('roleid'))) {
    //Admins are allowed to edit solvers
    $allowed = true;
  }
  if (!$allowed) {
    //Users are allowed to edit their own solver profiles
    if (isset($getSolverInfo) && $getSolverInfo->fk_user_id == $users->getRealId(Session::get('userid'))) {
        $allowed = true;
    } else {
      //Org admins are allowed to edit solver profiles in their company
      $orgLevel = $organizations->getUserOrganizationLevel($getSolverInfo->fk_org_id, Session::get('userid'), $users);
      if ($orgLevel == 1)
        $allowed = true;
    }
  }
  if (!$allowed) {
    //Warn about disallowed action
    error_log ("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to edit another user's profile, and was ejected.");
    Session::set('pendingMsg', createUserMessage("error", "You may not edit a Solver Profile other than your own!"));
    //header('Location:index.php');
    die();
  }
  //Process requested actions
  if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['updateSolverDetail']) && isset($solverid)) {
    $updateSolver = $solvers->updateSolverDetails($solverid, $_POST, $_FILES);
    if (isset($updateSolver)) {
      echo $updateSolver;
    }
  }
?>
<script src="scripts/skillsAjax.js"></script>
<div class="card " style="background-color: green;">
<div class="card-header">
    <h3><i class="fas fa-briefcase mr-2"></i>Solver Advertising <span class="float-right"> <a href="index.php" class="btn btn-reversed">Back</a> </span></h3>
  </div>
  <div class="card-body">
    <div style="width:600px; margin:0px auto">
    <h5 style="margin-bottom: 26px;">To help connect you with the most seekers possible, Bennit may share key information with partners. While most of these fields are optional, filling them out helps us "advertise" your profile to the largest audience possible!</h5>
      <form name="frmSolverDetail" id="frmSolverDetail" class="" action="solverDetail.php?solverid=<?php echo $solverid;?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="abstract"><b>Abstract</b> - Ideally 100-255 characters to quickly describe your capabilities</label>
          <input type="text" name="abstract" id="abstract" onKeyPress="if(this.value.length==255) return false;" value="<?php echo getIfSet($getSolverInfo, "abstract"); ?>" class="form-control" minlength="5" maxlength="254" required>
        </div>
        <div class="form-group">
          <img src="solverImage.php?id=<?php echo $solverid; ?>&type=portrait" style="width:112px;height:180px" width="112" height="180"><br/>
          <label for="portraitImage"><b>Portrait Image</b> - 350px wide by 565px tall in PNG format</label>
          <input type="file" id="portraitImage" name="portraitImage" class="form-control">
        </div>
        <div class="form-group">
          <img src="solverImage.php?id=<?php echo $solverid; ?>&type=banner" style="width:180px;height:112px" width="180" height="112"><br/>
          <label for="bannerImage"><b>Banner Image</b> - 320px wide by 180px tall in PNG format</label>
          <input type="file" name="bannerImage" id="bannerImage" class="form-control">
        </div>
        <div class="form-group">
          <?php
          $skillText = "";
          $skillIds = "";
          if(getIfSet($getSolverInfo, "id")) {
            $getSkillsInfo = $skills->getAllSkillsForSolverById(getIfSet($getSolverInfo, "id"));
            foreach($getSkillsInfo as $skill) {
              $skillText = $skillText . $skill->skill_name . ", ";
              $skillIds = $skillIds . $skill->fk_skill_id . ", ";
            }
            $skillText = substr($skillText, 0, strrpos($skillText, ','));
            $skillIds = substr($skillIds, 0, strrpos($skillIds, ','));
          }
          if (isset($_POST['skillsText'])) {
            $skillText = $_POST['skillsText'];
          }
          ?>
          <label for="skillsText"><b>Skills</b> - These will be used to help find matches, so include as many as you want, separated by commas.</label>
          <input type="text" id="skillsText" name="skillsText" value="<?php echo $skillText; ?>" class="form-control" onkeydown="return event.key != 'Enter';">
          <input type="hidden" id="skillsIds" name="skillsIds" value="<?php echo $skillIds; ?>">
        </div>
        <?php
          if ((isset($solverid) && $solverid != 0) || getIfSet($getSolverInfo, "id")) {
            echo '<input type="hidden" name="updateSolverDetail"/>';
          }
          echo "\r\n";
          ?>
        <input type="submit" disabled style="display:none"/>
    </form>
    <div class="form-group">
      <button id="btnControlSubmit" class="btn btn-default" onclick="controlledFormSubmit('frmSolverDetail')">Save</button>
    </div>
  </div>
</div>
</div>
<?php
  include 'inc/footer.php';
?>
</html>