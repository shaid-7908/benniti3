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

$allowed = false;
if (checkUserAuth("edit_solver_orthogonal", Session::get('roleid'))) {
  //Admins are allowed to edit solvers
  $allowed = true;
}
if (!$allowed) {
  //Users are allowed to edit their own solver profiles
  if (isset($getSolverInfo) && $getSolverInfo->fk_user_id == $users->getRealId(Session::get('userid')) && checkUserAuth("edit_solver", Session::get('roleid'))) {
    $allowed = true;
  } else {
    //Org admins are allowed to edit solver profiles in their company
    if (isset($getSolverInfo)) {
      $orgLevel = $organizations->getUserOrganizationLevel($organizations->getPublicId($getSolverInfo->fk_org_id), Session::get('userid'), $users);
      if ($orgLevel == 1)
        $allowed = true;
    }
  }
}
//Creation doesn't require edit permissions
if (isset($_GET['action']) && $_GET['action'] == "create_solver") {
  if (isset($orgid) && $orgid != "") {
    $solverUserId = Session::get("userid");
    if (isset($_GET['userid']) && is_numeric($_GET['userid']))
      $solverUserId = $_GET['userid'];
    $getSolverInfo = $solvers->getSolverProfileByOrgIdAndUserId($orgid, $solverUserId, $organizations, $users);
  }
} elseif ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['createSolver'])) {
  $getSolverInfo = $_POST;
  $createSolver = $solvers->createSolver($_POST, TRUE, $users, $organizations);

  //Show pending results
  if (isset($createSolver)) {
    echo $createSolver;
  }
} else {  //Everything else does
  if (!$allowed) {
    //Warn about disallowed action
    error_log("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to edit another user's profile, and was ejected.");
    Session::set('pendingMsg', createUserMessage("error", "You may not edit a Solver Profile other than your own!"));
    header('Location:index.php');
    die();
  } else {
    //Process requested actions
    if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['updateSolver']) && isset($solverid)) {
      $updateSolver = $solvers->updateSolverById($solverid, $_POST, $organizations, $users);
      if (isset($updateSolver)) {
        echo $updateSolver;
      }
    }
  }
}
//TODO: Prevent scraping
?>
<style>
  .ck-content {
     height: 174px;
  }
</style>
<script src="scripts/SITSAjax.js"></script>
<div class="xt-card-organization" style="color: black;">
  <div class="xt-sidebar-organization1">
    <div class="step-indicators text-white my-2">
      <h3 style="color: #FFFFFF;font-size: 24px;font-weight: 700;line-height: 32px;letter-spacing: 0em;text-align: left;margin-left: 35px;margin-top: 25px;">Create your Solver profile</h3>
      <div class="step-indicators" style="display: flex; margin-top: 20px; gap: 15px; margin-left: 35px;  flex-direction: column; ">
        <div class="step-indicator active">
          <div class="mr-1 step-number active" style="width: 30px; height: 30px; border: 1px solid ; display: flex; justify-content: center; align-items: center; border-radius: 4px;">1</div>
          <span class="poppoins-font font-700 " style="margin-left: 4px">What you offer</span>
        </div> <!-- Step 1 -->
        <div class="step-indicator">
          <div class="mr-1 step-number" style="width: 30px; height: 30px; border: 1px solid ; display: flex; justify-content: center; align-items: center; border-radius: 4px;">2</div>
          <span style="margin-left: 4px">Rate and availability</span>
        </div> <!-- Step 2 -->
        <div class="step-indicator">
          <div class="mr-1 step-number" style="width: 30px; height: 30px; border: 1px solid ; display: flex; justify-content: center; align-items: center; border-radius: 4px;">3</div><span style="margin-left: 4px">Available locations</span>
        </div> <!-- Step 3 -->
      </div>
    </div>
   <div class="bottom" style="margin-top: auto;color:#F5A800 ;width: 100%;">
     

        <div class="my-2 px-4" onclick="handleback()">Cancel</div>
         <script>
          function handleback(){
            window.history.back();
          }
         </script>
      
      <hr style="border-color: #024552;">
      <a href="https://www.bennit.ai/" target="_blank" style="text-decoration: none;color: inherit;">

        <div class="my-2 px-4" style="color: #F5A800; text-decoration: underline;">Bennit Ai</div>
      </a>
    </div>
  </div>
  <div class="xt-body-organization1">
    <div style="width:600px; margin:0px auto">
      <form name="frmSolver" id="frmSolver" class="" action="solver.php?solverid=<?php echo getIfSet($getSolverInfo, "public_id"); ?>" method="POST">
        <fieldset>
          <h3 class="poppins-font" style="font-size: 48px; font-weight: 700;margin-top:20px">What you offer</h3>
          <p class="inter-font" style="font-size: 16px;font-weight: 400;">Help others on The Manufacturing Exchange understand why they should choose you.</p>
          <div class="form-group">
            <label class="inter-font" for="fk_org_id" style="font-size: 16px; font-weight: 700;">Organization</label>
            <select class="form-control" name="fk_org_id" id="fk_org_id" <?php if (isset($_GET["orgid"])) {
                                                                            echo "disabled";
                                                                          } ?> required>
              <?php
              if (isset($orgList)) {
                foreach ($orgList as $orgValue) {
                  echo "<option value=\"" . $orgValue->id . "\">" . $orgValue->orgname . "</option>";
                }
              }
              ?>
            </select>
            <?php
            $selectVal = getIfSet($getSolverInfo, "fk_org_id");
            //TODO: This is a little hacky
            if (isset($selectVal)) {
              echo "<script>document.getElementById('fk_org_id').value='$selectVal'</script>\r\n";
            }
            if (isset($orgid)) {
              $realOrgId = $organizations->getRealId($orgid);
              echo "<script>document.getElementById('fk_org_id').value='$realOrgId';document.getElementById('fk_org_id').disabled=true;</script>\r\n";
              echo "<input type='hidden' name='fk_org_id' value='$realOrgId'/>";
            }
            ?>
          </div>
         
          <div class="form-group">
            <label class="inter-font" for="industry" style="display: block;font-size: 16px; font-weight: 700;">Industry</label>
            <p class="inter-font" style="font-size: 12px; font-weight: 400;color: gray;">Select the industries that apply to your organization</p>
            <input type="text" id="industryText" name="industryText" value="<?php echo $_POST['industryText'] ?>" class="form-control" style="background-color: white;border: 1px solid #ced4da;" />
            <input type="hidden" id="industry_id" name="industry_id" />
            <div id="industries-list" class="my-2" style="display: flex; flex-wrap: wrap;">

            </div>
          </div>
          <div class="form-group">
            <label class="inter-font" for="industry" style="display: block;font-size: 16px; font-weight: 700;">Technology</label>
            <p class="inter-font" style="font-size: 12px; font-weight: 400;color: gray;">Select the technology areas in which your organization has expertise</p>
            <input type="text" id="technologyText" name="technologyText" value="<?php echo $_POST['technologyText'] ?>" class="form-control"  style="background-color: white;border: 1px solid #ced4da;" />
            <input type="hidden" id="technology_id" name="technology_id" />
            <div id="technologies-list" class="my-2" style="display: flex; flex-wrap: wrap;">

            </div>
          </div>
          <div class="form-group">
            <label class="inter-font" for="industry" style="display: block;font-size: 16px; font-weight: 700;">Speciality</label>
            <p class="inter-font" style="font-size: 12px; font-weight: 400;color: gray;">Select the specialties that your organization has experience with</p>
            <input type="text" id="specialityText" name="specialityText" value="<?php echo $_POST['specialityText'] ?>" class="form-control" onkeydown="addSpecialty(event)" style="background-color: white;border: 1px solid #ced4da;" />
            <input type="hidden" id="speciality_id" name="speciality_id" />
            <div id="specialties-list" class="my-2" style="display: flex; flex-wrap: wrap;">

            </div>
          </div>
          <div class="form-group">
            <label class="inter-font" for="experience" style="font-size: 16px; font-weight: 700;">Experience and expertise</label>
            <p class="inter-font" style="font-size: 12px; font-weight: 400;color: gray; line-height: 14px;">Provide details about your organization’s experience and areas of expertise</p>

            <textarea name="experience" id="experience" style="width:100%; height:300px" class="form-control"><?php echo getIfSet($getSolverInfo, "experience"); ?></textarea>
          </div>
          <div class="form-group">
            <label class="inter-font" for="certificates" style="font-size: 16px; font-weight: 700;">Certificates</label>
            <p class="inter-font" style="font-size: 12px; font-weight: 400;color: gray; line-height: 14px;">Press Enter after you’re done typing to add a certification. Add as many as you need.</p>
            <input type="text" id="certificates" name="certificates" class="form-control" onkeydown="addCretificate(event)" style="background-color: white;border: 1px solid #ced4da;" />
            <input type="hidden" id="cretificate_hidden" name="cretificate_hidden" />
            <div id="certificates-list" class="my-2" style="display: flex; flex-wrap: wrap;">

            </div>
          </div>
          <div class="form-group">
            <?php
            $skillText = "";
            $skillIds = "";
            if (getIfSet($getSolverInfo, "id")) {
              $getSkillsInfo = $skills->getAllSkillsForSolverById(getIfSet($getSolverInfo, "id"));
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
            <label class="inter-font" for="skillsText" style="font-size: 16px; font-weight: 700;">Skills</label>
            <p class="inter-font" style="font-size: 12px; font-weight: 400;color: gray; line-height: 14px;">Press Enter after you’re done typing to add a skill. Add as many as you like!</p>
            <input type="text" id="skillsText" name="skillsText" class="form-control" value="<?php echo $_POST['skillsText'] ?>" onkeydown="addSkill(event)" style="background-color: white;border: 1px solid #ced4da;">
            <input type="hidden" id="skillsIds" name="skillsIds" value="<?php echo $skillIds; ?>">
            <div class="inter-font" id="skills-list" class="my-2" style="display: flex; flex-wrap: wrap;">
              <!-- Skills will be dynamically added here -->
            </div>
          </div>

          <input type="button" class="next-form btn btn-info" style="background-color: #F5A800; border: none; width:130px;color:black;font-weight: 700; margin-bottom: 35px;" value="Next" />
        </fieldset>
        <fieldset>
          <h3 class="poppins-font" style="font-size: 48px; font-weight: 700;margin-top: 20px;">Rate and availability</h3>
          <p class="inter-font" style="font-size: 16px; font-weight: 400;">Let others know what they can expect from you.</p>
          <div class="form-group">
            <label class="inter-font" for="availability" style="font-size: 16px; font-weight: 700;">Availability</label>
            <p class="inter-font" style="font-size: 12px; font-weight: 400;color:gray">Any information about your availability</p>
            <input type="text" name="availability" id="availability" style="background-color: white; border: 1px solid #ced4da;" value="<?php echo getIfSet($getSolverInfo, "availability"); ?>" class="form-control" minlength="3" maxlength="254" required>
          </div>
          <div class="form-group">
            <label class="inter-font" for="rate" style="font-size: 16px; font-weight: 700;">Rate</label>
            <p class="inter-font" style="font-size: 12px; font-weight: 400;color:gray">The billing rate (or range) you expect</p>
            <div style="display: inline-block;">
              <input type="text" name="rate" id="rate" value="<?php echo getIfSet($getSolverInfo, "rate"); ?>" style="background-color: white; border: 1px solid #ced4da;" class="form-control" minlength="3" maxlength="254" required>
            </div>
            <div style="display: inline-block; margin-left: 5px;">
              <select class="form-control" name="rate_type" id="rate_type">
                <option class="inter-font" value="per_hour">Per Hour</option>
                <option class="inter-font" value="per_day">Per Day</option>
              </select>
            </div>
          </div>

          <input type="button" name="previous" class="previous-form btn btn-info" value="Previous" style="background-color: #E7E7E8; border: none; width:130px;color:black;font-weight: 700;" />
          <input type="button" class="next-form btn btn-info" style="background-color: #F5A800; border: none; width:130px;color:black;font-weight: 700;" value="Next" />


        </fieldset>


        <fieldset>
          <div class="form-group">
            <label for="location" class="poppins-font" style="font-size: 48px; font-weight: 700;margin-top:20px">Available locations</label>
            <p class="inter-font" style="font-size: 16px; font-weight: 400;">Do you offer your services on-prem, remote, or a hybrid of the two?</p>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="location[]" value="On premise" id="onPremise">
              <label class="form-check-label inter-font" for="onPremise">
                On Premise
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="location[]" value="hybrid" id="hybrid">
              <label class="form-check-label inter-font" for="hybrid">
                Hybrid
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="location[]" value="remote" id="remote">
              <label class="form-check-label inter-font" for="remote">
                Remote
              </label>
            </div>

            <div id="rows">
              <!-- Initial row -->
              <div style="background-color: white; color:black;display:flex ;" class="mt-4">
                <div style="flex:50% !important;">
                  <label class="inter-font" for="city">City</label>
                  <input type="text" name="city" class="form-control" style="background-color: white; border: 1px solid #ced4da;">
                </div>
                <div style="flex:20%;" class="mx-2">
                  <label class="inter-font" for="state">State</label>


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
                  <label for="zip">Zip Code</label>
                  <input type="text" name="zip" class="form-control" style="background-color: white; border: 1px solid #ced4da;">
                </div>
              </div>
            </div>
          </div>


          <?php
          if (Session::get("roleid") == '1') {
            $coachChecked = "";
            if (getIfSet($getSolverInfo, "is_coach"))
              $coachChecked = "checked";
            $externalChecked = "";
            if (getIfSet($getSolverInfo, "allow_external"))
              $externalChecked = "checked";
          ?>
            <div class="form-group">
              <b>Admin Flags: </b><br />
              <input type="checkbox" id="is_coach" name="is_coach" <?php echo $coachChecked; ?>> <label for="is_coach">Verified Coach</label><br />
              <input type="checkbox" id="allow_external" name="allow_external" <?php echo $externalChecked; ?>> <label for="allow_external">Allow External Marketplaces (eg: CESMII)</label><br />
              <input type="hidden" id="was_coach" name="was_coach" value="<?php echo getIfSet($getSolverInfo, "is_coach"); ?>">
              <input type="hidden" id="was_external" name="was_external" value="<?php echo getIfSet($getSolverInfo, "allow_external"); ?>">
            </div>
          <?php
          }
          ?>



          <input type="button" name="previous" class="previous-form btn btn-info" value="Previous" style="display: inline-block;background-color: #E7E7E8; border: none; width:130px;color:black;font-weight: 700;" />
          <button id="btnControlSubmit" style="display: inline-block;" class="btn btn-default" onclick="controlledFormSubmit('frmSolver')">Save</button>
          <?php
          if ((isset($solverid) && $solverid != 0) || getIfSet($getSolverInfo, "public_id")) {
            echo '<input type="hidden" name="updateSolver"/>';
          } else {
            echo '<input type="hidden" name="createSolver"/>';
          }
          echo "\r\n";
          ?>

        </fieldset>
      </form>
    </div>
  </div>
</div>
<script>
  document.getElementById('addRow').addEventListener('click', function() {
    var rowsContainer = document.getElementById('rows');
    var newRow = document.createElement('div');

    newRow.style.display = 'flex';
    newRow.style.justifyContent = 'space-between';
    newRow.innerHTML = `
                 <div style="display: flex;flex-direction: column;">
                    <label style="font-size: 16px;font-weight: 700;">City</label>
                  <input type="text" name="city[]" class="form-control" placeholder="City" style="background-color: white; border: 1px solid #ced4da;">
                </div>
                <div style="display: flex;flex-direction: column;">
                  <label style="font-size: 16px;font-weight: 700;">State</label>
                  <input type="text" name="state[]" class="form-control" placeholder="State" style="background-color: white; border: 1px solid #ced4da;">
                </div>
                <div style="display: flex;flex-direction: column;">
                   <label style="font-size: 16px;font-weight: 700;">Zip</label>
                  <input type="text" name="zip[]" class="form-control" placeholder="Zip Code" style="background-color: white; border: 1px solid #ced4da;">
                </div>
  `;
    rowsContainer.appendChild(newRow);
  });
</script>

<script>
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

<?php
//Add certificate handled here
?>
<script>
  let certificatesArray = [];

  function addCretificate(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      const certificateText = document.getElementById('certificates').value.trim();
      console.log(certificateText)
      if (certificateText != "") {
        addCretificateToList(certificateText);
        document.getElementById('certificates').value = "";
        updateCertificatesInput()
      }
    }
  }

  function addCretificateToList(certificateText) {
    console.log(certificateText)
    certificatesArray.push(certificateText);
    const certificateLists = document.getElementById('certificates-list')
    const certiDiv = document.createElement('div');
    certiDiv.style.marginRight = '4px';
    certiDiv.addEventListener('click', function() {
      removeFromCertificatesArray(certificateText);
      certiDiv.remove()
      updateCertificatesInput();

    })
    const nDiv = document.createElement('div')
    nDiv.textContent = certificateText;
    nDiv.style.marginRight = '4px'

    certiDiv.appendChild(nDiv)
    certiDiv.style.display = 'flex';
    certiDiv.style.backgroundColor = '#f0f0f0';
    certiDiv.style.padding = '4px 8px 4px 8px';
    certiDiv.style.alignItems = 'center';
    certiDiv.style.fontWeight = '400';
    certiDiv.style.borderRadius = '4px'



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
    certiDiv.appendChild(svg)
    certificateLists.appendChild(certiDiv);
  }

  function updateCertificatesInput(certificateText) {
    const certificatesInput = document.getElementById('cretificate_hidden');
    certificatesInput.value = certificatesArray
  }

  function removeFromCertificatesArray(certificateText) {
    const index = certificatesArray.indexOf(certificateText);
    if (index !== -1) {
      certificatesArray.splice(index, 1); // Remove the element at the found index
    }
  }
</script>

<?php
//Add industry handled here
?>
<script>
  let industriesArray = [];

  function addIndustry(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      const industryText = document.getElementById('industries').value.trim();
      if (industryText !== "") {
        addIndustryToList(industryText);
        document.getElementById('industries').value = "";
        updateIndustriesInput();
      }
    }
  }

  function addIndustryToList(industryText) {
    industriesArray.push(industryText);
    const industriesList = document.getElementById('industries-list');
    const industryDiv = document.createElement('div');
    industryDiv.style.marginRight = '4px';
    industryDiv.addEventListener('click', function() {
      removeFromIndustriesArray(industryText);
      industryDiv.remove();
      updateIndustriesInput();
    });
    const nDiv = document.createElement('div');
    nDiv.textContent = industryText;
    nDiv.style.marginRight = '4px';

    industryDiv.appendChild(nDiv);
    industryDiv.style.display = 'flex';
    industryDiv.style.backgroundColor = '#f0f0f0';
    industryDiv.style.padding = '4px 8px 4px 8px';
    industryDiv.style.alignItems = 'center';
    industryDiv.style.fontWeight = '400';
    industryDiv.style.borderRadius = '4px';

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '16');
    svg.setAttribute('height', '16');
    svg.setAttribute('viewBox', '0 0 16 16');
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', 'M6.9987 12.8335C4.0587 12.8335 1.66536 10.4402 1.66536 7.50016C1.66536 4.56016 4.0587 2.16683 6.9987 2.16683C9.9387 2.16683 12.332 4.56016 12.332 7.50016C12.332 10.4402 9.9387 12.8335 6.9987 12.8335ZM6.9987 0.833496C3.31203 0.833496 0.332031 3.8135 0.332031 7.50016C0.332031 11.1868 3.31203 14.1668 6.9987 14.1668C10.6854 14.1668 13.6654 11.1868 13.6654 7.50016C13.6654 3.8135 10.6854 0.833496 6.9987 0.833496ZM8.72536 4.8335L6.9987 6.56016L5.27203 4.8335L4.33203 5.7735L6.0587 7.50016L4.33203 9.22683L5.27203 10.1668L6.9987 8.44016L8.72536 10.1668L9.66536 9.22683L7.9387 7.50016L9.66536 5.7735L8.72536 4.8335Z');
    path.setAttribute('fill', 'black');
    path.setAttribute('fill-opacity', '0.87');
    svg.appendChild(path);
    industryDiv.appendChild(svg);
    industriesList.appendChild(industryDiv);
  }

  function updateIndustriesInput() {
    const industriesInput = document.getElementById('industry_hidden');
    industriesInput.value = industriesArray.join(',');
  }

  function removeFromIndustriesArray(industryText) {
    const index = industriesArray.indexOf(industryText);
    if (index !== -1) {
      industriesArray.splice(index, 1);
    }
  }
</script>

<?php
//Add technology handled here
?>
<script>
  let technologiesArray = [];

  function addTechnology(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      const technologyText = document.getElementById('technologies').value.trim();
      if (technologyText !== "") {
        addTechnologyToList(technologyText);
        document.getElementById('technologies').value = "";
        updateTechnologiesInput();
      }
    }
  }

  function addTechnologyToList(technologyText) {
    technologiesArray.push(technologyText);
    const technologiesList = document.getElementById('technologies-list');
    const technologyDiv = document.createElement('div');
    technologyDiv.style.marginRight = '4px';
    technologyDiv.addEventListener('click', function() {
      removeFromTechnologiesArray(technologyText);
      technologyDiv.remove();
      updateTechnologiesInput();
    });
    const nDiv = document.createElement('div');
    nDiv.textContent = technologyText;
    nDiv.style.marginRight = '4px';

    technologyDiv.appendChild(nDiv);
    technologyDiv.style.display = 'flex';
    technologyDiv.style.backgroundColor = '#f0f0f0';
    technologyDiv.style.padding = '4px 8px 4px 8px';
    technologyDiv.style.alignItems = 'center';
    technologyDiv.style.fontWeight = '400';
    technologyDiv.style.borderRadius = '4px';

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '16');
    svg.setAttribute('height', '16');
    svg.setAttribute('viewBox', '0 0 16 16');
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', 'M6.9987 12.8335C4.0587 12.8335 1.66536 10.4402 1.66536 7.50016C1.66536 4.56016 4.0587 2.16683 6.9987 2.16683C9.9387 2.16683 12.332 4.56016 12.332 7.50016C12.332 10.4402 9.9387 12.8335 6.9987 12.8335ZM6.9987 0.833496C3.31203 0.833496 0.332031 3.8135 0.332031 7.50016C0.332031 11.1868 3.31203 14.1668 6.9987 14.1668C10.6854 14.1668 13.6654 11.1868 13.6654 7.50016C13.6654 3.8135 10.6854 0.833496 6.9987 0.833496ZM8.72536 4.8335L6.9987 6.56016L5.27203 4.8335L4.33203 5.7735L6.0587 7.50016L4.33203 9.22683L5.27203 10.1668L6.9987 8.44016L8.72536 10.1668L9.66536 9.22683L7.9387 7.50016L9.66536 5.7735L8.72536 4.8335Z');
    path.setAttribute('fill', 'black');
    path.setAttribute('fill-opacity', '0.87');
    svg.appendChild(path);
    technologyDiv.appendChild(svg);
    technologiesList.appendChild(technologyDiv);
  }

  function updateTechnologiesInput() {
    const technologiesInput = document.getElementById('technology_hidden');
    technologiesInput.value = technologiesArray.join(',');
  }

  function removeFromTechnologiesArray(technologyText) {
    const index = technologiesArray.indexOf(technologyText);
    if (index !== -1) {
      technologiesArray.splice(index, 1);
    }
  }
</script>

<?php
//Add Speciality handelled here
?>

<script>
  let specialtiesArray = [];

  function addSpecialty(event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      const specialtyText = document.getElementById('specialties').value.trim();
      if (specialtyText !== "") {
        addSpecialtyToList(specialtyText);
        document.getElementById('specialties').value = "";
        updateSpecialtiesInput();
      }
    }
  }

  function addSpecialtyToList(specialtyText) {
    specialtiesArray.push(specialtyText);
    const specialtiesList = document.getElementById('specialties-list');
    const specialtyDiv = document.createElement('div');
    specialtyDiv.style.marginRight = '4px;'
    specialtyDiv.addEventListener('click', function() {
      removeFromSpecialtiesArray(specialtyText);
      specialtyDiv.remove();
      updateSpecialtiesInput();
    });
    const nDiv = document.createElement('div');
    nDiv.textContent = specialtyText;
    nDiv.style.marginRight = '4px';

    specialtyDiv.appendChild(nDiv);
    specialtyDiv.style.display = 'flex';
    specialtyDiv.style.backgroundColor = '#f0f0f0';
    specialtyDiv.style.padding = '4px 8px 4px 8px';
    specialtyDiv.style.alignItems = 'center';
    specialtyDiv.style.fontWeight = '400';
    specialtyDiv.style.borderRadius = '4px';

    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '16');
    svg.setAttribute('height', '16');
    svg.setAttribute('viewBox', '0 0 16 16');
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', 'M6.9987 12.8335C4.0587 12.8335 1.66536 10.4402 1.66536 7.50016C1.66536 4.56016 4.0587 2.16683 6.9987 2.16683C9.9387 2.16683 12.332 4.56016 12.332 7.50016C12.332 10.4402 9.9387 12.8335 6.9987 12.8335ZM6.9987 0.833496C3.31203 0.833496 0.332031 3.8135 0.332031 7.50016C0.332031 11.1868 3.31203 14.1668 6.9987 14.1668C10.6854 14.1668 13.6654 11.1868 13.6654 7.50016C13.6654 3.8135 10.6854 0.833496 6.9987 0.833496ZM8.72536 4.8335L6.9987 6.56016L5.27203 4.8335L4.33203 5.7735L6.0587 7.50016L4.33203 9.22683L5.27203 10.1668L6.9987 8.44016L8.72536 10.1668L9.66536 9.22683L7.9387 7.50016L9.66536 5.7735L8.72536 4.8335Z');
    path.setAttribute('fill', 'black');
    path.setAttribute('fill-opacity', '0.87');
    svg.appendChild(path);
    specialtyDiv.appendChild(svg);
    specialtiesList.appendChild(specialtyDiv);
  }

  function updateSpecialtiesInput() {
    const specialtiesInput = document.getElementById('specialty_hidden');
    specialtiesInput.value = specialtiesArray.join(',');
  }

  function removeFromSpecialtiesArray(specialtyText) {
    const index = specialtiesArray.indexOf(specialtyText);
    if (index !== -1) {
      specialtiesArray.splice(index, 1);
    }
  }
</script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script type="text/javascript" src="scripts/form.js"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create( document.querySelector( '#experience' ),{
          toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
            ]
        }
        })
        .catch( error => {
            console.error( error );
        });
</script>
</body>

</html>