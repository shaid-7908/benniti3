<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
/* 
  This page handles disambiguation of orgs and solver profiles for potential matches
  Possibilities include:
  - Unknown: not enough data for a match
  - Exact match: one solver and one seeker (this is the only case where this page updates the database)
  - Multi-seeker: one solver, but multiple opportunities to be solved
  - Multi-solver: one opportunity to be solved, but multiple solver profiles
  This is because users can belong to multiple organizations. Additionally, users might
  arrive here not ready to make a match. Normally they shouldn't arrive here in such
  an "empty" scenario, because the UI wouldn't guide them here, but they might have an
  old bookmark, or might try to craft a URL to get around the normal flow.
  - Empty seeker: someone trying to match with a solver but not having any opportunities
  - Empty solver: someone trying to match with an opportunity but not having a solver profile
  */
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();
$opportunityMessages = $_GET['opportunityMessages'] ?? [];

?>
<div style="background-color: #012B33;min-height:100vh;padding:10px;display: flex;justify-content: center; align-items: center;">
    <div class="card" style="width: 60%;background-color: #024552; padding:8px; ">
    <div class="my-2" style="display: flex;align-items: center; background-color: #F5A800;background-color: #F5A800;display: flex; width: 200px;justify-content: flex-start; align-items: center; padding: 8px;border-radius: 4px;" onclick="goBack()">
                <div>
                     <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.0002 11.0001V13.0001H8.00016L13.5002 18.5001L12.0802 19.9201L4.16016 12.0001L12.0802 4.08008L13.5002 5.50008L8.00016 11.0001H20.0002Z" fill="black" />
                    </svg>
                </div>
                <div style="font-size: 14px; font-weight: 700;color:black">
                    Back to search results
                </div>
               </div>
        <div style="display: flex;justify-content: flex-start;align-items: center;">
               
               <!-- <div  class="my-2">
                <h3 style="">Match Pending</h3>
               
               </div> -->
               

        </div>
        <?php
        if (!empty($opportunityMessages)) {
         ?>
         <table class="table table-striped" style="border: 1px solid silver;">
            <thead>
                <tr>
                    <td>Id</td>
                    <td>Opportunity</td>
                    <td>Match Status</td>
                </tr>
            </thead>
            <tbody>
         <?php
            // Iterate over each opportunity message and display it
            foreach ($opportunityMessages as $opportunityId => $message) :
                $oppotunityDeatils = $opportunities->getOpportunityInfoByRealId($opportunityId);
                
            ?>  
            <tr>
                <td><?php echo $opportunityId ?></td>
                <td><?php echo $oppotunityDeatils->headline ?></td>
                <td><?php echo $message ?></td>
            </tr>  
            
           
          <?php endforeach ?>
            </tbody>
         </table>
         <?php
             
        } else {
            echo "<p>No opportunity messages found.</p>";
        }
        ?>
    </div>

</div>
<script>
function goBack() {
    window.history.back();
}
</script>
<?php
include 'inc/footer.php';
?>

</html>