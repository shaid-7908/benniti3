<?php
$docRoot = "../../";
include_once $docRoot."config/config.php";
include_once $docRoot."lib/Session.php";
Session::init();
include_once $docRoot."classes/Skills.php";
$skills = new Skills();
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
    if (isset($_POST["query"]) && $_POST["query"] == "skill") {
        if (isset($_POST['value']) && $_POST['value'] != "") {
            $skillId = $skills->checkSkillExists(strtolower($_POST['value']));
            die ('{"id":"' . $skillId . '"}');
        }
    }
    if (isset($_POST['create']) && $_POST['create'] == "skill") {
        if (isset($_POST['value']) && $_POST['value'] != "") {
            $skillId = $skills->createSkill(strtolower($_POST['value']));
            die ('{"id":"' . $skillId . '"}');
        }
    }
    die('{"error":"invalid query"}');
}
?>