<?php
include_once $docRoot."lib/Database.php";
include_once $docRoot."lib/Session.php";
include_once $docRoot."inc/common.php";

class Technologies{
  private $db;

  public function __construct() {
    $this->db = new Database();
  }
  public $lastError = "";


  public function checkTechnologyExists($technologyName) {
    $sql = "SELECT id, technology_name FROM tbl_technology WHERE LOWER(technology_name) = LOWER(:technology_name)";
    $stmt = $this->db->pdo->prepare($sql);
    $stmt->bindValue(':technology_name', $technologyName);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    if ($stmt->rowCount() > 0) {
        return $result[0]->id;
    } else {
        return 0;
    }
}

public function createTechnology($technologyName) {
    $technologyName = strtolower($technologyName);
    $retVal = -1;
    $this->lastError = "";

    // Check if the technology already exists
    $checkTechnology = $this->checkTechnologyExists($technologyName);

    if ($checkTechnology <= 0) {
        // Perform validations
        if ($technologyName == "" || $technologyName == " ") {
            $this->lastError = "Technology name cannot be blank";
        } elseif (strlen($technologyName) < 2) {
            $this->lastError = "Technology name should be at least two characters";
        } else {
            // Insert the technology into the database
            $sql = "INSERT INTO tbl_technology(technology_name) VALUES(:technologyName)";
            $stmt = $this->db->pdo->prepare($sql);
            $stmt->bindValue(':technologyName', filter_var($technologyName, FILTER_SANITIZE_STRING));
            $result = $stmt->execute();

            if ($result) {
                $retVal = $this->db->pdo->lastInsertId();
            } else {
                $this->lastError = "Technology not created. Something went wrong!";
            }
        }
    } else {
        // If the technology already exists, return its ID
        $retVal = $checkTechnology;
    }

    if ($this->lastError != "") {
        error_log("createTechnology error: " . $this->lastError);
    }

    return $retVal;
}
public function getAllTecnology(){
    $sql = "SELECT * FROM tbl_technology;";
    $stmt=$this->db->pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $result;

}

}