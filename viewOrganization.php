<!DOCTYPE html>
<?php
//In this page there is two modal on one modal we are selecting the opportunities we would like to match
//and once we submit the form on that modal conatinig the opportunity id we go to
?>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();
if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    //print_r($_POST);

}
$organizationDetails = $organizations->getAllOrganizationDataForUser(Session::get('userid'), $users);

?>
<div id="show-sidebar" style="background-color: white;" onclick="handleSidebar()">
  <div class="sidebar-toggle-button">
    <i class="fas fa-bars"></i>

  </div>
</div>
<div class="xt-card-organization1">
    <div class="xt-sidebar-organization1" id="jjk">
        <?php
        include 'inc/sidebar.php'
        ?>
        
    </div>
    <div class="xt-body-organization1 view-organization-body">
        <div class="mt-2 px-2 poppins-font font-700 font-24" style="flex:25%">
            My organization
        </div>
        <div class="px-2 mt-5" style="flex:50%">
            <form id="register_form" class="" action="" method="POST">
                <h2 class="poppoins-font font-700 " style="font-size: 48px;">Organization details</h2>
                <p class="inter-font font-400 font-16">Tell us about the organization you represent, so we can collect all the information necessary to match you with the best resources.</p>
                <div class="form-group">
                    <p class="inter-font font-700 font-16 mb-0" style="line-height: 24px;">Organization name</p>
                    <label class="inter-font font-400 font-12">The legal name of your company or organization</label>
                    <input type="text" name="orgname" id="orgname" style="background-color: white; border: 1px solid #ced4da;" value="<?php echo $organizationDetails[0]->orgname; ?>" class="form-control" minlength="3" maxlength="254" required>
                </div>
                <div class="form-group">
                    <label for="orgtype" style="color:black;font-size:16px;font-weight: 700;" class="mt-2">Type of organization</label>
                    <p style="color:black;font-size:12px;font-weight: 400;">The kind of organization you represent</p>
                    <select class="form-control" name="orgtype" id="orgtype" required>
                        <?php
                        foreach ($organizations->orgTypes() as $orgType) {
                            echo "<option value=\"" . $organizations->getOrgType($orgType) . "\">" . $orgType . "</option>";
                        }
                        ?>
                    </select>
                    <?php
                    $selectVal = getIfSet($getOInfo, "orgtype");
                    if ($selectVal != "") {
                        //TODO: This is a little hacky
                        echo "<script>document.getElementById('orgtype').value='$selectVal'</script>" . PHP_EOL;
                    }
                    ?>
                </div>
                <div class="form-group" style="color: black;">
                    <label class="inter-font font-700 font-16">Business EIN</label>
                    <p class="inter-font font-400 font-12">An Employer Identification Number (EIN) is used to identify a business entity</p>
                    <input type="text" class="form-control" name="buisness_ein" id="buisness_ein" value="<?php echo $organizationDetails[0]->buisness_ein ?>" style="background-color: white; border: 1px solid #ced4da;">
                </div>
                <div class="form-group">
                    <label for="description" style="color:black;font-size:16px;font-weight: 700;">Description</label>
                    <p style="color:black;font-size:12px;font-weight: 400;">A brief description of your organization</p>
                    <textarea name="description" id="description" style="width:100%; height:166px" class="form-control"><?php echo $organizationDetails[0]->description; ?></textarea>
                </div>
                <div class="form-group">
                    <h3 style="color: black; font-size:48px;font-weight: 700;">Location</h3>
                    <p style="font-size:16px;font-weight: 400;color:black;">Enter the primary location of the organization you represent.</p>
                    <label for="location" style="color:black;font-size:16px;font-weight: 700;" class="mt-2">Address line 1</label>
                    <input type="text" name="address_1" id="location" style="background-color: white; border: 1px solid #ced4da;" value="<?php echo $organizationDetails[0]->address1; ?>" class="form-control" minlength="3" maxlength="254" required>
                    <label for="address 2" style="color:black;font-size:16px;font-weight: 700;" class="mt-4">Address line 2</label>
                    <p style="color:black;font-size:12px;font-weight: 400;">Optional</p>
                    <input type="text" name="address_2" style="background-color: white; border: 1px solid #ced4da;" class="form-control" value="<?php echo $organizationDetails[0]->address2; ?>" minlength="3" maxlength="254">

                    <div style="background-color: white; color:black;display:flex ;" class="mt-4">
                        <div style="flex:50% !important;">
                            <label for="city">City</label>
                            <input type="text" name="city" value="<?php echo $organizationDetails[0]->city ?>" class="form-control" style="background-color: white; border: 1px solid #ced4da;">
                        </div>
                        <div style="flex:20%;" class="mx-2">
                            <label for="state">State</label>


                            <select id='state' name='state' class='form-control inter-font' style='background-color: white; border: 1px solid #ced4da;' required>";
                            <?php
                                $selectedState = $organizationDetails[0]->state; // Get the state value from the database
                                $states = array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District Of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming');
                                foreach ($states as $state) {
                                echo "<option value='$state'";
            if ($state === $selectedState) {
                echo " selected"; } echo ">$state</option>" ; }    ?> </select>
                        </div>
                        <div style=" flex:20%;display: flex;flex-direction: column;" class="">
                                    <label for="zip">Zip Code</label>
                                    <input type="text" value="<?php echo $organizationDetails[0]->zip ?>" name="zip" class="form-control" style="background-color: white; border: 1px solid #ced4da;">
                        </div>
                    </div>


                </div>
                <div class="form-group">
                    <h3 style="color: black; font-size:48px;font-weight: 700;">Contact Information</h3>
                    <p style="font-size:16px;font-weight: 400;color:black;">Help Bennit help you! Provide your organization’s website or a social media profile so we can facilitate the best matches and help ensure you have a comprehensive profile on The Manufacturing Exchange.</p>
                    <label for="location" style="color:black;font-size:16px;font-weight: 700;" class="mt-4">Website</label>
                    <p style="color:black;font-size:12px;font-weight: 400;">Your organization’s website</p>
                    <input type="url" name="website" id="website" value="<?php echo $organizationDetails[0]->website; ?>" style="background-color: white; border: 1px solid #ced4da;" class="form-control" placeholder="https://">
                    <label for="location" style="color:black;font-size:16px;font-weight: 700;" class="mt-4">Social Media</label>
                    <p style="color:black;font-size:12px;font-weight: 400;">Your organization’s primary social media account</p>
                    <input type="url" name="social_media" id="social_media" value="<?php echo $organizationDetails[0]->social_media; ?>" style="background-color: white; border: 1px solid #ced4da;" class="form-control" placeholder="https://">
                </div>
                <button type="submit" class="mb-4 inter-font font-700 font-16" style="border: none; background-color: #F5A800; width: 130px;height:40px;padding:8px 12px 8px 12px;border-radius: 4px;" name="createOrganization">Save</button>
            </form>
        </div>
        <div class="mt-2 px-2 save-button" style="flex:25%">
            <button class="inter-font font-700 font-16" style="border: none; background-color: #F5A800; width: 130px;height:40px;padding:8px 12px 8px 12px;border-radius: 4px;" >Save</button>
        </div>
    </div>

</div>
  <script>
    function handleSidebar() {
      //const sidebar = document.getElementsByClassName('xt-sidebar-organization1')[0];
      const sidebar = document.getElementById('jjk')
      console.log(sidebar)
      sidebar.style.display = (sidebar.style.display === 'block') ? 'none' : 'block';
    }
  </script>