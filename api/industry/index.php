<?php
$docRoot = "../../";
include_once $docRoot."config/config.php";
include_once $docRoot."lib/Session.php";
Session::init();
include_once $docRoot."classes/Industries.php";

$industries = new Industries();
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
    if (isset($_POST["query"]) && $_POST["query"] == "industry") {
        if (isset($_POST['value']) && $_POST['value'] != "") {
            $industryId = $industries->checkIndustryExists(strtolower($_POST['value']));
            die ('{"id":"' . $industryId . '"}');
        }
    }
    if (isset($_POST['create']) && $_POST['create'] == "industry") {
        if (isset($_POST['value']) && $_POST['value'] != "") {
            $industryId = $industries->createIndustry(strtolower($_POST['value']));
            die ('{"id":"' . $industryId . '"}');
        }
    }
    die('{"error":"invalid query"}');
}
?>