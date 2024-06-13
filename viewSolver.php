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
$all_opportunity = $opportunities->getAllOpportunityDataForUser(Session::get('userid'), $users);
$real_solver_id = $solvers->getRealId($_GET['public_solver_id']);

if (isset($_GET['public_solver_id'])) {

  $solver_details = $solvers->getSolverProfileById($_GET['public_solver_id']);
  $solver_organization = $organizations->getOrganizationInfoByRealId($solver_details->fk_org_id);
}

?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<?php
// if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['opportunityCheckbox'])){
//  //print_r($_POST);
//  $result = $matches->matchMadeBySeeker($_POST['solverid'], $_POST['opportunityCheckbox'], Session::get('userid'));

//   if($result){
//      $modalScript = "
//         <script>
//             $(document).ready(function() {
//                 $('#afterMatch').modal('show');
//             });
//         </script>
//     ";
//     echo $modalScript;
//   }

// }
?>


<?php
//After match modal
?>


<?php
//Before match modal
?>
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content" style=" box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);border-radius: 4px;">
      <div class="modal-header" style="display: flex; align-items: center;">
        <h5 class="modal-title" id="exampleModalLongTitle" style="color: #012B33; font-weight: 700; font-size:20px; line-height: 28px;">Is it a Match ?</h5>
        <button type="button" class="close" data-dismiss="modal" style="display: flex; justify-content: center; margin-right: 2px; align-items: center; height: 40px; width: 40px; border: 2px solid #E7E7E8;" aria-label="Close">
          <div>

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="black" />
            </svg>
          </div>

        </button>
      </div>


      <form id="matchForm" action="" method="post">
        <div class="modal-body" style="color: black; max-height: 400px; overflow-y: auto;">
          <diV>
            <p style="font-size: 24px;font-weight: 700; list-style: 32px; color:black">Match with <span id="modal-org-name"></span></p>
          </diV>
          <div>
            <p class="inter-font font-16 font-400">Select which opportunities you would like to invite <span id="modal-org-name2" style="font-weight: bold;"></span> to help you with.</p>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>
                    <input type="checkbox" id="selectAllCheckbox">
                  </th>
                  <th class="inter-font font-700 font-12">Opportunity</th>
                  <th class="inter-font font-700 font-12">Start Date</th>
                  <th class="inter-font font-700 font-12">Location</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($all_opportunity as $opportunity) : ?>
                  <tr>
                    <td>
                      <input type="checkbox" name="opportunityCheckbox[]" id="opportunityCheckbox" class="opportunityCheckbox" value="<?php echo $opportunity->id ?>">
                    </td>
                    <td class="inter-font font-14 font-500" style=" color:black; text-decoration: underline;"><b><?php echo $opportunity->headline ?></b></td>
                    <td><?php echo $opportunity->start_date ?></td>
                    <td> <!-- Example for description, replace this with your actual description field -->
                      <?php echo $opportunity->location ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>

            </table>
          </div>
          <input type="hidden" name="user_public_id" id="user_public_id" value="<?php echo Session::get('userid') ?>">
          <div id="seeker-id-input">

          </div>

          <script>
            document.getElementById('selectAllCheckbox').addEventListener('change', function() {
              var checkboxes = document.getElementsByClassName('opportunityCheckbox');
              for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = this.checked;
              }
            });
          </script>

        </div>
        <div class="modal-footer" style="display: flex; justify-content: flex-start;">
          <button type="submit" name="submit" id="submit" class="btn " style="padding: 8px 12px 8px 12px; background-color: #F5A800; color:black;font-size: 16px; font-weight: 700;">Submit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

      </form>


    </div>
  </div>
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
  <div class="xt-body-organization1" style="background-color: #f3f3f3;">
    <div class="p-4" style="color: black;">
      <div class="my-4" style="display: flex;align-items: center; cursor: pointer;" onclick="goBack()">
        <div>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20.0002 11.0001V13.0001H8.00016L13.5002 18.5001L12.0802 19.9201L4.16016 12.0001L12.0802 4.08008L13.5002 5.50008L8.00016 11.0001H20.0002Z" fill="black" />
          </svg>

        </div>
        <div class="inter-font font-600 font-14 ml-2">Back to search results</div>
      </div>
      <div style="border-radius: 4px;box-shadow: 0px 0px 18px 0px rgba(0, 0, 0, 0.1); overflow: hidden;">
        <div class="solver-div" style="background-image: url('https://img.freepik.com/free-photo/textured-background-white-tone_53876-128610.jpg'); background-size: cover; background-position: center; height: 200px;">

        </div>
        <div style="display: flex;position: relative;">
          <div style="flex:15%;display: flex;justify-content: center;">
            <div style="width: 124px; height: 124px;background-color: gray;transform: translateY(-20px);">
              <img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png" alt="Your Image" style="width: 100%; height: 100%; object-fit: cover;">
            </div>

          </div>

          <div style="flex:60%;padding:8px;">
            <!-- <h1 class="poppins-font font-34 font-700" style="color:#012B33;"><?php echo $solver_organization->orgname ?></h1> -->
            <p class="inter-font" style="font-size: 18px;font-weight: 500;"><?php echo $solver_details->headline ?></p>

            <div style="display: flex; flex-wrap: wrap;" class="my-4">
              <?php
              $solver_skills = $skills->getAllSkillsForSolverById($solver_details->id);
              foreach ($solver_skills as $skill) :

              ?>
                <div class="inter-font font-14 font-400  mr-2" style="padding:4px 8px 4px 8px;background-color: #E7E7E8;border-radius: 4px;"><?php echo ucfirst($skill->skill_name) ?></div>
              <?php
              endforeach;
              ?>
            </div>

            <p class="inter-font font-700 font-16">Certifications</p>

            <div style="display: flex; flex-wrap: wrap;" class="my-4">
              <div style="padding:4px 8px 4px 8px;background-color: #E7E7E8;display: flex;align-items: center;border-radius: 4px;">
                <div><svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.00007 6.16661C4.00698 5.63832 4.21991 5.13362 4.59349 4.76003C4.96708 4.38645 5.47179 4.17352 6.00007 4.16661C6.52836 4.17352 7.03306 4.38645 7.40665 4.76003C7.78023 5.13362 7.99316 5.63832 8.00007 6.16661C7.99316 6.6949 7.78023 7.1996 7.40665 7.57319C7.03306 7.94677 6.52836 8.1597 6.00007 8.16661C5.47179 8.1597 4.96708 7.94677 4.59349 7.57319C4.21991 7.1996 4.00698 6.6949 4.00007 6.16661ZM6.00007 12.1666L8.66674 12.8333V10.7799C7.86252 11.2642 6.93875 11.5137 6.00007 11.4999C5.06139 11.5137 4.13762 11.2642 3.3334 10.7799V12.8333M6.00007 2.16661C5.47472 2.15702 4.95295 2.25499 4.46685 2.45447C3.98075 2.65396 3.54058 2.95075 3.1734 3.32661C2.79348 3.69419 2.49325 4.13601 2.2914 4.62459C2.08954 5.11317 1.99038 5.63806 2.00007 6.16661C1.99312 6.69181 2.09362 7.21289 2.2954 7.69783C2.49717 8.18277 2.79596 8.62135 3.1734 8.98661C3.53887 9.36609 3.97827 9.66659 4.46445 9.86955C4.95063 10.0725 5.47327 10.1736 6.00007 10.1666C6.52687 10.1736 7.04951 10.0725 7.53569 9.86955C8.02187 9.66659 8.46127 9.36609 8.82674 8.98661C9.20418 8.62135 9.50297 8.18277 9.70474 7.69783C9.90652 7.21289 10.007 6.69181 10.0001 6.16661C10.0098 5.63806 9.9106 5.11317 9.70875 4.62459C9.50689 4.13601 9.20666 3.69419 8.82674 3.32661C8.45956 2.95075 8.01939 2.65396 7.53329 2.45447C7.04719 2.25499 6.52543 2.15702 6.00007 2.16661ZM11.3334 6.16661C11.3192 6.80653 11.1905 7.43874 10.9534 8.03328C10.7398 8.63823 10.4166 9.19868 10.0001 9.68661V14.8333L6.00007 13.4999L2.00007 14.8333V9.68661C1.1372 8.71758 0.662405 7.46413 0.666738 6.16661C0.654988 5.467 0.786762 4.7724 1.05393 4.12571C1.32109 3.47901 1.71797 2.89394 2.22007 2.40661C2.70921 1.90001 3.29698 1.49903 3.94711 1.22843C4.59725 0.957827 5.29594 0.823343 6.00007 0.833276C6.7042 0.823343 7.4029 0.957827 8.05303 1.22843C8.70316 1.49903 9.29094 1.90001 9.78007 2.40661C10.2822 2.89394 10.6791 3.47901 10.9462 4.12571C11.2134 4.7724 11.3452 5.467 11.3334 6.16661Z" fill="#012B33" />
                  </svg>
                </div>
                <div class="font-500 inter-font font-14 ml-2">ISO 9001:2015 Quality Management System</div>
              </div>
            </div>

            <div class="my-4 pt-4 pb-4" style="border-top: 1px solid #E7E7E8; border-bottom: 1px solid #E7E7E8;">
              <p class="inter-font font-700 font-16">Location</p>
              <div style="display: flex; align-items: center;">
                <div>
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.99479 4.33398C8.43682 4.33398 8.86074 4.50958 9.1733 4.82214C9.48586 5.1347 9.66146 5.55862 9.66146 6.00065C9.66146 6.21952 9.61835 6.43625 9.53459 6.63846C9.45083 6.84067 9.32807 7.0244 9.1733 7.17916C9.01854 7.33393 8.83481 7.45669 8.6326 7.54045C8.43039 7.62421 8.21366 7.66732 7.99479 7.66732C7.55276 7.66732 7.12884 7.49172 6.81628 7.17916C6.50372 6.8666 6.32812 6.44268 6.32812 6.00065C6.32812 5.55862 6.50372 5.1347 6.81628 4.82214C7.12884 4.50958 7.55276 4.33398 7.99479 4.33398ZM7.99479 1.33398C9.23247 1.33398 10.4195 1.82565 11.2946 2.70082C12.1698 3.57599 12.6615 4.76297 12.6615 6.00065C12.6615 9.50065 7.99479 14.6673 7.99479 14.6673C7.99479 14.6673 3.32812 9.50065 3.32812 6.00065C3.32812 4.76297 3.81979 3.57599 4.69496 2.70082C5.57013 1.82565 6.75711 1.33398 7.99479 1.33398ZM7.99479 2.66732C7.11074 2.66732 6.26289 3.01851 5.63777 3.64363C5.01265 4.26875 4.66146 5.1166 4.66146 6.00065C4.66146 6.66732 4.66146 8.00065 7.99479 12.474C11.3281 8.00065 11.3281 6.66732 11.3281 6.00065C11.3281 5.1166 10.9769 4.26875 10.3518 3.64363C9.72669 3.01851 8.87885 2.66732 7.99479 2.66732Z" fill="#012B33" />
                  </svg>

                </div>
                <div class="inter-font font-600 font-16"><?php echo $solver_details->city . ',' . $solver_details->state . ',' . $solver_details->zip ?></div>
              </div>
              <div style="display: flex; flex-wrap: wrap;" class="my-4">
                <?php
                $locations_prefenece = explode(',', $solver_details->location_preference);
                foreach ($locations_prefenece as $mode) :
                ?>
                  <div class="mr-2" style="padding:4px 8px 4px 8px;background-color: #E7E7E8;display: flex;align-items: center;border-radius: 4px;">

                    <div class="font-500 inter-font font-14 "><?php echo $mode ?></div>
                  </div>
                <?php
                endforeach;
                ?>
              </div>
            </div>

            <div class="my-4 pb-4" style="border-bottom: 1px solid #E7E7E8;max-width: 700px;">
              <p class="inter-font font-700 font-16">About us</p>
              <p class="inter-font font-400 font-16"><?php echo $solver_details->experience ?></p>
            </div>
            <div class="p-2" style="display: flex;">
              <div class="p-2" style="flex:1;">
                <p class="inter-font font-700 font-16">Rate</p>
                <?php
                // Assuming $solver_details is your object containing rate and rate_type values
                $rate_display = '';
                if ($solver_details->rate_type == 'per_hour') {
                  $rate_display = '$' . $solver_details->rate . '/hr';
                } elseif ($solver_details->rate_type == 'per_day') {
                  $rate_display = '$' . $solver_details->rate . '/day';
                }
                ?>

                <p style="color: #012B33;" class="inter-font font-600 font-16">From <?php echo $rate_display; ?></p>

              </div>
              <div class="p-2" style="flex:1;">
                <p class="inter-font font-700 font-16">Availability</p>
                <p style="color: #012B33;" class="inter-font font-600 font-16"><?php echo $solver_details->availability ?></p>
              </div>
                <?php
                $solverIndustries = $industries->getIndustryForSolverById($_GET['public_solver_id'],$solvers);
                ?>
            </div>
          </div>

          <div style="flex:15%; padding: 8px;">
            <div class="inter-font font-700 font-16 text-center match" data-id="<?php echo $real_solver_id ?>" style="width:127px;height:40px;background-color: #F5A800; padding:8px 12px 8px 12px;border-radius: 4px;cursor: pointer;">Match</div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
<script>
  function goBack() {
    window.history.back();
  }
</script>
<script>
  $(document).ready(function() {
    $('#matchForm').submit(function(event) {
      event.preventDefault(); // Prevent default form submission
      $('#exampleModalLong').modal('hide')
      // Your form submission logic here (e.g., AJAX call)
      var formData = $(this).serialize(); // Serialize form data

      $.ajax({
        type: 'POST',
        url: 'modal_forms/match_by_seeker.php',
        data: formData,
        success: function(response) {
          $('body').append(response)
          // Handle success response
          //$('#afterMatch').html(response); // Update modal content with response
          // $('#afterMatch').modal('show'); // Show the modal
        },
        error: function(xhr, status, error) {
          // Handle error
          console.error(xhr.responseText);
        }
      });
    });

    $('.match').on('click', function() {
      var solverId = $(this).data('id');
      var name = $(this).data('name');
      console.log(solverId);
      console.log(name);
      var inputField = $('<input>');
      inputField.attr('name', 'solverid'); // Set name attribute
      inputField.attr('type', 'hidden');
      inputField.attr('id', 'solverid'); // Set type attribute
      inputField.val(solverId);
      $('#modal-org-name').text(name)
      $('#modal-org-name2').text(name)
      $('#seeker-id-input').append(inputField)
      $('#exampleModalLong').modal('show')
    })
  })
</script>
</body>

</html>