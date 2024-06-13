<?php
$docRoot = "../../";
include_once $docRoot."config/config.php";
include_once $docRoot."lib/Session.php";
Session::init();
include_once $docRoot."classes/Solvers.php";
include_once $docRoot."classes/Skills.php";
include_once $docRoot."classes/SMProfiles.php";
$solvers = new Solvers();
$skills = new Skills();
$smprofiles = new SMProfiles();
$partnerKey = "";
$genericOrgName = "Bennit Manufacturing Exchange Member";
$genericAbstract = "At Bennit.ai, we believe that connecting manufacturing challenges with the right subject matter experts is the key to success. That’s why we partnered with CESMII to offer The Manufacturing Exchange™, a powerful product that match challenges with industry professionals, ensuring successful project outcomes.";
$basePath = "https://" . $_SERVER['HTTP_HOST'];
header('Content-type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    if (!isset($_POST['partnerkey'])) {
        die('{"error":"missing partner key"}');
    }
    $partnerKey = $_POST['partnerkey'];
    if ($partnerKey == "session") {
        $partnerKey = Session::get("partnerKey");
    } 
    if (!in_array($partnerKey, $partnerKeys)) {
        die('{"error":"invalid partner key"}');
    }
    //Search query
    if (isset($_POST["query"]) && $_POST["query"] == "search") {
        if (isset($_POST['value']) && $_POST['value'] != "") {
            $allSolvers = [];
            if (isset($_POST['take']) && isset($_POST['skip'])) {
                $allSolvers = $solvers->searchSolversByKeywordWithPaging($_POST['value'], $_POST['take'], $_POST['skip']);
            } else {
                $allSolvers = $solvers->searchSolversByKeyword($_POST['value']);
            }
            foreach ($allSolvers as $thisSolver) {
                unset($thisSolver->fk_user_id);
                unset($thisSolver->imagepath);
                unset($thisSolver->rate);
                unset($thisSolver->created_at);
                unset($thisSolver->rate);
                if ($partnerKey == 9999) {
                    if (!isset($thisSolver->abstract) || $thisSolver->abstract == "")
                       $thisSolver->abstract = $genericAbstract;
                    $thisSolver->portraitImage = $basePath . "/solverImage.php?type=portrait&id=" . $thisSolver->id;
                    $thisSolver->bannerImage = $basePath . "/solverImage.php?type=banner&id=" . $thisSolver->id;
                } else {
                    $thisSolver->orgname = $genericOrgName;
                    $thisSolver->abstract = $genericAbstract;
                    $thisSolver->portraitImage = $basePath . "/assets/BennitPortrait.png";
                    $thisSolver->bannerImage = $basePath . "/assets/BennitBanner.png";    
                }
                $skillData = $skills->getAllSkillsForSolverById($thisSolver->id);
                foreach ($skillData as $thisSkill) {
                    unset($thisSkill->fk_skill_id);
                }
                $smprofileData = $smprofiles->getAllSMProfilesForSolverById($thisSolver->id);
                foreach ($smprofileData as $thisSmprofile) {
                    unset($thisSmprofile->fk_profile_id);
                    unset($thisSmprofile->fk_solver_id);
                    unset($thisSmprofile->last_activity);
                }
                $thisSolver->smProfiles = $smprofileData;
            }
            die(json_encode($allSolvers));
        }
    }
    //List query
    if (isset($_POST["query"]) && $_POST["query"] == "list") {
        $allSolvers = $solvers->getAllSolverData();
        $solverData = [];
        foreach ($allSolvers as $thisSolver)  {
            $apiSolver = new stdClass();
            $apiSolver->id = $thisSolver['id'];
            $apiSolver->headline = $thisSolver['headline'];
            array_push($solverData, $apiSolver);
        }
        die(json_encode($solverData));
    }
    //Detail query
    if (isset($_POST["query"]) && $_POST["query"] == "detail") {
        if (isset($_POST['value']) && $_POST['value'] != "" && is_numeric($_POST['value'])) {
            $solverData = $solvers->getSolverProfileById($_POST['value']);
            $solverId = $solverData->id;
            unset($solverData->fk_user_id);
            if ($partnerKey == 9999) {
                if (!isset($solverData->abstract) || $solverData->abstract == "")
                   $solverData->abstract = $genericAbstract;
                $solverData->portraitImage = $basePath . "/solverImage.php?type=portrait&id=" . $solverData->id;
                $solverData->bannerImage = $basePath . "/solverImage.php?type=banner&id=" . $solverData->id;
            } else {
                $solverData->orgname = $genericOrgName;
                $solverData->abstract = $genericAbstract;
                $solverData->portraitImage = $basePath . "/assets/BennitPortrait.png";
                $solverData->bannerImage = $basePath . "/assets/BennitBanner.png";    
            }
            $skillData = $skills->getAllSkillsForSolverById($solverId);
            foreach ($skillData as $thisSkill) {
                unset($thisSkill->fk_skill_id);
            }
            $solverData->skills = $skillData;
            $smprofileData = $smprofiles->getAllSMProfilesForSolverById($solverId);
            foreach ($smprofileData as $thisSmprofile) {
                unset($thisSmprofile->fk_profile_id);
                unset($thisSmprofile->fk_solver_id);
                unset($thisSmprofile->last_activity);
            }
            $solverData->smProfiles = $smprofileData;
            die(json_encode($solverData));
        }
    }
    die('{"error":"invalid query"}');
} else {
    die('{"error":"method not supported: ' . $_SERVER["REQUEST_METHOD"] . '"}');
}
?>