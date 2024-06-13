<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/xtheader.php';
include 'inc/xttopbar.php';
Session::checkSession();
$views->showAndClearPendingMessage();

//Figure out what we're working on
if (isset($_GET['orgid'])) {
  $orgid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['orgid']);
}
$getOInfo = null;

$allowed = false;
if (checkUserAuth("edit_organization_orthogonal", Session::get('roleid'))) {
  //Admins are allowed to edit organizations
  $allowed = true;
}
if (!$allowed && isset($orgid)) {
  //Org admins are allowed to edit their own organizations
  $orgLevel = $organizations->getUserOrganizationLevel($orgid, Session::get('userid'), $users);
  if ($orgLevel == 1)
    $allowed = true;
}

//Creation doesn't require edit permissions
if (isset($_GET['action']) && $_GET['action'] == "create_organization" && checkUserAuth("create_organization", Session::get('roleid'))) {
  if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['createOrganization'])) {
    $getOInfo = $_POST;
    
    $addOrg = $organizations->createOrganization($_POST, TRUE, $users);
    //Show pending results
    if (isset($addOrg)) {
      echo $addOrg;
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
    Session::set('pendingMsg', createUserMessage("error", "You may not edit an organization other than your own!"));
    header('Location:index.php');
    die();
  } else {
    //Process requested actions
    if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['updateOrganization']) && isset($orgid)) {
      $updateOrg = $organizations->updateOrganizationById($orgid, $_POST);
      //Show pending results
      if (isset($updateOrg)) {
        echo $updateOrg;
      }
    }
  }
}
//TODO: Prevent scraping
?>

<div class="xt-card-organization1 ">

  <div class="xt-sidebar-organization1 " style="align-items: flex-start;overflow-y: auto;">


    <?php
    if (isset($_GET['dummy_step'])) {
    ?>
      <div class="px-4">

        <h1 class="my-4" style="font-weight: 700; font-size: 15xp;">I'm a <span style="color: #F5A800;"><?php echo $_GET['dummy_step'] ?></span>
        </h1>
        <p class="my-4" style="font-weight: 400; font-size:14px">I have a problem I want to solve or an opportunity
          to
          improve</p>
        <?php
        if ($_GET['dummy_step'] == 'seeker') {
        ?>
          <a class="my-4" style="color: inherit; text-decoration: none;" href="onBoard.php?step=solver">
            <div>
              <div style="color: #F5A800;">
                Create solver profile instead
              </div>
            </div>
          </a>
        <?php
        } else {
        ?>
          <a class="my-4" style="color: inherit; text-decoration: none;" href="onBoard.php?step=seeker">
            <div>
              <div style="color: #F5A800;">
                Become a seeker instead
              </div>
            </div>
          </a>
        <?php
        }
        ?>


      </div>
    <?php
    }
    ?>
    <?php
    if (isset($_GET['dummy_step'])) {
    ?>
      <div class="px-4" style=" width:100%">

        <hr style="border-color: white;">
      </div>
    <?php
    }
    ?>
    <div class="step-indicators text-white my-4 px-4">
      <h1 style="font-size: 24px;font-weight: 700;">Lets get you set up</h1>
      <div class="step-indicators" style="color: rgba(255, 255, 255, 0.4);">

        <div class="step-indicator my-4 active">
          <div class="step-number active" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #FFFFFF66; font-size: 16px; font-weight: bold; border-radius: 4px; text-align: center; line-height: 28px; margin-right: 10px;">
           <div class="step-progress">1</div> <!-- Step number -->
             
           
          </div>
          Organization details
        </div> <!-- Step 1 -->
        <div class="step-indicator my-4">
          <div class="step-number " style="display: inline-block; width: 30px; height: 30px; border: 1px solid #FFFFFF66; font-size: 16px; font-weight: bold; border-radius: 4px; text-align: center; line-height: 28px; margin-right: 10px;">
            <div class="step-progress">2</div> <!-- Step number -->
             <!-- Check mark icon -->
          </div>
          Location
        </div> <!-- Step 2 -->
        <div class="step-indicator  my-4">
          <div class="step-number " style="display: inline-block; width: 30px; height: 30px; border: 1px solid #FFFFFF66; font-size: 16px; font-weight: bold; border-radius: 4px; text-align: center; line-height: 28px; margin-right: 10px;">
           <div class="step-progress">3</div> <!-- Step number -->
             <!-- Check mark icon -->
        </div> Contact Information
        </div> <!-- Step 3 -->
      </div>
    </div>
    <div class="bottom" style="margin-top: auto;color:#F5A800 ;width: 100%;">
      <a href="onBoard.php" style="text-decoration: none;color: inherit;">

        <div class="my-2 px-4">Cancel</div>
      </a>
      <hr style="border-color: #024552;">
      <a href="https://www.bennit.ai/" target="_blank" style="text-decoration: none;color: inherit;">

        <div class="my-2 px-4" style="color: #F5A800; text-decoration: underline;">Bennit Ai</div>
      </a>
    </div>
  </div>


  <div class="xt-body-organization1">
    <div class="progress-bar" style="background-color: #F5A800; height: 8px;"></div>

    <div style="width: 55%;margin: auto; display: flex; flex-direction: column; justify-content: flex-start;">

      <div class="card-body">

        <?php
        if (isset($orgid)) {
          $getOInfo = $organizations->getOrganizationInfoById($orgid);
        }
        ?>
        <div style="width:600px; margin:0px auto">

          <form id="register_form" class="" action="" method="POST">

            <fieldset>
              <div class="form-group">
                <h3 style="color: black; font-size:48px;font-weight: 700;">Organization Details</h3>
                <p style="font-size:16px;font-weight: 400;color:black;">Tell us about the
                  organization
                  you represent, so we can collect all the information necessary to match you with
                  the
                  best resources.</p>
                <label for="orgname" style="color:black;font-size:16px;font-weight: 700;" class="mt-4">Organization name</label>
                <p style="color:black;font-size:12px;font-weight: 400;">The legal name of your
                  company
                  or organization</p>
                <input type="text" name="orgname" id="orgname" style="background-color: white; border: 1px solid #ced4da;" value="<?php echo getIfSet($getOInfo, "orgname"); ?>" class="form-control" minlength="3" maxlength="254" required>
              </div>
              <div class="form-group">
                <label for="orgtype" style="color:black;font-size:16px;font-weight: 700;" class="mt-2">Type of organization</label>
                <p style="color:black;font-size:12px;font-weight: 400;">The kind of organization you represent</p>
                <select class="form-control" name="orgtype" id="orgtype" required>
                  <?php
                  foreach ($organizations->orgTypes() as $orgType) {
                    echo "<option value=\"" . $orgType . "\">" . $orgType . "</option>";
                  }
                  ?>
                </select>
                
              </div>
              <div class="form-group" style="color: black;">
                  <label class="inter-font font-700 font-16">Business EIN</label>
                  <p class="inter-font font-400 font-12">An Employer Identification Number (EIN) is used to identify a business entity</p>
                  <input type="text" class="form-control" name="buisness_ein" id="buisness_ein" style="background-color: white; border: 1px solid #ced4da;">              
                </div>
              
              <div class="form-group">
                <label for="description" style="color:black;font-size:16px;font-weight: 700;">Description</label>
                <p style="color:black;font-size:12px;font-weight: 400;">A brief description of your organization</p>
                <textarea name="description" id="description" style="width:100%; height:166px" class="form-control"><?php echo getIfSet($getOInfo, "description"); ?></textarea>
              </div>
              <input type="button" class="next-form btn btn-info" style="background-color: #F5A800; border: none; width:130px;color:black;font-weight: 700;" value="Next" />
            </fieldset>

            <fieldset>
              <div class="form-group">
                <h3 style="color: black; font-size:48px;font-weight: 700;">Location</h3>
                <p style="font-size:16px;font-weight: 400;color:black;">Enter the primary location of the organization you represent.</p>
                <label for="location" style="color:black;font-size:16px;font-weight: 700;" class="mt-2">Address line 1</label>
                <input type="text" name="address_1" id="location" style="background-color: white; border: 1px solid #ced4da;" value="<?php echo getIfSet($getOInfo, "location"); ?>" class="form-control" minlength="3" maxlength="254" required>
                <label for="address 2" style="color:black;font-size:16px;font-weight: 700;" class="mt-4">Address line 2</label>
                <p style="color:black;font-size:12px;font-weight: 400;">Optional</p>
                <input type="text" name="address_2" style="background-color: white; border: 1px solid #ced4da;" class="form-control" minlength="3" maxlength="254">

                <div style="background-color: white; color:black;display:flex ;" class="mt-4">
                  <div style="flex:50% !important;">
                    <label for="city">City</label>
                    <input type="text" name="city" class="form-control" style="background-color: white; border: 1px solid #ced4da;">
                  </div>
                  <div style="flex:20%;" class="mx-2">
                    <label for="state">State</label>


                    <select id="state" name="state" class="form-control" style="background-color: white; border: 1px solid #ced4da;" required>
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

              <input type="button" name="previous" class="previous-form btn btn-info" value="Previous" style="background-color: #E7E7E8; border: none; width:130px;color:black;font-weight: 700;" />
              <input type="button" name="next" class="next-form btn btn-info" value="Next" style="background-color: #F5A800; border: none; width:130px;color:black;font-weight: 700; " />


            </fieldset>

            <fieldset>
              <div class="form-group">
                <h3 style="color: black; font-size:48px;font-weight: 700;">Contact Information</h3>
                <p style="font-size:16px;font-weight: 400;color:black;">Help Bennit help you! Provide your organization’s website or a social media profile so we can facilitate the best matches and help ensure you have a comprehensive profile on The Manufacturing Exchange.</p>
                <label for="location" style="color:black;font-size:16px;font-weight: 700;" class="mt-4">Website</label>
                <p style="color:black;font-size:12px;font-weight: 400;">Your organization’s website</p>
                <input type="url" name="website" id="website" value="<?php echo getIfSet($getOInfo, "website"); ?>" style="background-color: white; border: 1px solid #ced4da;" class="form-control" placeholder="https://">
                <label for="location" style="color:black;font-size:16px;font-weight: 700;" class="mt-4">Social Media</label>
                <p style="color:black;font-size:12px;font-weight: 400;">Your organization’s primary social media account</p>
                <input type="url" name="social_media" id="social_media" value="<?php echo getIfSet($getOInfo, "social_media"); ?>" style="background-color: white; border: 1px solid #ced4da;" class="form-control" placeholder="https://">
              </div>

              <input type="button" name="previous" class="previous-form btn btn-info" value="Previous" style="background-color: #E7E7E8; border: none; width:130px;color:black;font-weight: 700;" />

              <?php
              if (isset($orgid)) {
                echo '<button type="submit" name="updateOrganization" class="btn btn-success">Update</button>' . PHP_EOL;
              } else {
                echo '<button type="submit" name="createOrganization" class="btn " style="background-color: #F5A800; border: none; width:130px;color:black;font-weight: 700; ">Create</button>' . PHP_EOL;
              }
              ?>

            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

</body>

</html>