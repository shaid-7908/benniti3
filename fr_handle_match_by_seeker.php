<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
Session::checkSession();
$all_opportunity = $opportunities->getAllOpportunityDataForUser(Session::get('userid'), $users);
$real_solver_id = $solvers->getRealId($_GET['public_solver_id']);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['opportunityCheckbox'])){
 //print_r($_POST);
 $result = $matches->matchMadeBySeeker($_POST['solverid'], $_POST['opportunityCheckbox'], Session::get('userid'));

  if($result){
     $modalScript = "
        <script>
            $(document).ready(function() {
                $('#afterMatch').modal('show');
            });
        </script>
    ";
    echo $modalScript;
    echo '<div class="modal fade" id="afterMatch" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content"  style="border-radius: 4px;">
       <div class="modal-header">
        <div style="color: #012B33;font-size: 20px;font-weight: 700;line-height: 28px;">Match pending</div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <div>

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="black" />
            </svg>
          </div>
        </button>
      </div>
      <div class="modal-body" style="color: black;max-height: 400px; overflow-y: auto;">
        <p style="font-weight: 700; font-size: 24px; list-style: 32px;">Is it a match ?</p>
        <p style="font-weight: 400; font-size: 16px; list-style: 24px;">At Bennit, making great matches between subject matter experts and manufacturing challenges is so important that we review every potential match.</p>
        <p style="font-weight: 600; font-size: 16px; list-style: 24px;">What next ?</p>
        <p style="font-weight: 400; font-size: 16px; list-style: 10px;">After we review the Opportunity or Opportunities you selected and the skills of the suggested Solver, we’ll update this match with our guidance. </p>
        <p style="font-weight: 400; font-size: 16px; list-style: 10px;">Check back soon for confirmed matches on your <a href="index.php" style="text-decoration: underline; color:inherit"> Dashboard </a>, and keep the following Opportunities up-to-date so we know if it’s a good fit!</p>
        <div>
          <p style="font-weight: 700; font-size: 16px; list-style: 24px;">Opportunities Under Review</p>
          <div>
            <table class="table">
              <tbody>';
    
             
              foreach($result as $oportunityMatchid){

                  $matchedOpportunity = $opportunities->getOpportunityInfoByRealId($oportunityMatchid);

                  echo ' <tr>
                  <td style="text-decoration: underline; font-weight: 500;">'.$matchedOpportunity->headline.'</td>
                  <td>'.$matchedOpportunity->start_date.'</td>
                  <td>'.$matchedOpportunity->location.'</td>
                </tr>';
              }
              echo ' </tbody>
            </table>
          </div>
        </div>
      
      </div>
       <div class="modal-footer" style="display: flex; justify-content: flex-start;">
          <button type="button" name="submit" id="submit" class="btn " data-dismiss="modal" style="width: 130px;height: 40px; padding: 8px 12px 8px 12px; background-color: #F5A800; color:black;font-size: 14px; font-weight: 700;">Got It</button>
          <button type="button" class="" style="background-color: #E7E7E8; border:none;color:black;border-radius: 4px;width:155px;height:40px;font-weight: 700;font-size: 14px;" data-dismiss="modal">Go to Dashboard</button>
        </div>
    </div>
  </div>
</div>';

  }

}
?>



             