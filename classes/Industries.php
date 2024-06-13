<?php
include_once $docRoot . "lib/Database.php";
include_once $docRoot . "lib/Session.php";
include_once $docRoot . "inc/common.php";

class Industries
{

  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }
  public $lastError = "";

  public function checkIndustryExists($industryname)
  {
    $sql = "SELECT id,industry_name from tbl_industry WHERE LOWER(industry_name) = LOWER(:industry_name)";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':industry_name', $industryname);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($stmt->rowCount() > 0) {
      return $result[0]->id;
    } else {
      return 0;
    }
  }


  public function getAllIndustryName(){
    $sql = "SELECT * FROM tbl_industry";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->execute();
   $result = $stmt->fetchAll(PDO::FETCH_OBJ);
   return $result;
  }
  public function createIndustry($industryName)
  {
    $industryName = strtolower($industryName);
    $retVal = -1;
    $this->lastError = "";

    // Check if the industry already exists
    $checkIndustry = $this->checkIndustryExists($industryName);

    if ($checkIndustry <= 0) {
      // Perform validations
      if ($industryName == "" || $industryName == " ") {
        $this->lastError = "Industry name cannot be blank";
      } elseif (strlen($industryName) < 2) {
        $this->lastError = "Industry name should be at least two characters";
      } else {
        // Insert the industry into the database
        $sql = "INSERT INTO tbl_industry(industry_name) VALUES(:industryName)";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':industryName', filter_var($industryName, FILTER_SANITIZE_STRING));
        $result = $stmt->execute();

        if ($result) {
          $retVal = $this->db->pdo->lastInsertId();
        } else {
          $this->lastError = "Industry not created. Something went wrong!";
        }
      }
    } else {
      // If the industry already exists, return its ID
      $retVal = $checkIndustry;
    }

    if ($this->lastError != "") {
      error_log("createIndustry error: " . $this->lastError);
    }

    return $retVal;
  }

  public function getIndustryForSolverById($solverid, $solvers)
  {
    $realSolverid = $solvers->getRealId($solverid);
    $sql = "SELECT tbl_industry.id AS industry_id, tbl_industry.industry_name, tbl_solver_industry.fk_solver_id, tbl_solver_industry.fk_industry_id
             FROM tbl_industry
             INNER JOIN tbl_solver_industry ON tbl_industry.id = tbl_solver_industry.fk_industry_id
             WHERE tbl_solver_industry.fk_solver_id = :solverid;
             ";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt -> bindValue(':solverid',$realSolverid);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $result;
  }
}
