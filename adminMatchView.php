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
$matchedid = null;
if (isset($_GET['matchedid'])) {
  $matchedid = preg_replace('/[^a-zA-Z0-9-]/', '', (int)$_GET['matchedid']);
}
$updateMessage = $matches->updateMessageStatusForMatches($matchedid);
$match_details = $matches->getALLMatchdataBypublicid($matchedid);
$opportunity_details = $opportunities->getOpportunityInfoByRealId($match_details[0]->fk_opportunity_id);

?>
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
    <div class="xt-body-organization1 p-2" style="display: flex;justify-content: center;">
        <div style="color: black; width:70%;box-shadow: 0px 0px 18px 0px rgba(0, 0, 0, 0.1);padding: 10px;">
           <div class="card-header">
                 <div id="msgDiv"></div>
                <h3>Match Status - <?php
                if($match_details[0]->matchmaker_approved == 0){
                    echo '<span class="text-danger">Not approved</span>';
                }else{
                    echo '<span class="text-success">Approved</span>';
                }
                ?></h3>
            </div>
            <?php
            $user_info_seeker = $users->getUserInfoByRealId($opportunity_details->fk_user_id);
            ?>
            <h1 class="inter-font font-700 font-24">Opportunity Details</h1>
            <h1 class="inter-font font-600 font-18">Seeker conatct info: <a href="mailto:<?php echo $user_info_seeker->email ?>"><?php echo $user_info_seeker->email ?></a></h1> 
            <h1 class="inter-font font-600 font-18">Seeker name : <span class="inter-font font-400 font-16"><?php echo $user_info_seeker->fullname ?></span></h1>
            <h2 class="inter-font font-600 font-18">Headline :</h2>
           <p class="inter-font font-500 font-14"><?php echo $opportunity_details->headline ?></p>
           <h2 class="inter-font font-600 font-18">Required Skills :</h2>
           <?php
           $all_skills = $skills->getAllSkillsForOpportunityById($opportunity_details->id);
           ?>
           <div style="display: flex;">
            <?php
            foreach($all_skills as $skill):
            ?>
            <div class="mr-2 px-2 my-2 py-1 font-14" style="background-color: #bdbdbd;border-radius: 4px;">
            <?php echo ucfirst($skill->skill_name) ?>
            </div>
            <?php
            endforeach
            ?>
           </div>
           <div class="btn btn-primary">View more</div>
            <?php
             $solver_details = $solvers->advancedGetSolverByRealId($match_details[0]->fk_solver_id);
             //print_r($solver_details);
             $user_info_solver = $users->getUserInfoByRealId($solver_details[0]->fk_user_id);
            ?>
             
           <h1 class="inter-font font-24 font-700 mt-2">Solver Details</h1>
           <h2 class="inter-font font-18 font-600">Solver contact info : <a href="mailto:<?php echo $user_info_solver->email ?>"><span><?php echo $user_info_solver->email ?></span></a></h2>
            <h1 class="inter-font font-600 font-18">Solver name : <span class="inter-font font-400 font-16"><?php echo $user_info_solver->fullname ?></span></h1>
           <h2 class="inter-font font-18 font-600">Solver Skills:</h2>
           <div style="display: flex;">
          <div class="mr-2 px-2 my-2 py-1 font-14" style="background-color: #bdbdbd;border-radius: 4px;"><?php echo ucfirst($solver_details[0]->skills) ?></div>
           </div>

             <div class="btn btn-primary">View more</div>
             <hr>
             <div style="display: flex;flex-direction: column;align-items: center;">
                <div class="inter-font font-24 font-700 my-2">Actions</div>
                <div>
                    <div class="btn btn-success" id="approveMatchBtn">Approve Match</div>
                    <div class="btn btn-danger">Reject Match</div>
                </div>
             </div>
        </div>
    </div>
</div>

<script>
document.getElementById('approveMatchBtn').addEventListener('click', function() {
    var matchId = '<?php echo $matchedid; ?>';
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'handelMatchByadmin.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Handle response if needed
            document.getElementById('msgDiv').innerHTML = xhr.responseText;
            setTimeout(function() {
                        window.location.reload();
                    }, 1000);
            console.log(xhr.responseText);
        }
    };
    xhr.send('matchId=' + matchId);
});
</script>

</body>

</html>