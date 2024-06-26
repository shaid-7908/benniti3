<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/xtheader.php';
include 'inc/xttopbar.php';
Session::checkSession();
$views->showAndClearPendingMessage();

//Make sure they have an organization
$orgList = $organizations->checkUserHasOrganizationOrRedirect($users);

//Figure out what we're working on
if (isset($_GET['id'])) { //TODO: deprecate ambiguous ids
  $optyid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['id']);
}
if (isset($_GET['opportunityid']) && is_numeric($_GET['opportunityid'])) {
  $optyid = (int)$_GET['opportunityid'];
}
if (isset($_GET['orgid']) && is_numeric($_GET['orgid'])) {
  $orgid = (int)$_GET['orgid'];
}

$allowed = false;
if (isset($optyid)) {
  if (checkUserAuth("edit_opportunity_orthogonal", Session::get('roleid'))) {
    //Admins are allowed to edit opportunities
    $allowed = true;
  } else {
    //Check if this opportunity belongs to this user
    $userOpty = $opportunities->getOpportunityInfoById($optyid);
    if (isset($userOpty) && checkUserAuth("edit_opportunity", Session::get('roleid'))) {
      if (getIfSet($userOpty, "fk_user_id") ==  $users->getRealId(Session::get("userid"))) {
        $allowed = true;
      }
    }
  }
}
if (!$allowed && isset($orgid)) {
  //Org admins are allowed to edit opportunities in their company
  $orgLevel = $organizations->getUserOrganizationLevel($orgid, Session::get('userid'), $users);
  if ($orgLevel == 1)
    $allowed = true;
}
//Creation doesn't require edit permissions
if (isset($_GET['action']) && $_GET['action'] == "create_opportunity" && checkUserAuth("create_opportunity", Session::get('roleid'))) {
  if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['addOpty'])) {
    $getOInfo = $_POST;

    $addOpty = $opportunities->createOpportunity($_POST, TRUE, $organizations, $users);

    //Show pending results
    if (isset($addOpty)) {
      echo $addOpty;
    }
  } else {
    //Empty form
  }
}
//Everything else does
else {
  if (!$allowed) {
    //Warn about disallowed action
    error_log("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to edit another user's organization, and was ejected.");
    Session::set('pendingMsg', createUserMessage("error", "You may not edit an opportunity you do not own!"));
    //TODO: REMOVE COMMENT header('Location:index.php');
    die();
  } else {
    $getOInfo = null;
    //Process requested actions if allowed
    if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['updateOpty']) && isset($optyid)) {
      $getOInfo = $_POST;
      $updateOpty = $opportunities->updateOpportunityById($optyid, $_POST);
      //Show pending results
      if (isset($updateOpty)) {
        echo $updateOpty;
      }
    }
  }
}
?>
<style>
  .ck-content {
    height: 300px;
  }
</style>
<script src="scripts/skillsAjax.js"></script>
<div id="show-sidebar" style="background-color: white;" onclick="handleSidebar()">
  <div class="sidebar-toggle-button">
    <i class="fas fa-bars"></i>

  </div>
</div>
<div class="xt-card-organization1">

  <div class="xt-sidebar-organization1 " id="jjk">

    <div class="step-indicators p-4" style="margin-top: 20px">
      <h1 style="width: 272px; height: 64px; font-size: 24px; font-weight: 700;
                 line-height: 32px; letter-spacing: 0em; text-align: left;">Create an <br> opportunity</h1>

      <div class="step-indicators">

        <div class="step-indicator active" style="margin-top: 30px">
          <div class="step-number active" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #FFFFFF66; font-size: 16px; font-weight: bold; border-radius: 4px; text-align: center; line-height: 28px; margin-right: 10px;">1</div>
          <span style="width: 193px; height: 28px; font-size: 16px; font-weight: 400; line-height: 28px; letter-spacing: 0.15000000596046448px; text-align: left;">Opportunity description</span>
        </div>

        <div class="step-indicator" style="margin-top: 19px">
          <div class="step-number" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #FFFFFF66; font-size: 16px; font-weight: bold; text-align: center; border-radius: 4px; line-height: 28px; margin-right: 10px;">2</div>
          <span style="width: 193px; height: 28px; font-size: 16px; font-weight: 400; line-height: 28px; letter-spacing: 0.15000000596046448px; text-align: left;">Opportunity details</span>
        </div>

        <div class="step-indicator" style="margin-top: 19px">
          <div class="step-number" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #FFFFFF66; font-size: 16px; font-weight: bold; text-align: center; border-radius: 4px; line-height: 28px; margin-right: 10px;">3</div>
          <span style="width: 193px; height: 28px; font-size: 16px; font-weight: 400; line-height: 28px; letter-spacing: 0.15000000596046448px; text-align: left;">Desired skills</span>
        </div>

      </div>
    </div>
    <div class="bottom" style="margin-top: auto;color:#F5A800 ;width: 100%;">


      <div class="my-2 px-4" onclick="handleback()">Cancel</div>
      <script>
        function handleback() {
          window.history.back();
        }
      </script>

      <hr style="border-color: #024552;">
      <a href="https://www.bennit.ai/" target="_blank" style="text-decoration: none;color: inherit;">

        <div class="my-2 px-4" style="color: #F5A800; text-decoration: underline;">Bennit Ai</div>
      </a>
    </div>
  </div>
  
  <div class="xt-body-organization1" style="color: black;">
    <div class="progress-bar" style="background-color: #F5A800; height: 8px;"></div>
    <?php
    if (isset($optyid) && $optyid != 0) {
      $getOInfo = $opportunities->getOpportunityInfoById($optyid);
    }
    ?>
    <div class="opportunity-form-container" >


      <form name="frmOpportunity" id="frmOpportunity" class="" action="" method="POST">
        <fieldset>
          <h3 class="mt-4" style="font-size: 48px; font-weight: 700;">Opportunity description</h3>
          <p style="font-weight: 400; font-size: 16px;">Define the problem you need solved, or the opportunity to improve.</p>
          <div class="form-group">
            <label for="fk_org_id" style="font-weight: 700; font-size: 16px;">Organization</label>
            <select class="form-control" style=" appearance: none;" name="fk_org_id" id="fk_org_id" disabled>
              <?php
              if (isset($orgList)) {

                echo "<option selected  style= \"margin-top:20px; background-color: #fff; color: #000; padding: 10px; \" value=\"" . $orgList[0]->id . "\">" . $orgList[0]->orgname . "</option>";
              }
              ?>
            </select>
            <input type="hidden" name="fk_org_id" id="fk_org_id" value="<?php echo $orgList[0]->id ?>">

          </div>
          <div class="form-group">
            <label for="headline" style="font-weight: 700; font-size: 16px;">Opportunity Headline</label>
            <p style="font-size: 12px; font-weight: 400;">A brief description of your needs</p>
            <input style="background-color: white; border: 1px solid #ced4da;" type="text" name="headline" id="headline" value="<?php echo getIfSet($getOInfo, "headline"); ?>" class="form-control" minlength="8" maxlength="254" required>
          </div>

          <div class="form-group">
            <label style="font-weight: 700; font-size: 16px;" for="requirements">Requirements </label>
            <p style="font-size: 12px; font-weight: 400;">A detailed description of the opportunity</p>
            <textarea name="requirements" id="requirements" style="width:100%; height:300px" class="form-control"><?php echo getIfSet($getOInfo, "requirements"); ?></textarea>
          </div>
          <input type="button" class="next-form btn btn-info" style="background-color: #F5A800; border: none; width:130px;color:black;font-weight: 700;" value="Next" />
        </fieldset>

        <fieldset>
          <h3 class="mt-4" style="font-size: 48px; font-weight: 700;">Opportunity details</h3>
          <p style="font-weight: 400; font-size: 16px;">Provide details such as anticipated start and end dates, location, and pay range.</p>
          <div class="form-group">
            <label style="font-size: 24px;font-weight: 700;">Dates</label>
            <p style="font-size:16px;font-weight: 400;">Do you know when you want to start and end this opportunity? An estimate is okay! This will help us match you with Solvers who are available when you need them.</p>
            <label>
              <input type="radio" name="date" value="yes" checked> Yes
            </label><br>
            <label>
              <input type="radio" name="date" value="no"> No
            </label><br>
            <div class="range-date">


              <label for="start_date" style="font-size: 16px; font-weight: 700;">Anticipated Start Date</label>
              <div style="border: 1px solid #ced4da;border-radius: 4px; display:flex;align-items: center;" class="px-2 form-control">

                <input type="text" placeholder="Pick your dates" name="start_date" id="start_date" value="<?php echo getIfSet($getOInfo, "start_date"); ?>" minlength="3" maxlength="254" required style="background-color: white; border: none;outline: none;width:95%">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_495_1329)">
                    <rect width="14" height="14" fill="white" fill-opacity="0.01" />
                    <g clip-path="url(#clip1_495_1329)">
                      <path d="M9.49725 6.25202C9.53799 6.29266 9.57032 6.34094 9.59237 6.39409C9.61443 6.44724 9.62578 6.50422 9.62578 6.56177C9.62578 6.61931 9.61443 6.6763 9.59237 6.72945C9.57032 6.7826 9.53799 6.83088 9.49725 6.87152L6.87225 9.49652C6.83161 9.53726 6.78333 9.56958 6.73018 9.59164C6.67703 9.6137 6.62005 9.62505 6.5625 9.62505C6.50495 9.62505 6.44797 9.6137 6.39482 9.59164C6.34167 9.56958 6.29339 9.53726 6.25275 9.49652L4.94025 8.18402C4.89957 8.14334 4.86731 8.09505 4.84529 8.0419C4.82328 7.98876 4.81195 7.93179 4.81195 7.87427C4.81195 7.81674 4.82328 7.75978 4.84529 7.70663C4.86731 7.65348 4.89957 7.60519 4.94025 7.56452C5.0224 7.48237 5.13382 7.43622 5.25 7.43622C5.30753 7.43622 5.36449 7.44755 5.41764 7.46956C5.47078 7.49157 5.51907 7.52384 5.55975 7.56452L6.5625 8.56814L8.87775 6.25202C8.91839 6.21127 8.96667 6.17895 9.01982 6.15689C9.07297 6.13484 9.12995 6.12349 9.1875 6.12349C9.24505 6.12349 9.30203 6.13484 9.35518 6.15689C9.40833 6.17895 9.45661 6.21127 9.49725 6.25202Z" fill="#6C757D" />
                      <path d="M3.0625 -0.000732422C3.17853 -0.000732422 3.28981 0.0453612 3.37186 0.127408C3.45391 0.209456 3.5 0.320735 3.5 0.436768V0.874268H10.5V0.436768C10.5 0.320735 10.5461 0.209456 10.6281 0.127408C10.7102 0.0453612 10.8215 -0.000732422 10.9375 -0.000732422C11.0535 -0.000732422 11.1648 0.0453612 11.2469 0.127408C11.3289 0.209456 11.375 0.320735 11.375 0.436768V0.874268H12.25C12.7141 0.874268 13.1592 1.05864 13.4874 1.38683C13.8156 1.71502 14 2.16014 14 2.62427V12.2493C14 12.7134 13.8156 13.1585 13.4874 13.4867C13.1592 13.8149 12.7141 13.9993 12.25 13.9993H1.75C1.28587 13.9993 0.840752 13.8149 0.512563 13.4867C0.184374 13.1585 0 12.7134 0 12.2493V2.62427C0 2.16014 0.184374 1.71502 0.512563 1.38683C0.840752 1.05864 1.28587 0.874268 1.75 0.874268H2.625V0.436768C2.625 0.320735 2.67109 0.209456 2.75314 0.127408C2.83519 0.0453612 2.94647 -0.000732422 3.0625 -0.000732422ZM0.875 3.49927V12.2493C0.875 12.4813 0.967187 12.7039 1.13128 12.868C1.29538 13.0321 1.51794 13.1243 1.75 13.1243H12.25C12.4821 13.1243 12.7046 13.0321 12.8687 12.868C13.0328 12.7039 13.125 12.4813 13.125 12.2493V3.49927H0.875Z" fill="#6C757D" />
                    </g>
                  </g>
                  <defs>
                    <clipPath id="clip0_495_1329">
                      <rect width="14" height="14" fill="white" />
                    </clipPath>
                    <clipPath id="clip1_495_1329">
                      <rect width="14" height="14" fill="white" />
                    </clipPath>
                  </defs>
                </svg>

              </div>
              <label for="start_date" class="inter-font mt-2" style="font-size: 16px; font-weight: 700;">Anticipated End Date</label>
              <div style="border: 1px solid #ced4da;border-radius: 4px; display:flex;align-items: center;" class="px-2 form-control">

                <input type="text" placeholder="Pick your dates" name="complete_date" id="complete_date" value="<?php echo getIfSet($getOInfo, "complete_date"); ?>" minlength="3" maxlength="254" required style="background-color: white; border: none;outline: none;width:95%">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_495_1329)">
                    <rect width="14" height="14" fill="white" fill-opacity="0.01" />
                    <g clip-path="url(#clip1_495_1329)">
                      <path d="M9.49725 6.25202C9.53799 6.29266 9.57032 6.34094 9.59237 6.39409C9.61443 6.44724 9.62578 6.50422 9.62578 6.56177C9.62578 6.61931 9.61443 6.6763 9.59237 6.72945C9.57032 6.7826 9.53799 6.83088 9.49725 6.87152L6.87225 9.49652C6.83161 9.53726 6.78333 9.56958 6.73018 9.59164C6.67703 9.6137 6.62005 9.62505 6.5625 9.62505C6.50495 9.62505 6.44797 9.6137 6.39482 9.59164C6.34167 9.56958 6.29339 9.53726 6.25275 9.49652L4.94025 8.18402C4.89957 8.14334 4.86731 8.09505 4.84529 8.0419C4.82328 7.98876 4.81195 7.93179 4.81195 7.87427C4.81195 7.81674 4.82328 7.75978 4.84529 7.70663C4.86731 7.65348 4.89957 7.60519 4.94025 7.56452C5.0224 7.48237 5.13382 7.43622 5.25 7.43622C5.30753 7.43622 5.36449 7.44755 5.41764 7.46956C5.47078 7.49157 5.51907 7.52384 5.55975 7.56452L6.5625 8.56814L8.87775 6.25202C8.91839 6.21127 8.96667 6.17895 9.01982 6.15689C9.07297 6.13484 9.12995 6.12349 9.1875 6.12349C9.24505 6.12349 9.30203 6.13484 9.35518 6.15689C9.40833 6.17895 9.45661 6.21127 9.49725 6.25202Z" fill="#6C757D" />
                      <path d="M3.0625 -0.000732422C3.17853 -0.000732422 3.28981 0.0453612 3.37186 0.127408C3.45391 0.209456 3.5 0.320735 3.5 0.436768V0.874268H10.5V0.436768C10.5 0.320735 10.5461 0.209456 10.6281 0.127408C10.7102 0.0453612 10.8215 -0.000732422 10.9375 -0.000732422C11.0535 -0.000732422 11.1648 0.0453612 11.2469 0.127408C11.3289 0.209456 11.375 0.320735 11.375 0.436768V0.874268H12.25C12.7141 0.874268 13.1592 1.05864 13.4874 1.38683C13.8156 1.71502 14 2.16014 14 2.62427V12.2493C14 12.7134 13.8156 13.1585 13.4874 13.4867C13.1592 13.8149 12.7141 13.9993 12.25 13.9993H1.75C1.28587 13.9993 0.840752 13.8149 0.512563 13.4867C0.184374 13.1585 0 12.7134 0 12.2493V2.62427C0 2.16014 0.184374 1.71502 0.512563 1.38683C0.840752 1.05864 1.28587 0.874268 1.75 0.874268H2.625V0.436768C2.625 0.320735 2.67109 0.209456 2.75314 0.127408C2.83519 0.0453612 2.94647 -0.000732422 3.0625 -0.000732422ZM0.875 3.49927V12.2493C0.875 12.4813 0.967187 12.7039 1.13128 12.868C1.29538 13.0321 1.51794 13.1243 1.75 13.1243H12.25C12.4821 13.1243 12.7046 13.0321 12.8687 12.868C13.0328 12.7039 13.125 12.4813 13.125 12.2493V3.49927H0.875Z" fill="#6C757D" />
                    </g>
                  </g>
                  <defs>
                    <clipPath id="clip0_495_1329">
                      <rect width="14" height="14" fill="white" />
                    </clipPath>
                    <clipPath id="clip1_495_1329">
                      <rect width="14" height="14" fill="white" />
                    </clipPath>
                  </defs>
                </svg>

              </div>
            </div>

          </div>


          <div class="form-group">
            <label for="location" style="font-size: 24px; font-weight: 700;">Location</label>
            <p style="font-size: 16px; font-weight: 400;">Does this opportunity require Solvers to be on-prem, remote, or a hybrid of the two?</p>
            <label>
              <input type="radio" name="location" value="On premise" checked> On Premise
            </label><br>
            <label>
              <input type="radio" name="location" value="hybrid"> Hybrid
            </label><br>
            <label>
              <input type="radio" name="location" value="remote"> Remote
            </label><br>
            <div class="address-field my-2">
              <h3 style="font-size: 18px; font-weight: 700;">Opportuinity Address</h3>
              <p style="font-size: 16px; font-weight: 400;">Enter the address of the location you expect Solvers to travel to.</p>
              <label for="address line 1" style="font-size:16px;font-weight: 700;" class="mt-2">Address line 1</label>
              <input type="text" name="address1" id="address1" value="<?php echo getIfSet($getOInfo, "location"); ?>" class="form-control" minlength="2" maxlength="254" style="background-color: white; border: 1px solid #ced4da;">
              <label for="address line 2" style="font-size:16px;font-weight: 700;" class="mt-2">Address line 2</label>
              <p style="font-size: 12px;">Optional</p>
              <input type="text" name="address2" id="address2" value="<?php echo getIfSet($getOInfo, "location"); ?>" class="form-control" minlength="2" maxlength="254" style="background-color: white; border: 1px solid #ced4da;">
              <div style="background-color: white; color:black;display:flex ;" class="mt-4">
                <div style="flex:50% !important;">
                  <label for="city" style="font-size:16px;font-weight: 700;">City</label>
                  <input type="text" name="city" class="form-control" style="background-color: white; border: 1px solid #ced4da;">
                </div>
                <div style="flex:20%;" class="mx-2">
                  <label for="state" style="font-size:16px;font-weight: 700;">State</label>
                  <select id="state" name="state" class="form-control inter-font" style="background-color: white; border: 1px solid #ced4da;" required>
                    <option value="" selected disabled>Select a state</option>
                    <option value="Alabama">Alabama</option>
                    <option value="Alaska">Alaska</option>
                    <option value="Arizona">Arizona</option>
                    <option value="Arkansas">Arkansas</option>
                    <option value="California">California</option>
                    <option value="Colorado">Colorado</option>
                    <option value="Connecticut">Connecticut</option>
                    <option value="Delaware">Delaware</option>
                    <option value="District Of Columbia">District Of Columbia</option>
                    <option value="Florida">Florida</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Hawaii">Hawaii</option>
                    <option value="Idaho">Idaho</option>
                    <option value="Illinois">Illinois</option>
                    <option value="Indiana">Indiana</option>
                    <option value="Iowa">Iowa</option>
                    <option value="Kansas">Kansas</option>
                    <option value="Kentucky">Kentucky</option>
                    <option value="Louisiana">Louisiana</option>
                    <option value="Maine">Maine</option>
                    <option value="Maryland">Maryland</option>
                    <option value="Massachusetts">Massachusetts</option>
                    <option value="Michigan">Michigan</option>
                    <option value="Minnesota">Minnesota</option>
                    <option value="Mississippi">Mississippi</option>
                    <option value="Missouri">Missouri</option>
                    <option value="Montana">Montana</option>
                    <option value="Nebraska">Nebraska</option>
                    <option value="Nevada">Nevada</option>
                    <option value="New Hampshire">New Hampshire</option>
                    <option value="New Jersey">New Jersey</option>
                    <option value="New Mexico">New Mexico</option>
                    <option value="New York">New York</option>
                    <option value="North Carolina">North Carolina</option>
                    <option value="North Dakota">North Dakota</option>
                    <option value="Ohio">Ohio</option>
                    <option value="Oklahoma">Oklahoma</option>
                    <option value="Oregon">Oregon</option>
                    <option value="Pennsylvania">Pennsylvania</option>
                    <option value="Rhode Island">Rhode Island</option>
                    <option value="South Carolina">South Carolina</option>
                    <option value="South Dakota">South Dakota</option>
                    <option value="Tennessee">Tennessee</option>
                    <option value="Texas">Texas</option>
                    <option value="Utah">Utah</option>
                    <option value="Vermont">Vermont</option>
                    <option value="Virginia">Virginia</option>
                    <option value="Washington">Washington</option>
                    <option value="West Virginia">West Virginia</option>
                    <option value="Wisconsin">Wisconsin</option>
                    <option value="Wyoming">Wyoming</option>
                  </select>
                </div>
                <div style="flex:20%;display: flex;flex-direction: column;" class="">
                  <label for="zip" style="font-size:16px;font-weight: 700;">Zip Code</label>
                  <input type="text" name="zip" class="form-control" style="background-color: white; border: 1px solid #ced4da;">
                </div>
              </div>
            </div>
          </div>
          <div class="form-group inter-font">
            <label for="rate" style="font-size: 24px; font-weight: 700;">Pay rate</label>
            <p style="font-size: 16px;">Do you know the rate (or range) you’re expecting to pay?</p>
            <label>
              <input type="radio" name="rate" value="yes" checked> Yes
            </label><br>
            <label>
              <input type="radio" name="rate" value="no"> No
            </label><br>
            <div class="pay-rate">
              <label for="rate" style="font-size: 16px; font-weight: 700;">Rate</label>
              <p style="font-size: 12px; font-weight: 400;color:gray">Enter the rate or range you’re expecting to pay. Example: $300/hr or $1150/day.</p>
              <div style="display: inline-block;">
                <input type="text" name="rate_value" id="rate_value" value="<?php echo getIfSet($getSolverInfo, "rate"); ?>" style="background-color: white; border: 1px solid #ced4da; width: 200px;" class="form-control" minlength="3" maxlength="254" required>
              </div>
              <div style="display: inline-block; margin-left: 5px;">
                <select class="form-control" name="rate_type" id="rate_type" style="width: 120px;">
                  <option value="per_hour">Per Hour</option>
                  <option value="per_day">Per Day</option>
                </select>
              </div>
            </div>


          </div>
          <input type="button" name="previous" class="previous-form btn btn-info" value="Previous" style="background-color: #E7E7E8; border: none; width:130px;color:black;font-weight: 700;" />
          <input type="button" name="next" class="next-form btn btn-info" value="Next" style="background-color: #F5A800; border: none; width:130px;color:black;font-weight: 700; " />
        </fieldset>
        <fieldset>
          <div class="form-group">
            <h3 class="mt-4" style="font-size: 48px; font-weight: 700;">Desired skills</h3>
            <p style="font-size: 16px ; font-weight: 400;">List the skills you’d like your ideal Solver to have. This will help us refine your match results.</p>
            <?php
            $skillText = "";
            $skillIds = "";
            if (getIfSet($getOInfo, "id") != "") {
              $getSkillsInfo = $skills->getAllSkillsForOpportunityById(getIfSet($getOInfo, "id"));
              foreach ($getSkillsInfo as $skill) {
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
            <label for="skillsText" style="font-size: 16px; font-weight:700">Add a Skill</label>
            <p style="font-size: 12px; font-weight: 500;">Press Enter after you’re done typing to add a skill. Add as many as you like!</p>
            <input type="text" id="skillsText" name="skillsText" class="form-control" onkeydown="addSkill(event)" style="background-color: white;border: 1px solid #ced4da;">
            <input type="hidden" id="skillsIds" name="skillsIds" value="<?php echo $skillIds; ?>">
            <div id="skills-list" class="my-2" style="display: flex; flex-wrap: wrap;">
              <!-- Skills will be dynamically added here -->
            </div>
          </div>

          <?php
          if ((isset($optyid) && $optyid != 0) || getIfSet($getOInfo, "id")) {
            $buttonText = "Update";
            echo '<input type="hidden" name="updateOpty"/>';
          } else {
            $buttonText = "Sumbmit opportunity";
            echo '<input type="hidden" name="addOpty"/>';
          }
          echo "\r\n";
          ?>
          <input type="submit" disabled style="display:none" />
          <input type="hidden" id="fk_opportunity_id" name="fk_opportunity_id" value="<?php echo getIfSet($getOInfo, "id"); ?>">
          <input type="button" name="previous" class="previous-form btn btn-info" value="Previous" style="background-color: #E7E7E8; border: none; width:130px;color:black;font-weight: 700;" />
          <button id="btnControlSubmit" class="inter-font font-600" style="border: none;border-radius: 4px;padding: 4px 8px 4px 8px; background-color: #F5A800; color:black" onclick="controlledFormSubmit('frmOpportunity')"><?php echo $buttonText; ?></button>

        </fieldset>
      </form>
      <div class="form-group">
      </div>
    </div>



  </div>
</div>

<script>
  const form = document.getElementById('frmOpportunity')
  const addressField = document.querySelector('.address-field');
  const payRate = document.querySelector('.pay-rate')
  const rangeDate = document.querySelector('.range-date')

  form.addEventListener('change', function(event) {
    if (event.target && event.target.name === 'location') {
      if (event.target.value === 'remote') {
        addressField.style.display = 'none';
      } else {
        addressField.style.display = 'block';
      }
    }
  });

  form.addEventListener('change', function(event) {
    if (event.target && event.target.name === 'rate') {
      if (event.target.value === 'no') {
        payRate.style.display = 'none';
      } else {
        payRate.style.display = 'block'
      }
    }
  });

  form.addEventListener('change', function(event) {
    if (event.target && event.target.name === 'date') {
      if (event.target.value === 'no') {
        rangeDate.style.display = 'none';
      } else {
        rangeDate.style.display = 'block'
      }
    }
  });


  // functions to add skill as div
  function addSkill(event) {
    if (event.key === 'Enter') {
      event.preventDefault();

      // Get input value
      const skill = document.getElementById('skillsText').value.trim();
      if (skill !== '') {
        // Add skill to the list
        addSkillToList(skill);
        // Clear input field
        document.getElementById('skillsText').value = '';
      }
    }
  }

  function addSkillToList(skill) {
    // Get the hidden input field
    const skillsIdsInput = document.getElementById('skillsIds');
    // Get the existing skill IDs
    let skillIds = skillsIdsInput.value.split(',');
    // Add new skill ID
    skillIds.push(skill);
    // Update the hidden input field value
    skillsIdsInput.value = skillIds.join(',');

    // Get the skills list container
    const skillsList = document.getElementById('skills-list');
    // Create a new div element for the skill
    const skillDiv = document.createElement('div');
    skillDiv.classList.add('skill');
    skillDiv.addEventListener('click', function() {
      skillDiv.remove();
    });
    // Set the skill text
    // Create a <p> element
    const pTag = document.createElement('div');
    // Set text content to the skill variable
    pTag.textContent = skill;
    pTag.style.marginRight = "4px";
    // Append the <p> element to the skillDiv
    skillDiv.appendChild(pTag);
    skillDiv.style.backgroundColor = '#f0f0f0';
    skillDiv.style.padding = '4px 8px 4px 8px';
    skillDiv.style.margin = '4px';
    skillDiv.style.borderRadius = '4px';
    skillDiv.style.cursor = 'pointer';
    skillDiv.style.maxWidth = 'fit-content'
    skillDiv.style.fontSize = '14px';
    skillDiv.style.fontWeight = '400';
    skillDiv.style.display = 'flex';
    skillDiv.style.alignItems = 'center';
    // Create an SVG element
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    // Set SVG attributes
    svg.setAttribute('width', '16');
    svg.setAttribute('height', '16');
    svg.setAttribute('viewBox', '0 0 16 16');
    // Create SVG path
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    // Set path attributes
    path.setAttribute('d', 'M6.9987 12.8335C4.0587 12.8335 1.66536 10.4402 1.66536 7.50016C1.66536 4.56016 4.0587 2.16683 6.9987 2.16683C9.9387 2.16683 12.332 4.56016 12.332 7.50016C12.332 10.4402 9.9387 12.8335 6.9987 12.8335ZM6.9987 0.833496C3.31203 0.833496 0.332031 3.8135 0.332031 7.50016C0.332031 11.1868 3.31203 14.1668 6.9987 14.1668C10.6854 14.1668 13.6654 11.1868 13.6654 7.50016C13.6654 3.8135 10.6854 0.833496 6.9987 0.833496ZM8.72536 4.8335L6.9987 6.56016L5.27203 4.8335L4.33203 5.7735L6.0587 7.50016L4.33203 9.22683L5.27203 10.1668L6.9987 8.44016L8.72536 10.1668L9.66536 9.22683L7.9387 7.50016L9.66536 5.7735L8.72536 4.8335Z');
    path.setAttribute('fill', 'black'); // Set fill color to black
    path.setAttribute('fill-opacity', '0.87'); // Set fill opacity to 0.87
    // Append path to SVG
    svg.appendChild(path);
    // Append SVG to skillDiv
    skillDiv.appendChild(svg);
    // Append the skill div to the skills list
    skillsList.appendChild(skillDiv);
  }
</script>

<script>
  flatpickr("#start_date", {


  });
  flatpickr("#complete_date", {

  });
</script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script type="text/javascript" src="scripts/form.js"></script>

<script>
  ClassicEditor
    .create(document.querySelector('#requirements'), {
      toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
      heading: {
        options: [{
            model: 'paragraph',
            title: 'Paragraph',
            class: 'ck-heading_paragraph'
          },
          {
            model: 'heading1',
            view: 'h1',
            title: 'Heading 1',
            class: 'ck-heading_heading1'
          },
          {
            model: 'heading2',
            view: 'h2',
            title: 'Heading 2',
            class: 'ck-heading_heading2'
          }
        ]
      }
    })
    .catch(error => {
      console.error(error);
    });
</script>
<script>
  function handleSidebar() {
    //const sidebar = document.getElementsByClassName('xt-sidebar-organization1')[0];
    const sidebar = document.getElementById('jjk')
    console.log(sidebar)
    sidebar.style.display = (sidebar.style.display === 'block') ? 'none' : 'block';
  }
</script>
</body>

<!-- <?php
      include 'inc/footer.php';
      ?> -->

</html>