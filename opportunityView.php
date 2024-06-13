<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();

$opportunityid = null;
if (isset($_GET['opportunityid'])) {
  $opportunityid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['opportunityid']);
}
?>
<div class="xt-card-organization">
  <div class="xt-sidebar-organization">
    <?php include 'inc/sidebar.php' ?>
  </div>
  <div class="xt-body-organization p-4 d-flex" style="justify-content: center; ">
    <div style="color: black; width:70%;box-shadow: 0px 0px 18px 0px rgba(0, 0, 0, 0.1);">
      <div class="card-header">
        <h3><i class="fas fa-lightbulb mr-2"></i>Opportunity <span class="float-right"> <a href="opportunityList.php" class="btn btn-reversed">My Opportunities</a> </span> </h3>
      </div>
      <div class="card-body">
        <?php
        if (isset($opportunityid)) {
          $getOInfo = $opportunities->getOpportunityInfoById($opportunityid);
        }
        ?>
        <div style="width:600px; margin:0px auto">
          <div class="form-group">
            <?php
            //Determine if user is allowed to see the org info
            $getOrgList = $organizations->getOrganizationInfoByRealId(getIfSet($getOInfo, "fk_org_id"));
            $userOrgs = $organizations->getAllOrganizationDataForUser(Session::get("userid"), $users);
            $userOrgIds = [];
            foreach ($userOrgs as $userOrg) {
              array_push($userOrgIds, $userOrg->id);
            }
            $orgAdmin;
            if (isset($getOrgList)) {
              $thisOrgId = getIfSet($getOrgList, "id");
              if (Session::get("roleid") == '1' || in_array($thisOrgId, $userOrgIds))
                echo "<b>" . getIfSet($getOrgList, "orgname") . "</b>: ";
              $orgAdmin = getIfSet($getOrgList, "public_id");
            }
            echo getIfSet($getOInfo, "headline");
            ?>
          </div>
          <div class="form-group">
            <?php echo getIfSet($getOInfo, "requirements"); ?>
          </div>
          <div class="form-group">
            <b>Start Date: </b><?php echo getIfSet($getOInfo, "start_date"); ?>
          </div>
          <div class="form-group">
            <b>Completion By: </b><?php echo getIfSet($getOInfo, "complete_date"); ?>
          </div>
          <div class="form-group">
            <b>Location: </b><?php echo getIfSet($getOInfo, "location"); ?>
          </div>
          <div class="form-group" style="text-transform: capitalize;">
            <b>Skills Required: </b>
            <?php
            $getSkillsInfo = $skills->getAllSkillsForOpportunityById(getIfSet($getOInfo, "id"));
            $skillText = "";
            foreach ($getSkillsInfo as $skill) {
              $skillText = $skillText . $skill->skill_name . ", ";
            }
            $skillText = substr($skillText, 0, strrpos($skillText, ','));
            echo $skillText;
            ?>
          </div>
          <div class="form-group" style="display: flex; justify-content: space-between;">
            <?php
            $orgid = getIfSet($getOInfo, "public_id");
            if ($organizations->checkOrganizationAdmin($orgAdmin, Session::get("userid"), $users) || Session::get("roleid") == '1') {
              echo "<span class='btn btn-success'><a href='opportunity.php?opportunityid=" . getIfSet($getOInfo, "public_id") . "'>Edit</a></span>";
            }
            ?>
            <div class="btn btn-danger" onclick="handleBack()">Close</div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
<script>
  function handleBack(){
    window.history.back();
  }
</script>
<?php
include 'inc/footer.php';
?>

</html>