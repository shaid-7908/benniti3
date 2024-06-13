<?php
include_once $docRoot."lib/Database.php";
include_once $docRoot."lib/Session.php";
include_once $docRoot."inc/common.php";

class Specialities {
    private $db;
    public $lastError = "";

    public function __construct() {
        $this->db = new Database();
    }

    public function checkSpecialityExists($specialtyName) {
        $sql = "SELECT id, speciality_name FROM tbl_speciality WHERE LOWER(speciality_name) = LOWER(:speciality_name)";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->bindValue(':speciality_name', $specialtyName);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        if ($stmt->rowCount() > 0) {
            return $result[0]->id;
        } else {
            return 0;
        }
    }

    public function createSpeciality($specialtyName) {
        $specialtyName = strtolower($specialtyName);
        $retVal = -1;
        $this->lastError = "";

        // Check if the specialty already exists
        $checkSpecialty = $this->checkSpecialityExists($specialtyName);

        if ($checkSpecialty <= 0) {
            // Perform validations
            if ($specialtyName == "" || $specialtyName == " ") {
                $this->lastError = "Specialty name cannot be blank";
            } elseif (strlen($specialtyName) < 2) {
                $this->lastError = "Specialty name should be at least two characters";
            } else {
                // Insert the specialty into the database
                $sql = "INSERT INTO tbl_speciality(speciality_name) VALUES(:specialityName)";
                $stmt = $this->db->pdo->prepare($sql);
                $stmt->bindValue(':specialityName', filter_var($specialtyName, FILTER_SANITIZE_STRING));
                $result = $stmt->execute();

                if ($result) {
                    $retVal = $this->db->pdo->lastInsertId();
                } else {
                    $this->lastError = "Specialty not created. Something went wrong!";
                }
            }
        } else {
            // If the specialty already exists, return its ID
            $retVal = $checkSpecialty;
        }

        if ($this->lastError != "") {
            error_log("createSpecialty error: " . $this->lastError);
        }

        return $retVal;
    }
    public function getAllSpecialty(){
        $sql = "SELECT * FROM tbl_speciality;";
        $stmt = $this->db->pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $result;
    }
}
?>
