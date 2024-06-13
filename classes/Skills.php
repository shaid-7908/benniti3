<?php
include_once $docRoot."lib/Database.php";
include_once $docRoot."lib/Session.php";
include_once $docRoot."inc/common.php";

class Skills {

  private $db;

  public function __construct() {
    $this->db = new Database();
  }
  public $lastError = "";

  public function getRealId($publicId) {
    if (!isset($publicId) || $publicId == "") {
      error_log("Could not find real id for opportunity when public id not set");
      return false;
    }
    $sql = "SELECT id FROM tbl_opportunities WHERE public_id = :publicid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':publicid', $publicId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($result) {
      return $result[0]->id;
    } else {
      error_log("Could not find real id for opportunity with public id " . $publicId);
      return false;
    }
  }

  // Skill Lookup
  public function checkSkillExists($skillName) {
    $sql = "SELECT id, skill_name from tbl_skills WHERE LOWER(skill_name) = LOWER(:skillname)";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':skillname', $skillName);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($stmt->rowCount()> 0) {
      return $result[0]->id;
    } else {
      return 0;
    }
  }

  // Skill Creation
  public function createSkill($skillName) {
    $skillName = strtolower($skillName);
    $retVal = -1;
    $this->lastError = "";
    $checkSkill = $this->checkSkillExists($skillName);
    if ($checkSkill <= 0) {
      if ($skillName == "" || $skillName == " ") {
        $this->lastError = "Skill cannot be blank";
      } elseif (strlen($skillName) < 2) {
        $this->lastError = "Skill should be at least two characters";
      } elseif (containsBadWords($skillName)) {
        $this->lastError = "Skill should not include foul language";
      } else {
        $sql = "INSERT INTO tbl_skills(skill_name) VALUES(:skillname)";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':skillname', filter_var ($skillName, FILTER_SANITIZE_STRING));
        $result = $stmt->execute();
        if ($result) {
          $retVal = $this->db->pdo->lastInsertId();
        } else {
          $this->lastError = "Skill not created. Something went wrong!";
        }
      }
    } else {
      $retVal = $checkSkill;
    }
    if ($this->lastError != "")
      error_log("createSkill error: " . $this->lastError);
    return $retVal;
  }

  // Get all skills for a given opportunity id
  public function getAllSkillsForOpportunityById($opportunityId) {
    $sql = "select skills.*, opportunity_skills.* from tbl_skills skills
    inner join tbl_opportunity_skills opportunity_skills on skills.id = opportunity_skills.fk_skill_id
    where opportunity_skills.fk_opportunity_id = :opptyid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':opptyid', $opportunityId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  // Get all skills for a given solver id
  public function getAllSkillsForSolverById($solverid) {
    $sql = "select skills.*, solver_skills.* from tbl_skills skills
    inner join tbl_solver_skills solver_skills on skills.id = solver_skills.fk_skill_id
    where solver_skills.fk_solver_id = :solverid";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':solverid', $solverid);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  // Get all users that have a given skill name
  public function getAllSolversWithSkill($skillName) {
    $skillName = strtolower($skillName);
    $checkSkill = $this->checkSkillExists($skillName);
    if ($checkSkill > 0) {
      return $this->getAllSolversWithSkillById($checkSkill);
    } else {
      $this->lastError = "Could not find skill with that name";
      return null;
    }
  }

  // Get all users that have a given skill id
  public function getAllSolversWithSkillById($skillId) {
    $sql = "SELECT 
    skills.*, solver_skills.*, users.id as userid, solvers.id as solverid, solvers.headline
    FROM tbl_skills skills
    INNER JOIN tbl_solver_skills solver_skills on skills.id = solver_skills.fk_skill_id
    INNER JOIN tbl_users users on users.id = solver_skills.fk_user_id
    INNER JOIN tbl_solver solvers on solvers.fk_user_id = users.id
    WHERE skills.id = :skillid;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':skillid', $skillId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  // Get all users that have a given skill name
  public function getAllOpportunitiesWithSkill($skillName) {
    $skillName = strtolower($skillName);
    $checkSkill = $this->checkSkillExists($skillName);
    if ($checkSkill > 0) {
      return $this->getAllOpportunitiesWithSkillById($checkSkill);
    } else {
      $this->lastError = "Could not find skill with that name";
      return null;
    }
  }

  // Get all opportunities that require a given skill
  public function getAllOpportunitiesWithSkillById($skillId, $onlyActive = true) {
    $sql = "select skills.*, opportunity_skills.*, opportunities.id as opportunityid, opportunities.headline
    from tbl_skills skills
    inner join tbl_opportunity_skills opportunity_skills on skills.id = opportunity_skills.fk_skill_id
    inner join tbl_opportunities opportunities on opportunities.id = opportunity_skills.fk_opportunity_id
    where skills.id = :skillid;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':skillid', $skillId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function renameSkill($oldName, $newName) {
    $skillId = $this->checkSkillExists($oldName);
    if ($skillId > 0) {
      return $this->renameSkillById($skillId, $newName);
    } else {
      $this->lastError = "Could not find skill to update";
      return 0;
    }
  }

  // Update Skill by Id
  public function renameSkillById($skillId, $newName){
    if (is_numeric($skillId) && $skillId > 0) {
      if ($newName == "" || $newName == " ") {
        $this->lastError = "Skill cannot be blank";
        return -1;
      } elseif (strlen($newName) < 2) {
        $this->lastError = "Skill should be at least two characters";
        return -1;
      } else {
        $sql = "UPDATE tbl_skillls
          SET skill_name = :skillname
          WHERE id = :skillid;";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':skillname', filter_var ($newName, FILTER_SANITIZE_STRING));
        $stmt->bindValue(':skillid', $skillId);
        $result = $stmt->execute();
        if ($result) {
          return $skillId;
        } else {
          $this->lastError = "Could not update skill";
          return -1;
        }
      }
    }
  }

  // Delete Skill by Id (TODO: also delete relationships with skill)
  public function deleteSkillAndRelationshipsById($skillId){
    $sql = "DELETE FROM tbl_skills WHERE id = :id;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $skillId);
    
    $sql = "DELETE FROM tbl_opportunity_skills WHERE fk_skill_id = :id;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $skillId);

    $sql = "DELETE FROM tbl_solver_skills WHERE fk_skill_id = :id;";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':id', $skillId);
  }
}
