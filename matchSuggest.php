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

$pendingMsg = Session::get("pendingMsg");
if (isset($pendingMsg)) {
    echo $pendingMsg;
}
Session::set("pendingMsg", NULL);

//Figure out who is suggesting the match
$matchSuggester = "unknown";
if ((isset($_GET["opportunityid"]) || isset($_GET["solverid"])) && isset($_GET["as"]))
    $matchSuggester = strtolower($_GET["as"]);
else {
    if (isset($_GET["opportunityid"]) && !isset($_GET["solverid"]))
        $matchSuggester = "solver";
    if (!isset($_GET["opportunityid"]) && isset($_GET["solverid"]))
        $matchSuggester = "seeker";
}

//Check if the query points to valid result
if (isset($_GET["solverid"]) && is_numeric($_GET["solverid"])) {
    if (!$solvers->checkSolverExistsById($_GET["solverid"])) {
        //Specified solver doesn't exist
        Session::set('pendingMsg', createUserMessage("error", "Could not find Solver with that ID to match with."));
        header('Location: solverList.php?query');
        die();
    }
}
if (isset($_GET["opportunityid"]) && is_numeric($_GET["opportunityid"])) {
    if (!$opportunities->checkOpportunityExistsById($_GET["opportunityid"])) {
        //Specified opportunity doesn't exist
        Session::set('pendingMsg', createUserMessage("error", "Could not find Opportunity with that ID to match with."));
        header('Location: opportunityList.php?query');
        die();
    }
}

//echo "Match Suggester at Start: " . $matchSuggester . "<br>";
//Figure out type of match and readiness
$originalMatcher = $matchSuggester;

switch ($matchSuggester) {
    case "seeker": {
            //A seeker has identified a solver they want to match with
            if (isset($_GET["solverid"]) && is_numeric($_GET["solverid"])) {
                $useSolver = $_GET["solverid"];

                if (isset($_GET["opportunityid"]) && is_numeric($_GET["opportunityid"])) {
                    //if a specific opportunity was passed in, make sure its valid then use that
                    //  this was already validated above
                    $useOpportunity = $_GET["opportunityid"];
                    $matchSuggester = "singleseeker";
                } else {
                    //otherwise, try to find the user's default opportunity
                    $userOpportunities = $opportunities->getAllOpportunityDataForUser(Session::get("userid"), $users);
                    if (!isset($userOpportunities) || count($userOpportunities) < 1)    //no opportunities
                        $matchSuggester = "emptyseeker";
                    elseif (count($userOpportunities) > 1)    //if there's more than one, we need the user to disambiguate
                        $matchSuggester = "multiseeker";
                    else {  //found a default!
                        $matchSuggester = "singleseeker";
                        $useOpportunity = $userOpportunities[0]->public_id;
                    }
                }
            }
            break;
        }
    case "solver": {
            //A solver has identified an opportunity they want to match with
            if (isset($_GET["opportunityid"]) && is_numeric($_GET["opportunityid"])) {
                $useOpportunity = $_GET["opportunityid"];

                if (isset($_GET["solverid"]) && is_numeric($_GET["solverid"])) {
                    //if a specific solver was passed in, make sure its valid, then use that
                    //  this was already validated above
                    $useSolver = $_GET["solverid"];
                    $matchSuggester = "singlesolver";
                } else {
                    //otherwise, try to find the user's default solver
                    $userSolvers = $solvers->getAllSolverDataForUser(Session::get("userid"), $users, $organizations);
                    if (!isset($userSolvers) || count($userSolvers) < 1)    //no solver profiles
                        $matchSuggester = "emptysolver";
                    elseif (count($userSolvers) > 1)    //if there's more than one, we need the user to disambiguate
                        $matchSuggester = "multisolver";
                    else {  //found a default!
                        $matchSuggester = "singlesolver";
                        $useSolver = $userSolvers[0]->public_id;
                    }
                }
            }
            break;
        }
    case "adminforseeker": {
            //almost like the user seeker case, except if not specified, we need to list ALL possibile opportunities to match with
            if (isset($_GET["solverid"]) && is_numeric($_GET["solverid"])) {
                $useSolver = $_GET["solverid"];

                if (!isset($_GET["opportunityid"])) {
                    $matchSuggester = "multiseeker";
                    $userOpportunities = $opportunities->getAllOpportunityData(null, $organizations);
                } else {
                    $matchSuggester = "singlesolver";
                    $useOpportunity = $_GET["opportunityid"];
                }
            }
            break;
        }
    case "adminforsolver": {
            //almost like the user solver case, except if not specified, we need to list ALL possibile solvers to match with
            if (isset($_GET["opportunityid"]) && is_numeric($_GET["opportunityid"])) {
                $useOpportunity = $_GET["opportunityid"];

                if (!isset($_GET["solverid"])) {
                    $matchSuggester = "multisolver";
                    $userSolvers = $solvers->getAllSolverData(null, $organizations);
                } else {
                    $matchSuggester = "singleseeker";
                    $useSolver = $_GET["solverid"];
                }
            }
            break;
        }
}
//echo "Match maker after Review: " . $matchSuggester . "<br>";
//echo "Original match maker: " . $originalMatcher . "<br>";
//echo "Matching Opportunity: " . $useOpportunity . " with Solver: " . $useSolver . "<br>";
//TODO: don't let people match with themselves!
?>
<div style="padding: 10px;background-color: #012B33; display: flex; justify-content: center; min-height: 80vh;">

    <div class="card " style="width:50vw; background-color: #024552; padding:10px;min-height: 40vh; ">
        <div class="card-header">
            <h3>Manufacturing Exchange - Match Making</h3>
        </div>
        <div id="userMessage" style="margin-top: -20px"></div>
        <div class="card-body pr-2 pl-2">
            <div class="table" style="width:100%">
                <div>
                    <?php
                    switch ($matchSuggester) {
                        case "singleseeker": {
                                $matcherType = "seeker";
                                if (strpos($originalMatcher, "admin") !== false)
                                    $matcherType = "admin";
                                $result = $matches->suggestMatch($useSolver, $useOpportunity, Session::get("userid"), $matcherType, $solvers, $opportunities);
                                $cleanResult = str_replace("'", "\\'", $result);
                                $cleanResult = str_replace(array("\r", "\n"), '', $cleanResult);
                                echo "<script>document.getElementById('userMessage').innerHTML = '" . $cleanResult . "';</script>";
                    ?>
                                <tr>
                                    <td colspan="3">
                                        <p><b>Is it a Match?</b></p>
                                        <p>At Bennit, making great matches between subject matter expert and manufacturing challenges is so important that we review every potential match...</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td align="center" class="onboard-choice-icon">
                                        <h3 style="color:#F5A800;"><i class="fas fa-hourglass mr-2"></i><br />Match Pending</h3>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <p>After we review your Opportunity and the skills of the suggested Solver, we'll update this match with our guidance. Check back soon for confirmed matches on your <a href="index.php">Dashboard</a> -- and keep your <a href="opportunityView.php?opportunityid=">Opportunity</a> up-to-date, so we know if its a good fit!</p>
                                    </td>
                                </tr>
                            <?php
                                break;
                            }
                        case "multiseeker": {
                            ?>
                                <tr>
                                    <td colspan="3">
                                        <p>Which Opportunity is the best match?</p>
                                        <p>We found multiple Opportunities that you might want to use. Which one should we use for this match?</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td align="center">
                                        <form method="GET">
                                            <div class="form-group">
                                                <label for="fk_org_id"><b>Choose Opportunity:</b></label>
                                                <select class="form-control" name="opportunityid" id="opportunityid" style="min-width: 200px; max-width: 500px;" required>
                                                    <?php
                                                    if (isset($userOpportunities)) {
                                                        foreach ($userOpportunities as $thisOpportunity) {
                                                            echo "<option value=\"" . $thisOpportunity->public_id . "\">" . $thisOpportunity->headline . " (" . $thisOpportunity->orgname . ")" . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <br />
                                                <input type="hidden" name="as" value="<?php echo $_GET['as']; ?>">
                                                <input type="hidden" name="solverid" value="<?php echo $_GET['solverid']; ?>">
                                                <button type="submit" class="btn btn-default">Confirm</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td></td>
                                </tr>
                            <?php
                                break;
                            }
                        case "emptyseeker": {
                            ?>
                                <tr>
                                    <td colspan="4">
                                        <p>At Bennit, we believe that connecting manufacturing challenges with the right subject matter experts is the key to success. That’s why we developed The Manufacturing Exchange™, a place for manufacturers with challenges can find people with the skills to help.</p>
                                        <p>Before you can suggest a match with a solver, you have to define the problem you need solved, or the opportunity to improve.</p>
                                        <p><span class='link-action'><a href="opportunity.php?action=create_opportunity">Create an Opportunity Now</a></span>
                                    </td>
                                </tr>
                            <?php
                                break;
                            }
                        case "singlesolver": {
                                $matcherType = "solver";
                                if (strpos($originalMatcher, "admin") !== false)
                                    $matcherType = "admin";
                                $result = $matches->suggestMatch($useSolver, $useOpportunity, Session::get("userid"), $matcherType, $solvers, $opportunities);
                                $cleanResult = str_replace("'", "\\'", $result);
                                $cleanResult = str_replace(array("\r", "\n"), '', $cleanResult);
                                echo "<script>document.getElementById('userMessage').innerHTML = '" . $cleanResult . "';</script>";
                            ?>
                                <div>
                                    <td colspan="3">
                                        <p style="font-size: 20px;"><b>Is it a Match?</b></p>
                                        <p>At Bennit, making great matches between manufacturing challenges and the right subject matter experts is so important that we review every potential match...</p>
                                    </td>
                                </div>
                                <div style="display: flex;justify-content: center;">

                                    <div align="center" class="onboard-choice-icon">
                                        <h3 style="color:#F5A800;"><i class="fas fa-hourglass mr-2"></i><br>Match Pending</h3>
                                    </div>

                                </div>
                                <tr>
                                    <td colspan="3">
                                        <p>After we review the Opportunity and your skills, we'll update this match with our guidance. Check back soon for confirmed matches on your <b><a href="index.php" style="color: #F5A800;">Dashboard</a></b> -- and keep your <b><a href="" style="color: #F5A800;">Solver Profile</a></b> up-to-date, so we know if its a good fit!</p>
                                    </td>
                                </tr>
                                <div style="display: flex;">
                                    <div style="background-color: #F5A800;display: flex; width: 200px;justify-content: flex-start; align-items: center; padding: 8px;border-radius: 4px;">
                                        <div>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20.0002 11.0001V13.0001H8.00016L13.5002 18.5001L12.0802 19.9201L4.16016 12.0001L12.0802 4.08008L13.5002 5.50008L8.00016 11.0001H20.0002Z" fill="black" />
                                            </svg>

                                        </div>
                                        <div style="color: black; font-weight: 600;font-size: 14px;">
                                            Back to search results
                                        </div>
                                    </div>
                                    <a href="index.php" style="text-decoration: none; color: inherit;">
                                        <div class="ml-2" style="border: 2px solid #F5A800;padding:8px;border-radius: 4px;">
                                            Go back to Dashboard
                                        </div>
                                    </a>
                                </div>
                            <?php
                                break;
                            }
                        case "multisolver": {
                            ?>
                                <tr>
                                    <td colspan="3">
                                        <p>Which Solver is the best match?</p>
                                        <p>We found multiple Solver Profiles that you might want to use. Which one should we use for this match?</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td align="center">
                                        <form method="GET">
                                            <div class="form-group">
                                                <label for="fk_org_id"><b>Choose Solver Profile:</b></label>
                                                <select class="form-control" name="solverid" id="solverid" style="min-width: 200px; max-width: 500px;" required>
                                                    <?php
                                                    if (isset($userSolvers)) {
                                                        foreach ($userSolvers as $thisSolver) {
                                                            echo "<option value=\"" . $thisSolver->public_id . "\">" . $thisSolver->headline . " (" . $thisSolver->orgname . ")" . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <br />
                                                <input type="hidden" name="as" value="<?php echo $_GET['as']; ?>">
                                                <input type="hidden" name="opportunityid" value="<?php echo $_GET['opportunityid']; ?>">
                                                <button type="submit" class="btn btn-default">Confirm</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td></td>
                                </tr>
                            <?php
                                break;
                            }
                        case "emptysolver": {
                            ?>
                                <div class="my-8" >
                                    <div >
                                        <p >At Bennit, we believe that connecting manufacturing challenges with the right subject matter experts is the key to success. That’s why we developed The Manufacturing Exchange™, a place for manufacturers with challenges can find people with the skills to help.</p>
                                        <p >Before you can suggest a match with an opportunity, you have to create your Solver Profile, that provides details about your skills and capabilities.</p>
                                        <a href="solver.php?action=create_solver" style="color: inherit; text-decoration: none;">
                                            <button type="button" style="cursor: pointer; width: 200px;height: 40px; padding: 8px 12px 8px 12px;background-color: #F5A800; color:black;border: 1px solid #F5A800; border-radius: 4px;font-size: 16px;font-weight: 700;line-height: 24px">Create solver Profile</button>
                                        </a>
                                    </div>
                                </div>
                            <?php
                                break;
                            }
                        default: {
                            ?>
                                <tr>
                                    <td colspan="4">
                                        <p>At Bennit, we believe that connecting manufacturing challenges with the right subject matter experts is the key to success. That’s why we developed The Manufacturing Exchange™, a place for manufacturers with challenges can find people with the skills to help.</p>
                                        <p>To help make matches, we need to know if you've a Seeker -- someone with a problem you need a solution for, or a Solver -- someone with skills to solve problems. It's OK if both are true, but for this particular match, how do you want to start?</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td align="center" style="padding-left: 20%" class="onboard-choice-icon">
                                        <a href="solverList.php?query">
                                            <h3><i class="fas fa-search mr-2"></i><br />As a Seeker</h3>
                                            I have a problem I want to solve, or an opportunity to improve.
                                        </a>
                                    </td>
                                    <td align="center" style="padding-right: 20%" class="onboard-choice-icon">
                                        <a href="opportunityList.php?query">
                                            <h3><i class="fas fa-lightbulb mr-2"></i><br />As a Solver</h3>
                                            I have skills and experience I want to share with others.
                                        </a>
                                    </td>
                                    <td></td>
                                </tr>
                    <?php
                                break;
                            }
                    }
                    ?>
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

<?php
include 'inc/footer.php';
?>

</html>