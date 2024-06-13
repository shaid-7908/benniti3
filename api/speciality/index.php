<?php
$docRoot = "../../";
include_once $docRoot."config/config.php";
include_once $docRoot."lib/Session.php";
Session::init();
include_once $docRoot."classes/Specialities.php"; // Assuming Specialties.php contains the necessary class for managing specialties

$specialties = new Specialities(); // Instantiate the Specialties class
$partnerKey = "";
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
    if (isset($_POST["query"]) && $_POST["query"] == "speciality") {
        if (isset($_POST['value']) && $_POST['value'] != "") {
            $specialtyId = $specialties->checkSpecialityExists(strtolower($_POST['value']));
            die ('{"id":"' . $specialtyId . '"}');
        }
    }
    if (isset($_POST['create']) && $_POST['create'] == "speciality") {
        if (isset($_POST['value']) && $_POST['value'] != "") {
            $specialtyId = $specialties->createSpeciality(strtolower($_POST['value']));
            die ('{"id":"' . $specialtyId . '"}');
        }
    }
    die('{"error":"invalid query"}');
}
?>
