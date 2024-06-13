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

$opportunityid = null;
if (isset($_GET['opportunityid'])) {
  $opportunityid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['opportunityid']);
}
$opportunity_info = $opportunities->getOpportunityInfoById($opportunityid);
$opportunity_info_for_org = $opportunities->getAllOpportunityDataForOrg($opportunity_info->org_public_id, $organizations);
$solverdata_for_user = $solvers->getMAllSolverDataForUser(Session::get('userid'),$users,$organizations);
$skills_info = $skills->getAllSkillsForOpportunityById($opportunity_info->id);

?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<?php
// modal for match with opportunity
?>

<div id="matchWithOpportunity" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
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


      <form id="solvermatchForm" action="" method="post">
        <div class="modal-body" style="color: black; max-height: 400px; overflow-y: auto;">
          <diV>
            <p style="font-size: 24px;font-weight: 700; list-style: 32px; color:black">Match with <span id="modal-org-name"></span></p>
          </diV>
          <div>
            <p class="inter-font font-16 font-400">Select which Solver you would like to invite <span id="modal-org-name2" style="font-weight: bold;"></span> to help you with.</p>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>
                    <input type="checkbox" id="selectAllCheckbox">
                  </th>
                  <th class="inter-font font-700 font-12">Organization name</th>
                  <th class="inter-font font-700 font-12">Experience</th>
                  <th class="inter-font font-700 font-12">Rate</th>
                  
                </tr>
              </thead>
              <tbody>
                <?php foreach ($solverdata_for_user as $solver) : ?>
                  <tr>
                    <td>
                      <input type="checkbox" name="solverCheckbox[]" id="solverCheckbox" class="solverCheckbox" value="<?php echo $solver->public_id ?>">
                    </td>
                    <td class="inter-font font-14 font-500" style=" color:black; text-decoration: underline;"><b><?php echo $solver->orgname ?></b></td>
                    <?php
                       $useHeadline = $solver->headline;
                       $useHeadline = (strlen($useHeadline) > 33) ? substr($useHeadline,0,30).'...' : $useHeadline;
                     ?>
                    <td class="font-14"><?php echo $useHeadline ?></td>
                    <td><?php echo $solver->rate ?></td>
                    
                  </tr>
                <?php endforeach; ?>
              </tbody>

            </table>
          </div>
          <input type="hidden" name="user_public_id" id="user_public_id" value="<?php echo Session :: get('userid') ?>" >
          <div id="opportunity-id-input">

          </div>

          <script>
            document.getElementById('selectAllCheckbox').addEventListener('change', function() {
              var checkboxes = document.getElementsByClassName('solverCheckbox');
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

<div class="xt-card-organization">
  <div class="xt-sidebar-organization">
    <?php include 'inc/sidebar.php' ?>
  </div>
  <div class="xt-body-organization p-4">
    <div>
      go back
    </div>
    <div style="display:flex; border-radius: 4px;box-shadow: 0px 0px 18px 0px rgba(0, 0, 0, 0.1); overflow: hidden; color:black;">

      <div class="p-4" style="flex:60%">
        <div class="my-2">
          <h1 class="poppons-font font-700 font-34">
            <?php echo $opportunity_info->orgname ?>
          </h1>
          <p class="inter-font font-500 font-18">
            <?php echo $opportunity_info->headline ?>
          </p>
        </div>

        <div class="mt-4" style="border-top: 1px solid #E7E7E8;">
          <h2 class="inter-font font-700 font-16">
            Requirements
          </h2>
          <p class="inter-font font-400 font-16">
            <?php echo $opportunity_info->requirements ?>
          </p>
          <h2 class="inter-font font-700 font-16">
            Required Skills
          </h2>
          <div style="display: flex; flex-wrap: wrap;" class="my-4">
            <?php

            foreach ($skills_info as $skill) :

            ?>
              <div class="inter-font font-14 font-400  mr-2" style="padding:4px 8px 4px 8px;background-color: #E7E7E8;border-radius: 4px;"><?php echo ucfirst($skill->skill_name) ?></div>
            <?php
            endforeach;
            ?>
          </div>


        </div>

        <div class="mt-4" style="border-top: 1px solid #E7E7E8;">
          <h2 class="inter-font font-700 font-16">Locations</h2>
          <div style="display: flex; align-items: center;">
            <div>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.99479 4.33398C8.43682 4.33398 8.86074 4.50958 9.1733 4.82214C9.48586 5.1347 9.66146 5.55862 9.66146 6.00065C9.66146 6.21952 9.61835 6.43625 9.53459 6.63846C9.45083 6.84067 9.32807 7.0244 9.1733 7.17916C9.01854 7.33393 8.83481 7.45669 8.6326 7.54045C8.43039 7.62421 8.21366 7.66732 7.99479 7.66732C7.55276 7.66732 7.12884 7.49172 6.81628 7.17916C6.50372 6.8666 6.32812 6.44268 6.32812 6.00065C6.32812 5.55862 6.50372 5.1347 6.81628 4.82214C7.12884 4.50958 7.55276 4.33398 7.99479 4.33398ZM7.99479 1.33398C9.23247 1.33398 10.4195 1.82565 11.2946 2.70082C12.1698 3.57599 12.6615 4.76297 12.6615 6.00065C12.6615 9.50065 7.99479 14.6673 7.99479 14.6673C7.99479 14.6673 3.32812 9.50065 3.32812 6.00065C3.32812 4.76297 3.81979 3.57599 4.69496 2.70082C5.57013 1.82565 6.75711 1.33398 7.99479 1.33398ZM7.99479 2.66732C7.11074 2.66732 6.26289 3.01851 5.63777 3.64363C5.01265 4.26875 4.66146 5.1166 4.66146 6.00065C4.66146 6.66732 4.66146 8.00065 7.99479 12.474C11.3281 8.00065 11.3281 6.66732 11.3281 6.00065C11.3281 5.1166 10.9769 4.26875 10.3518 3.64363C9.72669 3.01851 8.87885 2.66732 7.99479 2.66732Z" fill="#012B33" />
              </svg>

            </div>
            <div class="inter-font font-600 font-16"><?php echo $opportunity_info->city . ',' . $opportunity_info->state . ',' . $opportunity_info->zip_code ?></div>
          </div>
          <div style="display: flex; flex-wrap: wrap;" class="my-4">

            <div class="mr-2" style="padding:4px 8px 4px 8px;background-color: #E7E7E8;display: flex;align-items: center;border-radius: 4px;">

              <div class="font-500 inter-font font-14 "><?php echo $opportunity_info->location ?></div>
            </div>

          </div>
        </div>
        <div class="mt-4" style="border-top: 1px solid #E7E7E8;">
          <div class="p-2" style="display: flex;">
            <div class="p-2" style="flex:1;">

              <p class="inter-font font-700 font-16">Rate</p>
              <?php
              $rate_display = "";
              if ($opportunity_info->rate_type == "per_day") {
                $rate_display = '$' . $opportunity_info->rate . '/day';
              } elseif ($opportunity_info->rate_type == "per_hour") {
                $rate_display = '$' . $opportunity_info->rate . '/hr';
              }
              ?>

              <p style="color: #012B33;" class="inter-font font-600 font-16"><?php echo $rate_display; ?></p>

            </div>
            <div class="p-2" style="flex:1;">
              <p class="inter-font font-700 font-16">Start and end dates</p>
              <p style="color: #012B33;" class="inter-font font-600 font-16"><?php echo $opportunity_info->start_date . ' to ' . $opportunity_info->complete_date; ?></p>
            </div>

          </div>
        </div>
      </div>
      <div class="p-4" style="flex:20%">
        <div style="height: 30vh;">
          <div class="inter-font font-700 font-16 text-center match" data-id="<?php echo $opportunityid ?>" style="width:127px;height:40px;background-color: #F5A800; padding:8px 12px 8px 12px;border-radius: 4px;cursor: pointer;">Match</div>
        </div>
        <?php
        if (count($opportunity_info_for_org) > 1) {
        ?>
          <div>
            <h2 class="inter-font font-700 font-14">More opportunity from this organizations</h2>
            <?php
            foreach ($opportunity_info_for_org as $orgOpportunity) :
              if ($orgOpportunity->public_id != $opportunityid) {
            ?> 
            <a style="color: inherit; text-decoration: none;" href="viewOpportunity.php?opportunityid=<?php echo $orgOpportunity->public_id ?>">
                <div class="inter-font font-500 font-14" style="text-decoration: underline;">
                  <?php echo $orgOpportunity->headline  ?>
                </div>
            </a>
            <?php
              }
            endforeach;
            ?>
          </div>
        <?php
        }
        ?>
      </div>
    </div>

  </div>

</div>


<script>
   $(document).ready(function (){
    $('#solvermatchForm').submit(function(){
        event.preventDefault();
        var formData = $(this).serialize();
          $.ajax({
        type: 'POST',
        url: 'fr_handle_match_by_solver.php',
        data: formData,
        success: function(response) {
             $('#matchWithOpportunity').modal('hide');
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
    })
    $('.match').on('click',function(){
        var opportunityPublic_id = $(this).data('id')
         var inputField = $('<input>');
      inputField.attr('name', 'opportunityPublic_id'); // Set name attribute
      inputField.attr('type', 'hidden');
      inputField.attr('id', 'opportunityPublic_id'); // Set type attribute
      inputField.val(opportunityPublic_id);

        $('#opportunity-id-input').append(inputField);
        $('#matchWithOpportunity').modal('show');
    })
   })
</script>

</body>

</html>