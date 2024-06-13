<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();
$pendingMsg = Session::get("pendingMsg");
if (isset($pendingMsg)) {
  echo $pendingMsg;
}

$opportunityid = null;
if (isset($_GET['opportunityid'])) {
  $opportunityid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['opportunityid']);
  $upadate_status = $opportunities->updateMessageStatusForOpportunity($opportunityid);
}


?>
<div id='messageContainer'>

</div>
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
    <div class="xt-body-organization1 p-4 d-flex" style="justify-content: center; ">
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
                    <div style="display: flex; justify-content: space-between">

                   
                    <div class="form-group" style="display: flex; justify-content: flex-start;">
                        <?php
                        $orgid = getIfSet($getOInfo, "public_id");
                        if ($organizations->checkOrganizationAdmin($orgAdmin, Session::get("userid"), $users) || Session::get("roleid") == '1') {
                            echo "<span class='btn btn-primary'><a href='opportunity.php?opportunityid=" . getIfSet($getOInfo, "public_id") . "'>Edit</a></span>";
                        }
                        ?>
                        <div class="btn btn-success mx-2" onclick="handleApproval()">
                          Approve
                        </div>
                        <div class="btn btn-danger mx-2" onclick="handleRejection()">
                            Reject
                        </div>
                    </div>
                    <div class="btn btn-danger" style="height: 40px;" onclick="handleBack()">Close</div>
                     </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    // Function to handle approval
    function handleApproval() {
        var status = 'active'; // Define the status
        var opportunityid = <?php echo json_encode($opportunityid); ?>; // Get the opportunity ID

        // Create a new XMLHttpRequest object
        var xhttp = new XMLHttpRequest();

        // Define the function to handle the response
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Do something after successful update, if needed
                console.log('Opportunity approved successfully');
                alert('Opportunity approved successfully');
            }
        };

        // Set up the POST request
        xhttp.open("POST", "updateOpportunityStatus.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        // Send the request with the status and opportunity ID as parameters
        xhttp.send("status=" + encodeURIComponent(status) + "&opportunityid=" + encodeURIComponent(opportunityid));
    }

    // Function to handle rejection
    function handleRejection() {
        var status = 'delay'; // Define the status
        var opportunityid = <?php echo json_encode($opportunityid); ?>; // Get the opportunity ID

        // Create a new XMLHttpRequest object
        var xhttp = new XMLHttpRequest();

        // Define the function to handle the response
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Do something after successful update, if needed
                console.log('Opportunity rejected successfully');
               alert('Opportunity rejected');
            }
        };

        // Set up the POST request
        xhttp.open("POST", "updateOpportunityStatus.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        // Send the request with the status and opportunity ID as parameters
        xhttp.send("status=" + encodeURIComponent(status) + "&opportunityid=" + encodeURIComponent(opportunityid));
    }

    // Function to handle going back
    
</script>
<script>
  function handleBack(){
    window.history.back();
  }
</script>
</html>
