<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
Session::checkSession();
$views->showAndClearPendingMessage();
//Check if user is ready to view this page
if (Session::get("roleid") > 2) {
    $userOrgs = $organizations->checkUserHasOrganizationOrRedirect($users);
    $foundSolver = false;

    if (isset($userOrgs) && is_array($userOrgs) && count($userOrgs) > 0) {
        foreach ($userOrgs as $thisOrg) {
            $sol = $solvers->getSolverProfileByOrgIdAndUserId($thisOrg->public_id, Session::get("userid"), $organizations, $users);
            if ($solvers->getSolverProfileByOrgIdAndUserId($thisOrg->public_id, Session::get("userid"), $organizations, $users)) {
                $foundSolver = true;
                break;
            }
        }
    }
}

$user_real_id = $users->getRealId(Session::get('userid'));

$solverdata_for_user = $solvers->getMAllSolverDataForUser(Session::get('userid'), $users, $organizations);


//Figure out what we're working on
$theQuery = "";
$skills1 = ""; //TODO: Support searching by skills
if (isset($_GET["query"])) {
    $theQuery = $_GET["query"];
}
if (isset($_GET["skills"])) {
    $skills = $_GET["skills"];
}
if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST["query"])) {
    $theQuery = $_POST["query"];
}

//Check permissions and process requested actions
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
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    print_r($_POST);
}
?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<?php
if (!$foundSolver && isset($_GET['query'])) {
    $modalScript = "
        <script>
            $(document).ready(function() {
                $('#exampleModalCenter').modal('show');
            });

           

        </script>
    ";

    // Echo the JavaScript code to the output
    echo $modalScript;
}


?>
<?php
// crete solver modal for user with no solver profile
?>
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="color: black;">
            <div class="modal-header">
                <div style="color: #012B33;font-size: 20px;font-weight: 700;line-height: 28px;">Create Your Solver Profile </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <div>

                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="black" />
                        </svg>
                    </div>
                </button>
            </div>
            <div class="modal-body">
                <div class="my-2" style="color:black;font-size: 24px;font-weight: 700;line-height: 32px;">Ready to Match?</div>
                <div class="my-2" style="color:black;font-size: 16px;font-weight: 400;line-height: 24px;">Before we can match you with this opportunity, you’ll need to create your Solver profile. This is how we can help determine whether it’s a good match!</div>
            </div>
            <div class="modal-footer" style="display: flex;justify-content: flex-start;">
                <a href="solver.php?action=create_solver" style="color: inherit; text-decoration: none;">
                    <button type="button" style="cursor: pointer; width: 146px;height: 40px; padding: 8px 12px 8px 12px;background-color: #F5A800; color:black;border: 1px solid #F5A800; border-radius: 4px;font-size: 16px;font-weight: 700;line-height: 24px">Create Profile</button>
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
//match with opportunity modal
?>
<div id="matchWithOpportunity" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">

        <div class="modal-content" style=" box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);border-radius: 4px;">


            <?php
            if (!$foundSolver) {
            ?>
                <div class="modal-header">
                    <div style="color: #012B33;font-size: 20px;font-weight: 700;line-height: 28px;">Create Your Solver Profile </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <div>

                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="black" />
                            </svg>
                        </div>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="my-2" style="color:black;font-size: 24px;font-weight: 700;line-height: 32px;">Ready to Match?</div>
                    <div class="my-2" style="color:black;font-size: 16px;font-weight: 400;line-height: 24px;">Before we can match you with this opportunity, you’ll need to create your Solver profile. This is how we can help determine whether it’s a good match!</div>
                </div>
                <div class="modal-footer" style="display: flex;justify-content: flex-start;">
                    <a href="solver.php?action=create_solver" style="color: inherit; text-decoration: none;">
                        <button type="button" style="cursor: pointer; width: 146px;height: 40px; padding: 8px 12px 8px 12px;background-color: #F5A800; color:black;border: 1px solid #F5A800; border-radius: 4px;font-size: 16px;font-weight: 700;line-height: 24px">Create Profile</button>
                    </a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            <?php } else { ?>
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
                                            $useHeadline = (strlen($useHeadline) > 33) ? substr($useHeadline, 0, 30) . '...' : $useHeadline;
                                            ?>
                                            <td class="font-14"><?php echo $useHeadline ?></td>
                                            <td><?php echo $solver->rate ?></td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>
                        <input type="hidden" name="user_public_id" id="user_public_id" value="<?php echo Session::get('userid') ?>">
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
            <?php
            } ?>

        </div>
    </div>
</div>

<?php
//opportunity view modal
?>

<div id="show-sidebar" style="background-color: white;" onclick="handleSidebar()">
  <div class="sidebar-toggle-button">
    <i class="fas fa-bars"></i>

  </div>
</div>
<div class="xt-card-organization1">
    <div class="xt-sidebar-organization1" id="jjk">
        <?php include 'inc/sidebar.php' ?>
        
    </div>


    <?php
    //View opportunity modal

    ?>
    <div class="modal modal_outer right_modal fade" style="color: black;" id="viewmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content modal_content2">


            </div>


        </div>
    </div>

    <div class="xt-body-organization1" style="color: black;">
        <div class="opportunity-search">
            <div class="xt-body-organization" style="color:black">
                <div style="width:100%; margin:0px auto; border-bottom: 2px solid #E7E7E8;">
                    <form class="findopportunity-form" action="" method="GET" id="searchForm" >
                        <div class="findopportunity-search-text">Find Opportunities</div>

                        
                        <div class="findopportunity-input-field">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.5 3C11.2239 3 12.8772 3.68482 14.0962 4.90381C15.3152 6.12279 16 7.77609 16 9.5C16 11.11 15.41 12.59 14.44 13.73L14.71 14H15.5L20.5 19L19 20.5L14 15.5V14.71L13.73 14.44C12.5505 15.4468 11.0507 15.9999 9.5 16C7.77609 16 6.12279 15.3152 4.90381 14.0962C3.68482 12.8772 3 11.2239 3 9.5C3 7.77609 3.68482 6.12279 4.90381 4.90381C6.12279 3.68482 7.77609 3 9.5 3ZM9.5 5C7 5 5 7 5 9.5C5 12 7 14 9.5 14C12 14 14 12 14 9.5C14 7 12 5 9.5 5Z" fill="black" fill-opacity="0.38" />
                            </svg>

                            <input type="text" placeholder="Search by keyword or skill, such as “Developer” or “PHP”" name="query" id="query" value="<?php if (isset($theQuery)) {
                                                                                                                                                            echo $theQuery;
                                                                                                                                                        } ?>" style="outline: none; border: none;width:100%;height:2.5rem;background-color: white;color:rgb(46, 46, 46)" minlength="3">
                        </div>
                        <div class="findopportunity-search-button" style="width:134px;text-align: center; font-weight: 600; font-size: 16px; border-radius: 4px;background-color: #F5A800; padding: 8px 12px 8px 12px ; cursor: pointer;" onclick="document.getElementById('searchForm').submit();">
                            Search
                        </div>
                        

                    </form>
                </div>
            </div>
        </div>
        <?php
        // When query is none show this =====================================================================================================================================================================================================================================
        ?>
        <div id="opportunity-gird">

        </div>







    </div>

</div>


<?php
//view opportunity modal script
?>
<script>
    $(document).ready(function() {
        function triggerSearch() {
            var action = 'searchResult';
            var query = $('#query').val().trim();


            // Log the values to the console
            console.log('Action:', action);
            console.log('Query:', query);


            $.ajax({
                url: "modal_forms/search_filter_opportunity.php",
                method: "POST",
                data: {
                    action: action,
                    query: query
                },
                success: function(data) {
                    $('#opportunity-gird').html(data);
                }
            });
        }
        $('#query').keyup(function(event) {
            event.preventDefault();
            triggerSearch();
        });
        triggerSearch();
    })
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


</html>