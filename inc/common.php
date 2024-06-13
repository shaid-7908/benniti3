<?php
// Format date
function formatDate($date) {
    date_default_timezone_set('UTC');
    $strtime = strtotime($date);
    return date('Y-m-d H:i:s', $strtime);
}

function createUserMessage($type, $msg) {
    switch(strtolower($type)) {
        case "error": {
        $msg = '<div class="alert alert-danger alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" onclick="document.getElementById(\'flash-msg\').style.display=\'none\'" aria-label="close">&times;</a>
        <strong>Error!</strong> ' . $msg . '</div>';
        break;
        }
        default: {
        $msg = '<div class="alert alert-success alert-dismissible mt-3" id="flash-msg">
        <a href="#" class="close" onclick="document.getElementById(\'flash-msg\').style.display=\'none\'" aria-label="close">&times;</a>
        <strong>' . strtoupper($type) . '</strong> ' . $msg . '</div>';
        break;
        }
    }
    return $msg;
}

function checkUserAuth($permission, $typeLevel) {
    //error_log("checking permission " . $permission . " for level " . $typeLevel);
    if (session_status() == PHP_SESSION_ACTIVE) {
        if (Session::get('roleid')) {
            if (array_key_exists($permission, ROLE_PERMISSIONS)) {
                if (!is_numeric(ROLE_PERMISSIONS[$permission]))
                    return ROLE_PERMISSIONS[$permission];
                else {
                    if (isset($typeLevel) && is_numeric($typeLevel) && $typeLevel <= ROLE_PERMISSIONS[$permission])
                        return true;
                }
            } else {
                error_log("Permission being checked does not exist: " . $permission);
            }
        }
    }
    //error_log("returning false!");
    return false;
}

function getIfSet($getInfo, $checkparam) {
    if (!isset($getInfo))
        return "";  
    if (isset($getInfo) && is_object($getInfo) && property_exists($getInfo, $checkparam)) {
        if (isset($getInfo->$checkparam))
            return $getInfo->$checkparam;
        else
            return "";
    }
    if (isset($getInfo) && is_array($getInfo) && array_key_exists($checkparam, $getInfo)) {
        if (isset($getInfo[$checkparam]))
            return $getInfo[$checkparam];
        else
            return "";
    }
    if (error_reporting() < 1)
        error_log("getIfSet could not find '" . $checkparam . "'", 0);
}

function containsBadWords($stringToCheck) {
    global $docRoot;
    $stringToCheck = strtolower($stringToCheck);
    $stringParts = explode(" ", $stringToCheck);
    //Load bad words
    $handle = fopen($docRoot."assets/badwords.txt", 'r');
    $badWords = [];
    while (($buffer = fgets($handle)) !== false) {
        array_push($badWords, trim($buffer));
    }
    fclose($handle);
    $badFound = false;
    foreach ($stringParts as $checkString) {
        if (in_array(trim($checkString), $badWords)) {
            $badFound = true;
            break;
        }
    }
    return $badFound;
}
?>