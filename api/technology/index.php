<?php
$docRoot = "../../";
include_once $docRoot."config/config.php";
include_once $docRoot."lib/Session.php";
Session::init();
include_once $docRoot."classes/Technologies.php"; //Technologies.php contains the necessary class for managing technologies

$technologies = new Technologies(); // Instantiate the Technologies class
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
    if (isset($_POST["query"]) && $_POST["query"] == "technology") {
        if (isset($_POST['value']) && $_POST['value'] != "") {
            $technologyId = $technologies->checkTechnologyExists(strtolower($_POST['value']));
            die ('{"id":"' . $technologyId . '"}');
        }
    }
    if (isset($_POST['create']) && $_POST['create'] == "technology") {
        if (isset($_POST['value']) && $_POST['value'] != "") {
            $technologyId = $technologies->createTechnology(strtolower($_POST['value']));
            die ('{"id":"' . $technologyId . '"}');
        }
    }
    die('{"error":"invalid query"}');
}
?>
