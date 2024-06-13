<?php
include_once $docRoot . "lib/Database.php";
include_once $docRoot . "lib/Session.php";

/* Matches operate on public_id */
$snowflake = new \Godruoyi\Snowflake\Snowflake;

class Matches
{

  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function getRealId($publicId)
  {
    if (!isset($publicId) || $publicId == "") {
      error_log("Could not find real id for matches when public id not set");
      return false;
    }
    $sql = "SELECT id FROM tbl_matches WHERE public_id = :publicid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':publicid', $publicId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result[0]->id;
    } else {
      error_log("Could not find real id for matches with public id " . $publicId);
      return false;
    }
  }

  public function checkMatchExists($solverId, $opportunityId, $solvers, $opportunities)
  {
    $solverId = $solvers->getRealId($solverId);
    $opportunityId = $opportunities->getRealId($opportunityId);
    if ($solverId && $opportunityId) {
      $sql = "SELECT * from tbl_matches WHERE fk_solver_id=:solverid AND fk_opportunity_id=:opportunityid";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':solverid', $solverId);
      $stmt->bindValue(':opportunityid', $opportunityId);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }

  public function matchMadeBySolver($solverIds, $opportunityId, $matchedby, $solvers, $opportunities)
  {
    $successfulSolver = [];

    // Loop through the solver IDs array
    foreach ($solverIds as $solverId) {
      if (!$this->checkMatchExists($solverId, $opportunityId, $solvers, $opportunities)) {
        $snowflake = new \Godruoyi\Snowflake\Snowflake;
        $newPublicId = $snowflake->id();
        $orsolverId = $solverId;
        $solverId = $solvers->getRealId($solverId);
        $opportunityId = $opportunities->getRealId($opportunityId);

        if ($solverId && $opportunityId) {
          if ($solverId == "" || $opportunityId == "" || !is_numeric($solverId) || !is_numeric($opportunityId)) {
            continue;
          } else {
            $sql = "INSERT INTO tbl_matches(public_id, fk_opportunity_id, fk_solver_id, matched_by, solver_match) VALUES(:publicid, :opportunityid, :solverid, :matchedby, :actor);";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':publicid', $newPublicId);
            $stmt->bindValue(':solverid', $solverId);
            $stmt->bindValue(':opportunityid', $opportunityId);
            $stmt->bindValue(':matchedby', $matchedby);
            $stmt->bindValue(':actor', $matchedby);
            $result = $stmt->execute();
            if ($result) {
              $successfulSolver[] = $orsolverId;
              $allInsertedIds[] = $newPublicId;
            }
          }
        }
      } else {
        $successfulSolver[] = $solverId;
      }
    }

    return (object) [
      'successfulSolver' => $successfulSolver,
      'allInsertedIds' => $allInsertedIds,
    ];
  }


  public function checkMatchExistsByRealSolverID($solverId, $opportunityId)
  {
    if ($solverId && $opportunityId) {
      $sql = "SELECT * from tbl_matches WHERE fk_solver_id=:solverid AND fk_opportunity_id=:opportunityid";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':solverid', $solverId);
      $stmt->bindValue(':opportunityid', $opportunityId);
      $stmt->execute();
      if ($stmt->rowCount() > 0) {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }

  public function matchMadeBySeeker($solverID, $opportunityIds, $matchedby)
  {
    $seeker_id = $matchedby;
    $successfulOpportunities = [];

    foreach ($opportunityIds as $opportunityId) {
      if (!$this->checkMatchExistsByRealSolverID($solverID, $opportunityId)) {
        $snowflake = new \Godruoyi\Snowflake\Snowflake;
        $newPublicId = $snowflake->id();

        if ($solverID && $opportunityId) {
          if ($solverID == "" || $opportunityId == "" || !is_numeric($solverID) || !is_numeric($opportunityId)) {
            continue; // Skip incomplete matches
          } elseif (!isset($matchedby) || !is_numeric($matchedby)) {
            continue; // Skip incomplete matches
          } else {
            $sql = "INSERT INTO tbl_matches(public_id, fk_opportunity_id, fk_solver_id, matched_by,seeker_match) VALUES(:publicid,:opportunityid,:solverid,:matchedby,:actor);";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':publicid', $newPublicId);
            $stmt->bindValue(':solverid', $solverID);
            $stmt->bindValue(':opportunityid', $opportunityId);
            $stmt->bindValue(':matchedby', $matchedby);
            $stmt->bindValue(':actor', $seeker_id);
            $result = $stmt->execute();
            if ($result) {
              $successfulOpportunities[] = $opportunityId;
              $allInsertedIds[] = $newPublicId;
            }
          }
        } else {
          continue; // Skip incomplete matches
        }
      } else {
        // Match exists, add the opportunity ID
        $successfulOpportunities[] = $opportunityId;
      }
    }

    return (object) [
      'successfulOpportunities' => $successfulOpportunities,
      'allInsertedIds' => $allInsertedIds
    ];
  }




  public function suggestMatch($solverId, $opportunityId, $matchedby, $matcherType, $solvers, $opportunities)
  {
    if (!$this->checkMatchExists($solverId, $opportunityId, $solvers, $opportunities)) {
      $snowflake = new \Godruoyi\Snowflake\Snowflake;
      $newPublicId = $snowflake->id();

      $solverId = $solvers->getRealId($solverId);
      $opportunityId = $opportunities->getRealId($opportunityId);
      //echo "Matching between " . $solverId . " and " . $opportunityId . " from matcher: " . $matcherType;
      if ($solverId && $opportunityId) {
        if ($solverId == "" || $opportunityId == "" || !is_numeric($solverId) || !is_numeric($opportunityId)) {
          return createUserMessage("error", "All fields are required!");
        } elseif (!isset($matchedby) || filter_var($matchedby, FILTER_SANITIZE_NUMBER_INT) == FALSE) {
          return createUserMessage("error", "Match must be submitted by a valid user");
        } else {
          $sql = "INSERT INTO tbl_matches(public_id, fk_opportunity_id, fk_solver_id, matched_by";
          if ($matcherType == "seeker")
            $sql .= ",seeker_match";
          if ($matcherType == "solver")
            $sql .= ",solver_match";
          if ($matcherType == "admin")
            $sql .= ",matchmaker_approved";
          $sql .= ") VALUES(:publicid, :opportunityid, :solverid, :matchedby";
          //if ($matcherType == "seeker" || $matcherType == "solver" || $matcherType == "admin")
          $sql .= ",:actor);";
          $stmt = $this->db->pdo->prepare($sql);
          $stmt->bindValue(':publicid', $newPublicId);
          $stmt->bindValue(':opportunityid', $opportunityId);
          $stmt->bindValue(':solverid', $solverId);
          $stmt->bindValue(':matchedby', $matchedby);
          $stmt->bindValue(':actor', $matchedby);
          $result = $stmt->execute();
          if ($result) {
            return createUserMessage("success", "Match made. Pending review...");
          } else {
            return createUserMessage("error", "Match not complete. Something went wrong!");
          }
        }
      } else {
        return createUserMessage("error", "Match not complete. Solver or Opportunity could not be found.");
      }
    }
    return createUserMessage("success", "Match already exists. Review still pending...");
  }

  public function getAllMatchDataForUserId($userId, $approvedOnly = true, $users, $organizations, $opportunities, $_debugMode = false)
  {

    if ($userId) {
      //find all organizations for user's public id
      $userOrgs = $organizations->getAllOrganizationDataForUser($userId, $users);
      $userMatches = [];
      foreach ($userOrgs as $userOrg) {
        //for each organization, accumulate matches
        $orgMatches = $this->getAllMatchDataForOrgIdSolvers($userOrg->public_id, $approvedOnly, $organizations);
        if ($_debugMode) {
          echo "<br>Matches for org: " . $userOrg->public_id . " count " . sizeof($orgMatches) . "<br>";
          print_r($orgMatches);
          echo "<br>";
        }
        foreach ($orgMatches as $orgMatch) {
          if (!$this->isMatchInArray($orgMatch, $userMatches)) {
            $userMatches[] = $orgMatch;
          }
        }
        if ($_debugMode) {
          echo "<br>User Matches now: " . sizeof($userMatches) . "<br>";
          print_r($userMatches);
          echo "<br>";
        }
      }
      foreach ($userOrgs as $userOrg) {
        $optys = $opportunities->getAllOpportunityDataForOrg($userOrg->public_id, $organizations);
        foreach ($optys as $opty) {
          $optyMatches = $this->getAllMatchDataForOpportunityId($opty->public_id, $approvedOnly, $opportunities);
          if ($_debugMode) {
            echo "<br>Matches for opportunity: " . $opty->public_id . " count " . sizeof($optyMatches) . "<br>";
            print_r($optyMatches);
            echo "<br>";
          }
          foreach ($optyMatches as $optyMatch) {
            if (!$this->isMatchInArray($optyMatch, $userMatches)) {
              $userMatches[] = $optyMatch;
            }
          }
          if ($_debugMode) {
            echo "<br>User Matches now: " . sizeof($userMatches) . "<br>";
            print_r($userMatches);
            echo "<br>";
          }
        }
      }
      return $userMatches;
    }
    return null;
  }
  public function getALLMatchdataBypublicid($matched_id)
  {
    $sql = "SELECT * FROM tbl_matches WHERE public_id = :matched_id";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':matched_id', $matched_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $result;
  }
  //Get all matches where given org is the solver
  public function getAllMatchDataForOrgIdSolvers($orgId, $approvedOnly = true, $organizations)
  {
    $orgId = $organizations->getRealId($orgId);
    if ($orgId) {
      $sql = "SELECT 
      tbl_matches.*, 
      tbl_solvers.id as solver_id, 
      tbl_solvers.public_id as solver_org_publicid, 
      tbl_solvers.fk_org_id as solver_org_id, 
      tbl_solvers.headline as solver_headline, 
      tbl_opportunities.id as opportunity_id, 
      tbl_opportunities.public_id as opportunity_publicid, 
      tbl_opportunities.fk_org_id as opportunity_org_id, 
      tbl_opportunities.headline as opportunity_headline,
      tbl_organizations.orgname as org_name, 
      tbl_organizations.location as location 
      FROM tbl_matches 
      INNER JOIN tbl_solvers ON tbl_matches.fk_solver_id=tbl_solvers.id 
      INNER JOIN tbl_opportunities ON tbl_matches.fk_opportunity_id=tbl_opportunities.id 
      INNER JOIN tbl_organizations ON tbl_opportunities.fk_org_id=tbl_organizations.id 
      WHERE tbl_solvers.fk_org_id=:orgid";
      if ($approvedOnly)
        $sql .= " AND tbl_matches.matchmaker_approved = 1";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':orgid', $orgId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    return null;
  }
  public function getAllMatchDataForOpportunityByRealId($opty_id)
  {
    $sql = "SELECT * FROM tbl_matches WHERE fk_opportunity_id = :opty_id";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':opty_id', $opty_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $result;
  }
  public function updateMessageStatusForMatches($matchid)
  {
    $sql = "UPDATE tbl_message_queue SET is_read = 1 WHERE matched_id = :matchid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindParam(':matchid', $matchid, PDO::PARAM_INT);
    $stmt->execute();
  }
  public function getAllMatchDataForOpportunityId($opportunityId, $approvedOnly, $opportunities)
  {
    $opportunityId = $opportunities->getRealId($opportunityId);
    if ($opportunityId) {
      $sql = "SELECT 
      tbl_matches.*, 
      tbl_solvers.id as solver_id, 
      tbl_solvers.public_id as solver_org_public_id, 
      tbl_solvers.fk_org_id as solver_org_id, 
      tbl_solvers.headline as solver_headline, 
      tbl_opportunities.id as opportunity_id, 
      tbl_opportunities.public_id as opportunity_org_public_id, 
      tbl_opportunities.fk_org_id as opportunity_org_id, 
      tbl_opportunities.headline as opportunity_headline,
      tbl_organizations.orgname as org_name, 
      tbl_organizations.location as location 
      FROM tbl_matches 
      INNER JOIN tbl_solvers ON tbl_matches.fk_solver_id=tbl_solvers.id 
      INNER JOIN tbl_opportunities ON tbl_matches.fk_opportunity_id=tbl_opportunities.id 
      INNER JOIN tbl_organizations ON tbl_opportunities.fk_org_id=tbl_organizations.id 
      WHERE tbl_matches.fk_opportunity_id=:opportunityid";
      if ($approvedOnly)
        $sql .= " AND tbl_matches.matchmaker_approved = 1";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindValue(':opportunityid', $opportunityId);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    return null;
  }

  public function getSpecificMatchDataById($matchId, $organizations, $users)
  {
    $publicId = $matchId;
    $matchId = $this->getRealId($publicId);
    return $this->getSpecificMatchDataByRealId($matchId, $organizations, $users);
  }

  public function getSpecificMatchDataByRealId($realId, $organizations, $users)
  {
    $matches = $this->getAllMatchData($organizations, $users, $realId);
    if (isset($matches) && is_array($matches))
      return $matches[0];
  }

  public function getAllMatchData($organizations = null, $users = null, $filterId = null)
  {
    $sql = "SELECT 
    tbl_matches.*, 
    tbl_solvers.id as solver_id, 
    tbl_solvers.public_id as solver_public_id,
    tbl_solvers.fk_org_id as solver_org_id, 
    tbl_solvers.headline as solver_headline, 
    tbl_opportunities.id as opportunity_id, 
    tbl_opportunities.public_id as opportunity_public_id,
    tbl_opportunities.fk_org_id as opportunity_org_id, 
    tbl_opportunities.headline as opportunity_headline
    FROM tbl_matches 
    INNER JOIN tbl_solvers ON tbl_matches.fk_solver_id=tbl_solvers.id 
    INNER JOIN tbl_opportunities ON tbl_matches.fk_opportunity_id=tbl_opportunities.id";
    if (isset($filterId) && is_numeric($filterId))
      $sql .= " WHERE tbl_matches.id=" . $filterId;
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
    $allMatches = $stmt->fetchAll(PDO::FETCH_OBJ);
    if (!isset($allMatches))
      return false;
    else {
      if (isset($organizations)) {
        foreach ($allMatches as $thisMatch) {
          $seekerOrg = $organizations->getOrganizationInfoByRealId($thisMatch->opportunity_org_id); //TODO: THESE ARE REAL IDS
          $thisMatch->opportunity_orgname = $seekerOrg->orgname;
          $solverOrg = $organizations->getOrganizationInfoByRealId($thisMatch->solver_org_id);
          $thisMatch->solver_orgname = $solverOrg->orgname;
        }
      }
      if (isset($users)) {
        foreach ($allMatches as $thisMatch) {
          $matchuser = $users->getUserInfoById($thisMatch->matched_by);
          $thisMatch->matched_by_username = $matchuser->username;
        }
      }
      return $allMatches;
    }
  }

  public function getUserRoleInMatch($userId, $matchId, $users, $organizations, $opportunities, $_debugMode = false)
  {
    $userRole = "";
    $matchData = $this->getSpecificMatchDataById($matchId, $organizations, $users);
    if ($_debugMode) {
      print_r($matchData);
      echo "<br>";
    }

    //Are any of the user's organizations the seeker for this match?
    $userOrgs = $organizations->getAllOrganizationDataForUser($userId, $users);
    foreach ($userOrgs as $userOrg) {
      if ($userOrg->id == $matchData->solver_org_id) {
        $userRole .= "solver";
      }
    }
    //Are any of the user's solvers the solver for this match?
    foreach ($userOrgs as $userOrg) {
      $optys = $opportunities->getAllOpportunityDataForOrg($userOrg->public_id, $organizations);
      if ($_debugMode) {
        print_r($optys);
        echo "<br>";
      }
      foreach ($optys as $opty) {
        if ($opty->id == $matchData->fk_opportunity_id) {
          $userRole .= "seeker";
        }
      }
    }
    return $userRole;
  }
  public function approveMatchByAdmin($matchid, $userid)
  {
    if (Session::get('roleid') == 1) {

      $sql = "UPDATE tbl_matches SET matchmaker_approved = :userid WHERE public_id = :matchid";
      $stmt = $this->db->pdo->prepare($sql);
      $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
      $stmt->bindParam(':matchid', $matchid, PDO::PARAM_INT);
      $result=$stmt->execute();
      if($result){
        return createUserMessage("success", "Match status updated!");
      }else{
        return createUserMessage("error", "Match status could not be changed.");
      }
      
    }else{
      return createUserMessage("error","You are not an admin");
    }
  }
  public function actOnMatchById($matchId, $action, $actor)
  {
    $matchId = $this->getRealId($matchId);
    if ($matchId) {
      if ($actor == "admin" && Session::get('roleid') == 1)
        $sql = "UPDATE tbl_matches SET matchmaker_approved = :userid";
      if ($actor != "admin")
        $sql = "UPDATE tbl_matches SET " . $actor . "_match = :userid, " . $actor . "_viewed=current_timestamp()";
      $sql .= " WHERE id = :matchid;";
      $stmt = $this->db->pdo->prepare($sql);
      if ($action == "approve")
        $stmt->bindValue(':userid', Session::get('userid'));
      else
        $stmt->bindValue(':userid', 0);
      $stmt->bindValue(':matchid', $matchId);
      $result = $stmt->execute();
      if (!$result)
        return createUserMessage("error", "Match status could not be changed.");
      else
        return createUserMessage("success", "Match status updated!");
    } else {
      return createUserMessage("error", "Match could not be found!");
    }
    return false;
  }

  public function deleteMatchById($matchId)
  {
    $matchId = $this->getRealId($matchId);
    if ($matchId) {
      if (Session::get('roleid') == 1) {
        $sql = "DELETE FROM tbl_matches WHERE id = :id ";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':id', $matchId);
        $result = $stmt->execute();
        if ($result) {
          return createUserMessage("success", "Match deleted.");
        } else {
          return createUserMessage("error", "Match not deleted. Something went wrong!");
        }
      }
    }
    return createUserMessage("error", "Match not deleted. Could not find real match id!");
  }

  public function isMatchInArray($match, $matchArray)
  {
    foreach ($matchArray as $thisMatch) {
      if ($match->public_id == $thisMatch->public_id)
        return true;
      return false;
    }
  }
}
