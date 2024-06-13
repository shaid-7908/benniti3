<?php
include_once $docRoot."lib/Database.php";
include_once $docRoot."lib/Session.php";

class SMProfiles {

  private $db;

  public function __construct() {
    $this->db = new Database();
  }
  public $lastError = "";

  // Skill Lookup
  public function checkSMProfilesExistsByName($smprofileName) {
    $sql = "SELECT id, profile_name from tbl_smprofiles WHERE LOWER(profile_name) = LOWER(:smprofileName)";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':smprofileName', $smprofileName);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($stmt->rowCount()> 0) {
      return $result[0]->id;
    } else {
      return 0;
    }
  }

  // Skill Creation
  public function createSMProfile($smprofileName, $smprofileNSUri, $smprofileMarketplaceId) {
    $retVal = -1;
    $this->lastError = "";
    if (!is_numeric($smprofileMarketplaceId)) {
      //TODO: we should do more checking
      $this->lastError = "Profile not created, Marketplace ID must be numeric!";
    } else {
      $smprofileName = strtolower($smprofileName);
      $smprofileNSUri = strtolower($smprofileNSUri);
  
      $checkProfile = $this->checkSMProfilesExistsByName($smprofileName);
      if ($checkProfile <= 0) {
        if ($smprofileName == "" || $smprofileName == " ") {
          $this->lastError = "SM Profile name cannot be blank";
        } elseif (strlen($skillName) < 3) {
          $this->lastError = "SM Profile name should be at least three characters";
        } else {
          $sql = "INSERT INTO tbl_smprofiles(profile_name, profile_namespace_uri, profile_marketplace_id) VALUES(:smprofileName, :smprofileNSUri, smprofileMarketplaceId)";
          $stmt = $this->db->pdo->prepare($sql);
          $stmt->bindValue(':smprofileName', filter_var ($smprofileName, FILTER_SANITIZE_STRING));
          $stmt->bindValue(':smprofileNSUri', filter_var ($smprofileNSUri, FILTER_SANITIZE_URL));
          $stmt->bindValue(':smprofileName', filter_var ($smprofileName, FILTER_SANITIZE_NUMBER_FLOAT));
          $result = $stmt->execute();
          if ($result) {
            $retVal = $this->db->pdo->lastInsertId();
          } else {
            $this->lastError = "SM Profile not created. Something went wrong!";
          }
        }
      } else {
        $retVal = $checkProfile;
      }
    }
    if ($this->lastError != "")
      error_log("createSMProfile error: " . $this->lastError);
    return $retVal;
  }

  // Get all skills for a given opportunity id
  public function getAllSMProfilesForOpportunityById($opportunityId) {
    $sql = "select smprofiles.*, opportunity_smprofiles.* from tbl_smprofiles smprofiles
    inner join tbl_opportunity_smprofiles opportunity_smprofiles on smprofiles.id = opportunity_smprofiles.fk_profile_id
    where opportunity_smprofiles.fk_opportunity_id = :opptyid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':opptyid', $opportunityId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  // Get all skills for a given solver id
  public function getAllSMProfilesForSolverById($solverid) {
    $sql = "select smprofiles.*, solver_smprofiles.* from tbl_smprofiles smprofiles
    inner join tbl_solver_smprofiles solver_smprofiles on smprofiles.id = solver_smprofiles.fk_profile_id
    where solver_smprofiles.fk_solver_id = :solverid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':solverid', $solverid);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  // Get all users that have a given skill name
  public function getAllSolversWithSMProfile($smprofileName) {
    $smprofileName = strtolower($smprofileName);
    $checkSMProfile = $this->checkSMProfilesExistsByName($smprofileName);
    if ($checkSMProfile > 0) {
      return $this->getAllSolversWithSMProfileById($checkSMProfile);
    } else {
      $this->lastError = "Could not find SM Profiles with that name";
      return null;
    }
  }

  // Get all users that have a given skill id
  public function getAllSolversWithSMProfileById($smprofileId) {
    $sql = "SELECT 
    smprofiles.*, solver_smprofiles.*, users.id as userid, solvers.id as solverid, solvers.headline
    FROM tbl_smprofiles smprofiles
    INNER JOIN tbl_solver_smprofiles solver_smprofiles on smprofiles.id = solver_smprofiles.fk_smprofile_id
    INNER JOIN tbl_users users on users.id = solver_smprofiles.fk_user_id
    INNER JOIN tbl_solver solvers on solvers.fk_user_id = users.id
    WHERE smprofile.id = :smprofileId;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':smprofileId', $smprofileId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  // Get all users that have a given skill name
  public function getAllOpportunitiesWithSMProfile($smprofileName) {
    $smprofileName = strtolower($smprofileName);
    $checkSMProfile = $this->checkSMProfilesExistsByName($smprofileName);
    if ($checkSMProfile > 0) {
      return $this->getAllOpportunitiesWithSMProfileById($checkSMProfile);
    } else {
      $this->lastError = "Could not find SM Profile with that name";
      return null;
    }
  }

  // Get all opportunities that require a given skill
  public function getAllOpportunitiesWithSMProfileById($smprofileId, $onlyActive = true) {
    $sql = "select smprofiles.*, opportunity_smprofiles.*, opportunities.id as opportunityid, opportunities.headline
    from tbl_smprofiles smprofiles
    inner join tbl_opportunity_smprofiles opportunity_smprofiles on smprofiles.id = opportunity_smprofiles.fk_smprofile_id
    inner join tbl_opportunities opportunities on opportunities.id = opportunity_smprofiles.fk_opportunity_id
    where skills.id = :smprofileId;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':smprofileId', $smprofileId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function renameSMProfile($oldName, $newName) {
    $smprofileId = $this->checkSMProfilesExistsByName($oldName);
    if ($smprofileId > 0) {
      return $this->renameSkillById($smprofileId, $newName);
    } else {
      $this->lastError = "Could not find SM Profile to update";
      return 0;
    }
  }

  // Delete Skill by Id (TODO: also delete relationships with skill)
  public function deleteProfileAndRelationshipsById($profileId){
    $sql = "DELETE FROM tbl_smprofiles WHERE id = :id;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $profileId);
    
    $sql = "DELETE FROM tbl_opportunity_smprofiles WHERE fk_smprofile_id = :id;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $profileId);

    $sql = "DELETE FROM tbl_solver_smprofiles WHERE fk_smprofile_id = :id;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $profileId);
  }
}
