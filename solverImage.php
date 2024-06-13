<?php
header("Content-Type: image/png");
$docRoot = "";
include_once $docRoot."config/config.php";
require_once $docRoot."vendor/autoload.php";

$type="portrait";
if (isset($_GET['type']))
    $type= strtolower($_GET['type']);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    spl_autoload_register(function($classes){
        global $docRoot;
        include $docRoot.'classes/'.$classes.".php";
    });
    $solvers = new Solvers();
    $imgData = $solvers->getSolverImageById($_GET['id'], $type, $raw=true);
    if (isset($imgData)) {
        echo $imgData;
        exit;
    }
}
$name = './assets/Bennit' . ucfirst($type) . '.png';
$fp = fopen($name, 'rb');

// send the right headers
header("Content-Type: image/png");
header("Content-Length: " . filesize($name));

// dump the picture and stop the script
fpassthru($fp);
exit;
?>