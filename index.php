<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
ob_start();
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();

$pendingMsg = Session::get("pendingMsg");
if (isset($pendingMsg)) {
  echo $pendingMsg;
}


Session::set("pendingMsg", NULL);
//Load organizations
$userOrgs = $organizations->getAllOrganizationDataForUser(Session::get("userid"), $users);
//print_r($userOrgs);
// Check for first run
if (Session::get("firstrun") == 1 && isset($userOrgs) && is_array($userOrgs) && count($userOrgs) > 0) {
  Session::set("firstrun", 0);  //Skip first run if already in an org somehow
}
$firstrunUrl = "onBoard.php";
$onboardingData = Session::get("onboarding");
if (isset($onboardingData) && isset($onboardingData->resumeUrl) && ($onboardingData->resumeUrl == "index.php" || !isset($onboarding->completedUrl))) {
  Session::set("firstrun", 0);  //Finish first run flow
  Session::set("onboarding", null);
} else {
  if (isset($onboardingData->resumeUrl) && $onboardingData->resumeUrl != "")
    $firstrunUrl = $onboardingData->resumeUrl;
}
if (Session::get("firstrun") == 1) {
  header('Location:'.$firstrunUrl);
   
}
//Load Subscription status
$subscriptionExists = false;
if (!$subscriptions->checkSubscriptionExists(Session::get("userid"), "", $users, $organizations)) {
  foreach ($userOrgs as $thisOrg) {
    if ($subscriptions->checkSubscriptionExists(Session::get("userid"), $thisOrg->public_id, $users, $organizations)) {
      $subscriptionExists = true;
      break;
    }
  }
} else {
  $subscriptionExists = true;
}

//Load matches for this solver
$userMatches = $matches->getAllMatchDataForUserId(Session::get('userid'), $approvedOnly = false, $users, $organizations, $opportunities);
$totalMatches = sizeof($userMatches);
if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['updateOpty'])) {
  $getOInfo = $_POST;
  print_r($_POST);
  $updateOpty = $opportunities->updateModalOpportunityById($_POST);
  //Show pending results
  if (isset($updateOpty)) {
    echo $updateOpty;
  }
}

if (isset($_GET["action"]) && $_GET["opportunityid"] && is_numeric($_GET["opportunityid"])) {
  $allowed = false;
  $removeId = (int)$_GET["opportunityid"];
  if (checkUserAuth($_GET["action"] . "_orthogonal", Session::get('roleid'))) {
    //Admins are allowed to manage opportunities
    $allowed = true;
  } else {
    //Check if this opportunity belongs to this user
    $userOpty = $opportunities->getOpportunityInfoById($removeId);
    if (isset($userOpty) && checkUserAuth("edit_opportunity", Session::get('roleid'))) {
      if (getIfSet($userOpty, "fk_user_id") ==  $users->getRealId(Session::get("userid"))) {
        $allowed = true;
      }
    }
  }
  if (!$allowed && isset($userOpty)) {
    //Org admins are allowed to manage opportunities in their org
    $orgLevel = $organizations->getUserOrganizationLevel(getIfSet($userOpty, "fk_org_id"), Session::get("userid"), $users);
    if (checkUserAuth($_GET["action"], $orgLevel)) {
      $allowed = true;
    }
  }
  if (!$allowed) {
    //Warn about disallowed action
    error_log("Insufficient privileges, user " . Session::get("userid") . " with level " . Session::get('roleid') . " attempted to delete an opportunity they did not own, and was ejected.");
    Session::set('pendingMsg', createUserMessage("error", "You may not manage opportunities you do not own!"));
    header('Location:opportunityList.php');
  } else {
    //Actually do the delete
    $removeOpty = $opportunities->deleteOpportunityById($removeId);
    if (isset($removeOpty)) {
      echo $removeOpty;
    }
  }
}
ob_end_flush();

?>


<?php
//Modal to edit opportunity
?>
<script src="scripts/skillsAjax.js"></script>

<div class="modal modal_outer right_modal fade" style="color: black;" id="viewopportunitymodal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog " role="document">
    <div class="modal-content">


    </div>


  </div>
</div>

<div class="modal modal_outer right_modal fade" style="color: black;" id="editmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Edit Opportunity</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">



















        < </div>
          <div class="modal-footer">

            <div>
              <button type="button" class="inter-font font-700 font-16" style="background-color: #E7E7E8;width:79px;height: 40px;padding: 8px 12px 8px 12px;border: none;border-radius: 4px;" data-dismiss="modal">Cancel</button>
              <button id="btnControlSubmit" class="bt btn-succes" style="background-color: #F5A800; padding: 4px 8px 4px 8px;color: black;border: none ; border-radius: 4px; width:130px;height: 40px;font-weight: 600;" onclick="controlledFormSubmit('frmOpportunityEdit')">Submit</button>

            </div>

          </div>
      </div>

    </div>


  </div>
</div>
<div id="show-sidebar" style="background-color: white;" onclick="handleSidebar()">
  <div class="sidebar-toggle-button">
    <i class="fas fa-bars"></i>

  </div>
</div>
<div class="xt-card-organization1">
  <div class="xt-sidebar-organization1 " id="jjk">
    <?php include 'inc/sidebar.php' ?>
  </div>

  <div class="xt-body-organization1" style="color: black;padding: 10px;">

    <?php
    $username = Session::get('fullname');
    ?>
    <h4 class="poppins-font font-700 font-34">Welcome, <?php echo $username; ?></h4>


    <?php

    $userOpportunity = $opportunities->getAllOpportunityDataForUser(Session::get('userid'), $users);
    $userSolverDetails = $solvers->getAllSolverDataForUser(Session::get('userid'), $users, $organizations);
    //if(!$userOpportunity || !$userSolverDetails){
    if (!$userOpportunity || !$userSolverDetails) {
    ?>
      <div class="index-grid-container">
        <div class="card" style="padding: 4px;">
          <div class="card-header" style="font-size: 20px; font-weight: 700; line-height: 28px; ">
            Organization onboarding
          </div>

          <div class="card-body">
            <p style="font-size: 16px; font-weight: 400; line-height: 24px;">Complete the following tasks to make the most of The Manufacturing Exchange.</p>
            <?php if ($userOrgs) { ?>

              <div class="d-flex p-2 my-4  align-items-center" style="border:2px solid #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; background-color: #024552; color:White;">
                Create organization profile <span><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.00016 16.1701L4.83016 12.0001L3.41016 13.4101L9.00016 19.0001L21.0002 7.00009L19.5902 5.59009L9.00016 16.1701Z" fill="white" />
                  </svg>
                </span>
              </div>

            <?php } else { ?>
              <a href="organization.php?action=create_organization" style="color: inherit; text-decoration: none;">
                <div class="d-flex p-2 my-4  align-items-center" style="border:2px solid #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:#024552;">
                  Create organization profile
                </div>
              </a>
            <?php } ?>
            <?php
            if ($userOpportunity) {
            ?>
              <div class="d-flex p-2 my-4  align-items-center" style="border:2px solid #024552;background-color: #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:white;">
                Create your first Opportunity <span><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.00016 16.1701L4.83016 12.0001L3.41016 13.4101L9.00016 19.0001L21.0002 7.00009L19.5902 5.59009L9.00016 16.1701Z" fill="white" />
                  </svg>
                </span>
              </div>
            <?php } else { ?>
              <a href="opportunity.php?action=create_opportunity" style="color: inherit;text-decoration: none;">

                <div class="d-flex p-2 my-4  align-items-center" style="border:2px solid #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:#024552;">
                  Create your first Opportunity
                </div>
              </a>
            <?php } ?>
            <?php if ($userSolverDetails) { ?>
              <div class="d-flex p-2  my-4 align-items-center" style="border:2px solid #024552;background-color:  #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:white;">
                Create your Solver profile <span><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.00016 16.1701L4.83016 12.0001L3.41016 13.4101L9.00016 19.0001L21.0002 7.00009L19.5902 5.59009L9.00016 16.1701Z" fill="white" />
                  </svg>
                </span>
              </div>
            <?php } else { ?>

              <a href="solver.php?action=create_solver&orgid=<?php echo $userOrgs[0]->public_id; ?>" style="color: inherit; text-decoration: none;">

                <div class="d-flex p-2  my-4 align-items-center" style="border:2px solid #024552; border-radius: 4px; height:40px;font-size: 16px; font-weight: 600; line-height: 24px; color:#024552;">
                  Create your Solver profile
                </div>
              </a>
            <?php } ?>
          </div>
        </div>
        <div class="card" style="padding: 4px;">
          <div class="card-header" style="font-size: 20px; font-weight: 700; line-height: 28px; ">
            Explore
          </div>
          <div class="card-body">
            <div class="my-4">
              <h6 style="font-size: 16px; font-weight: 700; line-height: 24px;">Find Solvers</h6>
              <p style="font-size: 16px; font-weight: 400; line-height: 24px;">Looking to solve a problem? Search for Solvers using some keywords. We’ll match you with the best resources.</p>
              <a href="findsolver.php" style="color: inherit; text-decoration: none;">
                <div style="font-size: 14px; font-weight: 700; line-height: 24px;padding:8px 12px 8px 12px;width: 120px;height:42px;background-color: #E7E7E8;">
                  Find Solvers
                </div>
              </a>
            </div>
            <div class="my-4">
              <h6 style="font-size: 16px; font-weight: 700; line-height: 24px;">Find Opportunities</h6>

              <p style="font-size: 16px; font-weight: 400; line-height: 24px;">Have skills and experience you want to share with others? We’ll match you with Seekers who need your expertise.</p>
              <a href="findopportunity.php" style="color: inherit; text-decoration: none;">

                <div style="font-size: 14px; font-weight: 700; line-height: 24px;padding:8px 12px 8px 12px;width: 160px;height:42px;background-color: #E7E7E8;">
                  Find Opportunities
                </div>
              </a>
            </div>
          </div>
        </div>

      </div>
    <?php
    } else {
      $user_real_id = $users->getRealId(Session::get('userid'));
      $newOpty = $opportunities->findLatestOpportunitiesinIndex($user_real_id);

      $gridActions = ["edit", "delete"];
      $gridColumns = ["location", "status", "organization"];

    ?>
      <div class="d-flex flex-column flex-md-row justify-content-between my-4">
        <div style="font-size: 24px; font-weight: 700;">Active Opportunities (<?php echo count($userOpportunity) ?>) </div>
        <div class="d-flex mt-3 mt-md-0">
          <a href="opportunityList.php" style="color: inherit;text-decoration: none;">
            <div class="mr-2 d-flex align-items-center justify-content-center" style="width:88px; height:40px; border:2px solid #F5A800; padding:8px 12px; font-size: 14px; font-weight: 700; line-height: 24px; border-radius: 4px;">
              View All
            </div>
          </a>
          <a href="opportunity.php?action=create_opportunity" style="color: inherit;text-decoration: none;">
            <div class="d-flex align-items-center justify-content-center" style="width:215px; height:40px; border:2px solid #F5A800; background-color: #F5A800; padding:8px 12px; font-size: 16px; font-weight: 700; line-height: 24px; border-radius: 4px;">
              Create New Opportunity
            </div>
          </a>
        </div>
      </div>


    <?php
      $views->makeOpportunityGrid2($newOpty, $gridColumns, $gridActions, $matches);
    } ?>
    <?php

    if (Session::get('roleid') == '1') {
    ?>
      <div class="card-body pr-2 pl-2">
        <div id="dashboard-container">
          <div id="dashboard-sidebar">
            <ul style="margin-top:10px">
              <li class="menu-action menu-action-highlight">
                <?php
                if ($totalMatches > 0) {
                  echo "<a href=\"matchList.php\">Review Matches</a> ";
                  echo " <i class=\"fas fa-certificate\">" . $totalMatches . "</i>" . PHP_EOL;
                } else {
                  echo "No matches yet!" . PHP_EOL;
                }
                ?>
              </li>
              <li class="menu-action menu-action-highlight"><a href="opportunityList.php?query">Find Opportunities</a></li>
              <li class="menu-action menu-action-highlight"><a href="solverList.php?query">Find Solvers</a></li>
            </ul>
            <ul style="margin-top: 28px;">
              <li class="menu-action"><a href="organizationList.php?userid=<?php echo Session::get("userid"); ?>">My Organizations</a></li>
              <li class="menu-action"><a href="opportunityList.php">My Opportunities</a></li>
            </ul>
          </div>
          <div id="dashboard-content">


            <br />
            <h5>
              The Manufacturing Exchange™ is where you can match opportunities with industry professionals, ensuring successful project outcomes.
            </h5>
            <?php
            if ($totalMatches == 1) {
              echo "<h5 style=\"padding-top:12px; padding-bottom:12px\">Bennit's AI has found 1 potential match for you to review!</h5>" . PHP_EOL;
            } elseif ($totalMatches > 1) {
              echo "<h5 style=\"padding-top:12px; padding-bottom:12px\">Bennit's AI has found " . $totalMatches . " potential matches for you to review!</h5>" . PHP_EOL;
            } else {
              echo "<h5>&nbsp;</h5>" . PHP_EOL;
            }
            if (!$subscriptions->checkSubscriptionExistsAnywhere(Session::get('userid'), $users, $organizations)) {
              echo "<div class='subscription-status'><span class='link-action'><a href='subscription.php?action=create_subscription'>Subscribe now</a></span> to make and review matches...</div>" . PHP_EOL;
            } else {
              echo "<div class='subscription-status'>You have an <a href='subscriptionList.php'>active subscription</a>, and are able to make and review matches...</div>" . PHP_EOL;
            }
            ?>
            <ul>
              <?php
              if (sizeof($userOrgs) < 1) {
                echo "<li>You don't belong to any Organizations yet. <span class='link-action'><a href='organization.php'>Create an Organization</a></span></li>" . PHP_EOL;
              } else {
                foreach ($userOrgs as $org) {
                  $opty = $opportunities->getAllOpportunityData($org->public_id, $organizations);
                  if (sizeof($opty) == 1) {
                    echo "<li>Your organization <b>" . $org->orgname . "</b> has <span class='link-action'><a href='opportunityList.php?org=" . $org->public_id . "'>" . sizeof($opty) . " active Opportunity.</a></span></li>" . PHP_EOL;
                  } elseif (sizeof($opty) > 1) {
                    echo "<li>Your organization <b>" . $org->orgname . "</b> has <span class='link-action'><a href='opportunityList.php?org=" . $org->public_id . "'>" . sizeof($opty) . " active Opportunities.</a></span></li>" . PHP_EOL;
                  } else {
                    if ($org->org_level > 0 && $org->org_level < 3)
                      echo "<li>Your organization <b>" . $org->orgname . "</b> doesn't have any Opportunities yet. <span class='link-action'><a href='opportunity.php?action=create_opportunity&orgid=" . getIfSet($org, "public_id") . "'>Create an Opportunity</a></span></li>" . PHP_EOL;
                  }
                  if ($org->id == 1 && Session::get('roleid') <= 2) {
                    $orgSolvers = $solvers->getSolverProfilesByRealOrgId(1);
                    if (count($orgSolvers) > 0) {
                      echo "<li>Solvers in your organization: </li><ul>" . PHP_EOL;
                      foreach ($orgSolvers as $thisSolver) {
                        $useHeadline = $thisSolver->headline;
                        $useHeadline = (strlen($useHeadline) > 43) ? substr($useHeadline, 0, 40) . '...' : $useHeadline;
                        echo "<li><a href='solverView.php?solverid=" . $thisSolver->public_id . "'>" . $useHeadline . "</a></li>" . PHP_EOL;
                      }
                      echo "</ul>" . PHP_EOL;
                    }
                  } else {
                    $solver = $solvers->getSolverProfileByOrgIdAndUserId($org->public_id, Session::get("userid"), $organizations, $users);
                    if (isset($solver) && isset($solver->public_id)) {
                      echo "<li>Your organization <b>" . $org->orgname . "</b> has an active <span class='link-action'><a href='solverView.php?solverid=" . getIfSet($solver, "public_id") . "'>Solver Profile.</a></span></li>" . PHP_EOL;
                    } else {
                      if ($org->id == 1 && ($org->org_level < 0 || $org->org_level > 3))  //org 1 is a special case
                        echo "<li>You don't have a Solver Profile in the organization <b>" . $org->orgname . "</b>. <span class='link-action'><a href='solver.php?action=create_solver&orgid=" . getIfSet($org, "public_id") . "'>Create your Solver Profile now</a></span></li>" . PHP_EOL;
                      else
                        echo "<li>Your organization <b>" . $org->orgname . "</b> doesn't have a Solver profile yet. <span class='link-action'><a href='solver.php?action=create_solver&orgid=" . getIfSet($org, "public_id") . "'>Create a Solver Profile</a></span></li>" . PHP_EOL;
                    }
                  }
                  echo "<br>" . PHP_EOL;
                }
              }
              ?>
            </ul>
          </div>
        </div>
      </div>
    <?php
    };
    ?>

  </div>


  <?php
  //Scripts to edit opportunity
  ?>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <script>
    $(document).ready(function() {
      $('.editbutton').on('click', function() {

        var opportunityId = $(this).data('id');
        console.log(opportunityId);

        $.ajax({
          url: 'modal_forms/fetch_opportunity.php', // Adjust the URL
          method: 'GET',
          data: {
            id: opportunityId
          },
          success: function(response) {
            $('#editmodal .modal-body').html(response);
            $('#editmodal').modal('show');
          }
        });


      });
      $('.viewopportunityinmodalbutton').on('click', function() {
        var opportunityId = $(this).data('id');
        console.log(opportunityId);
        $.ajax({
          url: 'modal_forms/fetch_opportunity_for_view.php', // Adjust the URL
          method: 'GET',
          data: {
            id: opportunityId
          },
          success: function(response) {
            $('#viewopportunitymodal .modal-content').html(response);

            $('#viewopportunitymodal').modal('show');
          }
        });
      })

    });
  </script>


  <script>
    var userid = <?php echo json_encode(Session::get('userid')); ?>;
    var isRequestPending = false; // Flag to prevent duplicate requests

    if (userid) {
      function longPolling() {
        if (isRequestPending) {
          return; // Prevent duplicate requests during previous call
        }

        isRequestPending = true; // Mark request as pending

        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4) {
            isRequestPending = false; // Reset flag for next request

            if (this.status == 200) {
              var hasUnreadMessages = this.responseText.trim() === 'true';
              var badge = document.querySelector('.notification-icon');
              badge.style.display = hasUnreadMessages ? 'block' : 'none';

              // Schedule the next long-polling request after a delay
              setTimeout(longPolling, 5000); // Adjust delay as needed (e.g., 3000-5000ms)
            } else {
              console.error("Error fetching unread messages:", this.statusText);
              // Handle errors gracefully (e.g., retry after a longer delay)
            }
          }
        };

        // Send a GET request with a timeout parameter for long-polling
        xhttp.open("GET", "apiCheckUnredMessages.php", true);
        xhttp.send();
      }

      longPolling(); // Initiate the first long-polling request
    }
  </script>
  <script>
    function handleSidebar() {
      //const sidebar = document.getElementsByClassName('xt-sidebar-organization1')[0];
      const sidebar = document.getElementById('jjk')
      console.log(sidebar)
      sidebar.style.display = (sidebar.style.display === 'block') ? 'none' : 'block';
    }
  </script>


</html>